<?php

namespace App\Tests\Unit;

use App\Entity\Goal;
use App\Entity\Match;
use App\Entity\Prediction;
use App\Entity\Score;
use App\Service\ScoreCalculator;
use PHPUnit\Framework\TestCase;

class ScoreCalculatorTest extends TestCase
{

    public function testReturnsNoScoresIfNoPredictionMatches()
    {
        $calc = new ScoreCalculator();
        $goal = (new Goal())
            ->setMatch($this->createMatch())
            ->setScorer('Matty Taylor (S)')
            ->setTiming('First half');
        $this->assertEmpty($calc->calculate($goal, []));
    }

    /**
     * @dataProvider dataForCorrectPosition
     */
    public function testReturnsCorrectPointsIfPredictionMatchesPosition($scorer, $predictedPosition, $expectedPoints)
    {
        $calc = new ScoreCalculator();
        $match = $this->createMatch();
        $goal = (new Goal())
            ->setMatch($match)
            ->setScorer($scorer)
            ->setTiming('First half');
        $prediction = $this->createPrediction($predictedPosition, 'Second half', $match);
        $scores = $calc->calculate($goal, [$prediction]);
        $this->assertGreaterThanOrEqual(1, count($scores));
        $score = array_pop($scores);
        $this->assertEquals($expectedPoints, $score->getPoints());
    }

    public function dataForCorrectPosition()
    {
        return [
            'Strikers score 1 point' => ['Matty Taylor (S)', 'Strikers', 1],
            'Midfielders score 2 points' => ['Cameron Brannagan (M)', 'Midfielders', 2],
            'Defenders score 3 points' => ['Joshua Ruffels (D)', 'Defenders', 3],
        ];
    }

    /**
     * @dataProvider dataForTimePeriods
     */
    public function testReturnsCorrectPointsIfPredictionMatchesCorrectTimePeriod($timeScored, $timePredicted, $expectedPoints)
    {
        $calc = new ScoreCalculator();
        $match = $this->createMatch();
        $goal = (new Goal())
            ->setMatch($match)
            ->setScorer('Joshua Ruffels (D)')
            ->setTiming($timeScored);
        $prediction = $this->createPrediction('Strikers', $timePredicted, $match);
        $scores = $calc->calculate($goal, [$prediction]);
        $this->assertGreaterThanOrEqual(1, count($scores));
        $score = array_pop($scores);
        $this->assertEquals($expectedPoints, $score->getPoints());
    }

    public function dataForTimePeriods()
    {
        return [
            'First half scores 1 point' => ['16-30 mins', 'First half', 1],
            'Second half scores 1 point' => ['46-60 mins', 'Second half', 1],
            'Correct 15 min period scores 3 points' => ['16-30 mins', '16-30 mins', 3],
            'Stoppage time scores 4.5 points' => ['Stoppage time', 'Stoppage time', 4.5],
        ];
    }

    public function testReturnsTwoScoresIfBothScoreAndTimeMatch()
    {
        $calc = new ScoreCalculator();
        $match = $this->createMatch();
        $goal = (new Goal())
            ->setMatch($match)
            ->setScorer('Joshua Ruffels (D)')
            ->setTiming('31-45 mins');
        $prediction = $this->createPrediction('Defenders', 'First half', $match);
        $scores = $calc->calculate($goal, [$prediction]);
        $this->assertGreaterThanOrEqual(2, count($scores));
    }

    /**
     * @dataProvider dataForBonusPoint
     */
    public function testReturnsBonusPointWhenAppropriate($matchQualifies, $userIsPresent, $userHasAlreadyScored, $expectedBonusPoints)
    {
        $calc = new ScoreCalculator();
        $match = $this->createMatch($matchQualifies);
        $goal = (new Goal())
            ->setMatch($match)
            ->setScorer('Joshua Ruffels (D)')
            ->setTiming('31-45 mins');
        $prediction = $this->createPrediction('Defenders', 'First half', $match, 1, $userIsPresent);
        if ($userHasAlreadyScored) {
            $oldScore = (new Score())
                ->setPoints(ScoreCalculator::POINTS_STRIKERS)
                ->setReason(ScoreCalculator::CORRECT_POSITION)
                ->setPrediction($prediction)
            ;
        }
        $scores = $calc->calculate($goal, [$prediction]);
        $bonusPoints = array_filter($scores, function (Score $score) {
            return $score->getReason() === ScoreCalculator::BONUS_POINT;
        });
        $this->assertGreaterThanOrEqual($expectedBonusPoints, count($bonusPoints));
    }

    public function dataForBonusPoint()
    {
        return [
            'User is at qualifying match' => [true, true, false, 1],
            'User is not at qualifying match' => [true, false, false, 0],
            'User is at non-qualifying match' => [false, true, false, 0],
            'User is at qualifying match but has already scored' => [true, true, true, 0],
        ];
    }

    public function testReturnScoresForMultiplePredictions()
    {
        $calc = new ScoreCalculator();
        $match = $this->createMatch(true);
        $goal = (new Goal())
            ->setMatch($match)
            ->setScorer('Joshua Ruffels (D)')
            ->setTiming('31-45 mins');
        $first = $this->createPrediction('Defenders', 'First half', $match, 1, true);
        $second = $this->createPrediction('Defenders', '31-45 mins', $match, 2, true);
        $oldScore = (new Score())
            ->setPoints(ScoreCalculator::POINTS_STRIKERS)
            ->setReason(ScoreCalculator::CORRECT_POSITION)
            ->setPrediction($second)
        ;
        $third = $this->createPrediction('Strikers', '1-15 mins', $match, 3, true);
        $scores = $calc->calculate($goal, [$first, $second, $third]);
        $firstScores = array_filter($scores, function (Score $score) use ($first) {
            return $score->getPrediction()->getUser() === $first->getUser();
        });
        $secondScores = array_filter($scores, function (Score $score) use ($second) {
            return $score->getPrediction()->getUser() === $second->getUser();
        });
        $thirdScores = array_filter($scores, function (Score $score) use ($third) {
            return $score->getPrediction()->getUser() === $third->getUser();
        });
        $this->assertEquals(3, count($firstScores));
        $this->assertEquals(2, count($secondScores));
        $this->assertEquals(0, count($thirdScores));
    }

    public function testUpdatesPredictionWithNewScore()
    {
        $calc = new ScoreCalculator();
        $match = $this->createMatch();
        $goal = (new Goal())
            ->setMatch($match)
            ->setScorer('Joshua Ruffels (D)')
            ->setTiming('31-45 mins');
        $prediction = $this->createPrediction('Defenders', 'First half', $match, 1, true);
        $prediction->setPoints(2);
        $calc->calculate($goal, [$prediction]);
        $this->assertGreaterThan(2, $prediction->getPoints());
    }

    private function createMatch($bonusPoints = false)
    {
        return (new Match())
            ->setOpponent('Unit Test FC')
            ->setDate(new \DateTimeImmutable())
            ->setLocation('Home')
            ->setCompetition($bonusPoints ? 'Cup/Other' : 'League')
        ;
    }

    private function createPrediction($position, $timing, $match, $user = 1, $atMatch = false): Prediction
    {
        return (new Prediction())
            ->setMatch($match)
            ->setAtMatch($atMatch)
            ->setTime($timing)
            ->setUser($user)
            ->setPosition($position)
            ;
    }
}
