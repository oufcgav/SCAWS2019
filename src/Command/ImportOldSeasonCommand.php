<?php

namespace App\Command;

use App\Entity\Goal;
use App\Entity\GoalTimes;
use App\Entity\Match;
use App\Entity\Pint;
use App\Entity\Prediction;
use App\Entity\Score;
use App\Repository\SeasonList;
use App\Service\ScoreCalculator;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportOldSeasonCommand extends Command
{
    protected static $defaultName = 'scaws:import-season';

    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var SeasonList
     */
    private $seasonList;
    /**
     * @var Match[]
     */
    private $oldMatches = [];
    /**
     * @var Prediction[]
     */
    private $oldPredictions = [];
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        EntityManagerInterface $em,
        LoggerInterface $logger,
        SeasonList $seasonList,
        string $name = null
    ) {
        parent::__construct($name);
        $this->em = $em;
        $this->seasonList = $seasonList;
        $this->logger = $logger;
    }

    protected function configure()
    {
        $this->addArgument('season', InputArgument::REQUIRED, 'Season to import from');
    }

    public function run(InputInterface $input, OutputInterface $output)
    {
        $seasonYear = (int) $input->getArgument('season');

        $seasonDate = new DateTimeImmutable('December '.$seasonYear);
        $season = $this->seasonList->findCurrentSeason($seasonDate);
        if (!$season) {
            $output->writeln('Cannot find old season '.$seasonYear);

            return 1;
        }

        $this->import('matches', $seasonYear, function ($oldMatch) use ($season) {
            $match = (new Match())
                ->setOpponent($oldMatch['opposition'])
                ->setLocation($oldMatch['location'] ?? 'unknown')
                ->setDate(new DateTimeImmutable($oldMatch['match_date']))
                ->setCompetition($oldMatch['type'] ?? 'unknown')
                ->setSeason($season)
            ;
            $this->em->persist($match);
            $this->oldMatches[(int) $oldMatch['id']] = $match;
        });

        $this->import('predictions', $seasonYear, function ($oldPrediction) {
            $match = $this->oldMatches[(int) $oldPrediction['match_id']];
            $prediction = (new Prediction())
                ->setMatch($match)
                ->setPoints($oldPrediction['points'])
                ->setAtMatch($oldPrediction['presence'] ?? false)
                ->setUser($oldPrediction['human'])
                ->setTime($oldPrediction['time'])
                ->setPosition($oldPrediction['position'])
                ->setNiceTime($oldPrediction['nice_time'])
            ;
            $this->em->persist($prediction);
            $this->oldPredictions[(int) $oldPrediction['id']] = $prediction;
        });

        $this->import('goals', $seasonYear, function ($oldGoal) {
            $match = $this->oldMatches[(int) $oldGoal['match_id']];
            $goal = (new Goal())
                ->setMatch($match)
                ->setTiming($oldGoal['time'])
            ;
            if (isset($oldGoal['scorer'])) {
                $goal->setScorer($oldGoal['scorer']);
            } else {
                $goal->setPosition($oldGoal['position']);
            }
            $this->em->persist($goal);
        });

        $this->import('pints', $seasonYear, function ($oldPint) {
            $match = $this->oldMatches[(int) $oldPint['match_id']];
            $pint = (new Pint())
                ->setMatch($match)
                ->setUser($oldPint['human'])
            ;
            for ($p = 1; $p <= (int) $oldPint['number']; $p++) {
                $pint->addPintDrunk();
            }
            $this->em->persist($pint);
        });

        if ($seasonYear > 2015) {
            $this->import('scores', $seasonYear, function ($oldScore) {
                $prediction = $this->oldPredictions[(int) $oldScore['prediction_id']];
                $match = $prediction->getMatch();
                $goals = $match->getGoals();
                $this->logger->debug('Importing score', ['score' => $oldScore['id'], 'match' => $match->getId(), 'goals' => count($goals)]);
                switch ((int) $oldScore['reason']) {
                    case ScoreCalculator::CORRECT_POSITION:
                        $possibleGoals = array_filter($goals, function ($goal) use ($oldScore, $prediction) {
                            $previousScores = array_filter($goal->getScores(), function (Score $score) use ($oldScore) {
                                return $score->getPrediction()->getId() === (int) $oldScore['prediction_id'] && $score->getReason() === ScoreCalculator::CORRECT_POSITION;
                            });
                            if (!empty($previousScores)) {
                                return false;
                            }

                            return $goal->getPosition() === $prediction->getPosition();
                        });
                        break;
                    case ScoreCalculator::BONUS_POINT:
                        $possibleGoals = array_filter($goals, function ($goal) use ($oldScore) {
                            $previousScores = array_filter($goal->getScores(), function (Score $score) use ($oldScore) {
                                return $score->getPrediction()->getId() === (int) $oldScore['prediction_id'] && $score->getReason() === ScoreCalculator::BONUS_POINT;
                            });

                            return empty($previousScores);
                        });
                        break;
                    case ScoreCalculator::CORRECT_TIME:
                        $possibleGoals = array_filter($goals, function ($goal) use ($oldScore, $prediction) {
                            $previousScores = array_filter($goal->getScores(), function (Score $score) use ($oldScore) {
                                return $score->getPrediction()->getId() === (int) $oldScore['prediction_id'] && $score->getReason() === ScoreCalculator::CORRECT_TIME;
                            });
                            if (!empty($previousScores)) {
                                return false;
                            }

                            return $goal->getTiming() === $prediction->getTime();
                        });
                        break;
                    case ScoreCalculator::CORRECT_HALF:
                        $possibleGoals = array_filter($goals, function ($goal) use ($oldScore, $prediction) {
                            $previousScores = array_filter($goal->getScores(), function (Score $score) use ($oldScore) {
                                return $score->getPrediction()->getId() === (int) $oldScore['prediction_id'] && $score->getReason() === ScoreCalculator::CORRECT_HALF;
                            });
                            if (!empty($previousScores)) {
                                return false;
                            }

                            return GoalTimes::matchesHalf($goal, $prediction);
                        });
                        break;
                    default:
                        throw new RuntimeException('Unknown score reason '.json_encode($oldScore));
                }
                if (empty($possibleGoals)) {
                    throw new RuntimeException('No goals found '.json_encode($oldScore));
                }
                $goal = array_shift($possibleGoals);
                $score = (new Score())
                    ->setPoints((float) $oldScore['points'])
                    ->setGoal($goal)
                    ->setPrediction($prediction)
                    ->setReason((int) $oldScore['reason']);
                $this->em->persist($score);
            });
        }

        return 0;
    }

    private function import($entityName, $seasonYear, callable $import)
    {
        $sql = "
            SELECT *
            FROM ${entityName}_${seasonYear}
        ";
        $oldEntities = $this->em->getConnection()->fetchAllAssociative($sql);
        foreach ($oldEntities as $entity) {
            $import($entity);
        }
        $this->em->flush();
    }
}
