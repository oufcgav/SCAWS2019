<?php

namespace App\Tests\Unit;

use App\Entity\Goal;
use App\Entity\GoalTimes;
use App\Entity\Match;
use App\Entity\Positions;
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
            ->setTiming(GoalTimes::FIRST_HALF()->getValue());
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
            ->setTiming(GoalTimes::FIRST_HALF()->getValue());
        $prediction = $this->createPrediction($predictedPosition, GoalTimes::SECOND_HALF()->getValue(), $match);
        $scores = $calc->calculate($goal, [$prediction]);
        $this->assertGreaterThanOrEqual(1, count($scores));
        $score = array_pop($scores);
        $this->assertEquals($expectedPoints, $score->getPoints());
    }

    public function dataForCorrectPosition()
    {
        return [
            'Strikers score 1 point' => ['Matty Taylor (S)', Positions::STRIKERS()->getValue(), 1],
            'Midfielders score 2 points' => ['Cameron Brannagan (M)', Positions::MIDFIELDERS()->getValue(), 2],
            'Defenders score 3 points' => ['Joshua Ruffels (D)', Positions::DEFENDERS()->getValue(), 3],
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
        $prediction = $this->createPrediction(Positions::STRIKERS()->getValue(), $timePredicted, $match);
        $scores = $calc->calculate($goal, [$prediction]);
        $this->assertGreaterThanOrEqual(1, count($scores));
        $score = array_pop($scores);
        $this->assertEquals($expectedPoints, $score->getPoints());
    }

    public function dataForTimePeriods()
    {
        return [
            'First half scores 1 point' => [GoalTimes::SECOND_FIFTEEN()->getValue(), GoalTimes::FIRST_HALF()->getValue(), 1],
            'Second half scores 1 point' => [GoalTimes::FOURTH_FIFTEEN()->getValue(), GoalTimes::SECOND_HALF()->getValue(), 1],
            'Correct 15 min period scores 3 points' => [GoalTimes::SECOND_FIFTEEN()->getValue(), GoalTimes::SECOND_FIFTEEN()->getValue(), 3],
            'Stoppage time scores 4.5 points' => [GoalTimes::STOPPAGE_TIME()->getValue(), GoalTimes::STOPPAGE_TIME()->getValue(), 4.5],
        ];
    }

    public function testReturnsTwoScoresIfBothScoreAndTimeMatch()
    {
        $calc = new ScoreCalculator();
        $match = $this->createMatch();
        $goal = (new Goal())
            ->setMatch($match)
            ->setScorer('Joshua Ruffels (D)')
            ->setTiming(GoalTimes::THIRD_FIFTEEN()->getValue());
        $prediction = $this->createPrediction(Positions::DEFENDERS()->getValue(), GoalTimes::FIRST_HALF()->getValue(), $match);
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
            ->setTiming(GoalTimes::THIRD_FIFTEEN()->getValue());
        $prediction = $this->createPrediction(Positions::DEFENDERS()->getValue(), GoalTimes::FIRST_HALF()->getValue(), $match, 1, $userIsPresent);
        if ($userHasAlreadyScored) {
            (new Score())
                ->setPoints(ScoreCalculator::POINTS_STRIKERS)
                ->setReason(ScoreCalculator::CORRECT_POSITION)
                ->setPrediction($prediction)
            ;
        }
        $scores = $calc->calculate($goal, [$prediction]);
        $bonusPoints = array_filter($scores, function (Score $score) {
            return $score->getReason() === ScoreCalculator::BONUS_POINT;
        });
        $this->assertEquals($expectedBonusPoints, count($bonusPoints));
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
            ->setTiming(GoalTimes::THIRD_FIFTEEN()->getValue());
        $first = $this->createPrediction(Positions::DEFENDERS()->getValue(), GoalTimes::FIRST_HALF()->getValue(), $match, 1, true);
        $second = $this->createPrediction(Positions::DEFENDERS()->getValue(), GoalTimes::THIRD_FIFTEEN()->getValue(), $match, 2, true);
        (new Score())
            ->setPoints(ScoreCalculator::POINTS_STRIKERS)
            ->setReason(ScoreCalculator::CORRECT_POSITION)
            ->setPrediction($second)
        ;
        $third = $this->createPrediction(Positions::STRIKERS()->getValue(), GoalTimes::FIRST_FIFTEEN()->getValue(), $match, 3, true);
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
            ->setTiming(GoalTimes::THIRD_FIFTEEN()->getValue());
        $prediction = $this->createPrediction(Positions::DEFENDERS()->getValue(), GoalTimes::FIRST_HALF()->getValue(), $match, 1, true);
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
