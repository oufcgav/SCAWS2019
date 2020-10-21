<?php

namespace App\Tests\Database;

use App\Entity\GoalTimes;
use App\Entity\Positions;
use App\Security\User;
use App\Service\PredictionRestriction;
use DateInterval;
use DateTimeImmutable;

class PredictionRestrictionTest extends BaseTestCase
{
    public function testCannotPredictSamePositionOrTime()
    {
        $user = (new User())->setUsername('Andy');
        $season = $this->createSeason();
        $firstMatch = $this->createMatch($season, 'First match', (new DateTimeImmutable())->sub(new DateInterval('P2W')));
        $firstMatch->setReset();
        $previousMatch = $this->createMatch($season, 'Previous match', (new DateTimeImmutable())->sub(new DateInterval('P1W')));
        $this->createMatch($season, 'Next match', (new DateTimeImmutable())->add(new DateInterval('P1W')));
        $position = Positions::MIDFIELDERS()->getValue();
        $time = GoalTimes::SECOND_HALF()->getValue();
        $this->createPrediction('Andy', $position, $time, $previousMatch);
        $this->em->flush();

        $restrictions = self::$container->get(PredictionRestriction::class);
        $positions = $restrictions->getPositions($user);
        $timings = $restrictions->getTimings($user);
        $this->assertNotContains($position, $positions);
        $this->assertNotContains($time, $timings);
    }

    public function testCanPredictSamePositionAfterReset()
    {
        $user = (new User())->setUsername('Andy');
        $season = $this->createSeason();
        $oldMatch = $this->createMatch($season, 'Last match', (new DateTimeImmutable())->sub(new DateInterval('P1W')));
        $currentMatch = $this->createMatch($season, 'Next match', (new DateTimeImmutable())->add(new DateInterval('P1W')));
        $currentMatch->setReset();
        $position = Positions::MIDFIELDERS()->getValue();
        $time = GoalTimes::SECOND_HALF()->getValue();
        $this->createPrediction('Andy', $position, $time, $oldMatch);
        $this->em->flush();

        $restrictions = self::$container->get(PredictionRestriction::class);
        $positions = $restrictions->getPositions($user);
        $timings = $restrictions->getTimings($user);
        $this->assertContains($position, $positions);
        $this->assertNotContains($time, $timings);
    }
}
