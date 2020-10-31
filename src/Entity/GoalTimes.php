<?php

namespace App\Entity;

use MyCLabs\Enum\Enum;

/**
 * @method static GoalTimes FIRST_HALF()
 * @method static GoalTimes SECOND_HALF()
 * @method static GoalTimes FIRST_FIFTEEN()
 * @method static GoalTimes SECOND_FIFTEEN()
 * @method static GoalTimes THIRD_FIFTEEN()
 * @method static GoalTimes FOURTH_FIFTEEN()
 * @method static GoalTimes FIFTH_FIFTEEN()
 * @method static GoalTimes SIXTH_FIFTEEN()
 * @method static GoalTimes STOPPAGE_TIME()
 */
class GoalTimes extends Enum
{
    private const FIRST_HALF = 'First half';
    private const SECOND_HALF = 'Second half';
    private const FIRST_FIFTEEN = '1-15 mins';
    private const SECOND_FIFTEEN = '16-30 mins';
    private const THIRD_FIFTEEN = '31-45 mins';
    private const FOURTH_FIFTEEN = '46-60 mins';
    private const FIFTH_FIFTEEN = '61-75 mins';
    private const SIXTH_FIFTEEN = '76-90 mins';
    private const STOPPAGE_TIME = 'Stoppage time';

    public static function matchesHalf(Goal $goal, Prediction $prediction): bool
    {
        $goalMatchesPrediction = false;
        switch ($goal->getTiming()) {
            case static::FIRST_FIFTEEN()->getValue():
            case static::SECOND_FIFTEEN()->getValue():
            case static::THIRD_FIFTEEN()->getValue():
                if ($prediction->getTime() === static::FIRST_HALF()->getValue()) {
                    $goalMatchesPrediction = true;
                }
                break;
            case static::FOURTH_FIFTEEN()->getValue():
            case static::FIFTH_FIFTEEN()->getValue():
            case static::SIXTH_FIFTEEN()->getValue():
                if ($prediction->getTime() === static::SECOND_HALF()->getValue()) {
                    $goalMatchesPrediction = true;
                }
                break;
        }

        return $goalMatchesPrediction;
    }
}
