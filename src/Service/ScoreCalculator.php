<?php


namespace App\Service;


use App\Entity\Goal;
use App\Entity\Prediction;
use App\Entity\Score;

class ScoreCalculator
{
    const CORRECT_HALF = 1;
    const CORRECT_TIME = 2;
    const CORRECT_POSITION = 3;
    const BONUS_POINT = 4;

    const POINTS_STRIKERS = 1;
    const POINTS_MIDFIELDERS = 2;
    const POINTS_DEFENDERS = 3;
    const POINTS_HALF = 1;
    const POINTS_TIME = 3;
    const POINTS_STOPPAGE_BONUS = 1.5;
    const POINTS_BONUS = 1;

    /**
     * @param Goal $goal
     * @param Prediction[] $predictions
     * @param bool $bonusPointsAvailable
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
            $bonusPointsAvailable = $prediction->getMatch()->qualifiesForBonusPoint();
            $predictionHasAlreadyScored = $prediction->hasScored();
            if ($goal->getPosition() === $prediction->getPosition()) {
                switch ($goal->getPosition()) {
                    case 'Defenders':
                        $points = self::POINTS_DEFENDERS;
                        break;
                    case 'Midfielders':
                        $points = self::POINTS_MIDFIELDERS;
                        break;
                    case 'Strikers':
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
                if ($goal->getTiming() === 'Stoppage time') {
                    $points += self::POINTS_STOPPAGE_BONUS;
                }
            }
            switch ($goal->getTiming()) {
                case '1-15 mins':
                case '16-30 mins':
                case '31-45 mins':
                    if ($prediction->getTime() === 'First half') {
                        $points = self::POINTS_HALF;
                    }
                    break;
                case '46-60 mins':
                case '61-75 mins':
                case '76-90 mins':
                    if ($prediction->getTime() === 'Second half') {
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
}
