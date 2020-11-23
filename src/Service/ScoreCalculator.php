<?php

namespace App\Service;

use App\Entity\Goal;
use App\Entity\GoalTimes;
use App\Entity\Positions;
use App\Entity\Prediction;
use App\Entity\Score;

class ScoreCalculator
{
    const CORRECT_HALF = 1;
    const CORRECT_TIME = 2;
    const CORRECT_POSITION = 3;
    const BONUS_POINT = 4;
    const LEGACY = 5;

    const POINTS_STRIKERS = 1;
    const POINTS_MIDFIELDERS = 2;
    const POINTS_DEFENDERS = 3;
    const POINTS_HALF = 1;
    const POINTS_TIME = 3;
    const POINTS_STOPPAGE_BONUS = 1.5;
    const POINTS_BONUS = 1;

    /**
     * @param Prediction[] $predictions
     * @param bool         $bonusPointsAvailable
     *
     * @return Score[]
     */
    public function calculate(Goal $goal, array $predictions): array
    {
        $scores = [];
        if (empty($predictions)) {
            return [];
        }

        foreach ($predictions as $prediction) {
            $predictionScores = [];
            $bonusPointsAvailable = $prediction->getAtMatch() && $prediction->getMatch()->qualifiesForBonusPoint();
            $predictionHasAlreadyScored = $prediction->hasScored();
            if ($goal->getPosition() === $prediction->getPosition()) {
                switch ($goal->getPosition()) {
                    case Positions::DEFENDERS()->getValue():
                        $points = self::POINTS_DEFENDERS;
                        break;
                    case Positions::MIDFIELDERS()->getValue():
                        $points = self::POINTS_MIDFIELDERS;
                        break;
                    case Positions::STRIKERS()->getValue():
                    default:
                        $points = self::POINTS_STRIKERS;
                        break;
                }
                $score = (new Score())
                    ->setGoal($goal)
                    ->setPrediction($prediction)
                    ->setReason(self::CORRECT_POSITION)
                    ->setPoints($points);
                $predictionScores[] = $score;
            }
            $points = 0;
            if ($goal->getTiming() === $prediction->getTime()) {
                $points = self::POINTS_TIME;
                if ($goal->getTiming() === GoalTimes::STOPPAGE_TIME()->getValue()) {
                    $points += self::POINTS_STOPPAGE_BONUS;
                }
            }
            switch ($goal->getTiming()) {
                case GoalTimes::FIRST_FIFTEEN()->getValue():
                case GoalTimes::SECOND_FIFTEEN()->getValue():
                case GoalTimes::THIRD_FIFTEEN()->getValue():
                    if ($prediction->getTime() === GoalTimes::FIRST_HALF()->getValue()) {
                        $points = self::POINTS_HALF;
                    }
                    break;
                case GoalTimes::FOURTH_FIFTEEN()->getValue():
                case GoalTimes::FIFTH_FIFTEEN()->getValue():
                case GoalTimes::SIXTH_FIFTEEN()->getValue():
                    if ($prediction->getTime() === GoalTimes::SECOND_HALF()->getValue()) {
                        $points = self::POINTS_HALF;
                    }
                    break;
            }
            if ($points > 0) {
                $score = (new Score())
                    ->setGoal($goal)
                    ->setPrediction($prediction)
                    ->setReason(self::CORRECT_HALF)
                    ->setPoints($points);
                $predictionScores[] = $score;
            }
            if ($bonusPointsAvailable && !empty($predictionScores) && !$predictionHasAlreadyScored) {
                $predictionScores[] = (new Score())
                    ->setPrediction($prediction)
                    ->setReason(self::BONUS_POINT)
                    ->setPoints(self::POINTS_BONUS)
                    ->setGoal($goal)
                ;
            }
            array_walk($predictionScores, function (Score $score) use ($prediction) {
                $prediction->addScore($score);
            });
            $scores = array_merge($scores, $predictionScores);
        }

        return $scores;
    }

    public function getReasonName(int $reason)
    {
        switch ($reason) {
            case self::CORRECT_HALF:
                return 'Correct half';
            case self::CORRECT_TIME:
                return 'Correct 15 min time period';
            case self::CORRECT_POSITION:
                return 'Correct position';
            case self::BONUS_POINT:
                return 'Bonus point';
            default:
                return 'Unknown';
        }
    }
}
