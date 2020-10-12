<?php

namespace App\Tests\Database;


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
        $oldMatch = $this->createMatch($season, 'Last match', (new DateTimeImmutable())->sub(new DateInterval('P1W')));
        $this->createMatch($season, 'Next match', (new DateTimeImmutable())->add(new DateInterval('P1W')));
        $position = 'Midfielders';
        $time = 'Second half';
        $this->createPrediction('Andy', $position, $time, $oldMatch);
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
        $this->createMatch($season, 'Next match', (new DateTimeImmutable())->add(new DateInterval('P1W')));
        $position = 'Midfielders';
        $time = 'Second half';
        $prediction = $this->createPrediction('Andy', $position, $time, $oldMatch);
        $prediction->setReset();
        $this->em->flush();

        $restrictions = self::$container->get(PredictionRestriction::class);
        $positions = $restrictions->getPositions($user);
        $timings = $restrictions->getTimings($user);
        $this->assertContains($position, $positions);
        $this->assertNotContains($time, $timings);
    }
}