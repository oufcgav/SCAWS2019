<?php

namespace App\Tests\Database;

use App\Service\PositionResetter;

class PositionResetterTest extends BaseTestCase
{
    public function testFirstMatchIsReset()
    {
        $season = $this->createSeason();
        $match = $this->createMatch($season, 'First', new \DateTimeImmutable());
        $this->em->flush();
        $resetter = self::$container->get(PositionResetter::class);
        $match = $resetter->reset($season, $match);

        $this->assertTrue($match->resetPositionChoices());
    }

    public function testSecondMatchIsNotReset()
    {
        $season = $this->createSeason();
        $previousReset = $this->createMatch($season, 'First', (new \DateTimeImmutable())->sub(new \DateInterval('P1W')));
        $previousReset->setReset();
        $thisMatch = $this->createMatch($season, 'Second', new \DateTimeImmutable());
        $this->em->flush();
        $resetter = self::$container->get(PositionResetter::class);
        $thisMatch = $resetter->reset($season, $thisMatch);

        $this->assertFalse($thisMatch->resetPositionChoices());
    }

    public function testThirdMatchIsNotReset()
    {
        $season = $this->createSeason();
        $previousReset = $this->createMatch($season, 'First', (new \DateTimeImmutable())->sub(new \DateInterval('P2W')));
        $previousReset->setReset();
        $this->createMatch($season, 'Second', (new \DateTimeImmutable())->sub(new \DateInterval('P1W')));
        $thisMatch = $this->createMatch($season, 'Third', new \DateTimeImmutable());
        $this->em->flush();
        $resetter = self::$container->get(PositionResetter::class);
        $thisMatch = $resetter->reset($season, $thisMatch);

        $this->assertFalse($thisMatch->resetPositionChoices());
    }

    public function testFourthMatchIsReset()
    {
        $season = $this->createSeason();
        $previousReset = $this->createMatch($season, 'First', (new \DateTimeImmutable())->sub(new \DateInterval('P3W')));
        $previousReset->setReset();
        $this->createMatch($season, 'Second', (new \DateTimeImmutable())->sub(new \DateInterval('P2W')));
        $this->createMatch($season, 'Third', (new \DateTimeImmutable())->sub(new \DateInterval('P1W')));
        $thisMatch = $this->createMatch($season, 'Fourth', new \DateTimeImmutable());
        $this->em->flush();
        $resetter = self::$container->get(PositionResetter::class);
        $thisMatch = $resetter->reset($season, $thisMatch);

        $this->assertTrue($thisMatch->resetPositionChoices());
    }

    public function testOutOfSyncMatchResetsCorrectly()
    {
        $season = $this->createSeason();
        $this->createMatch($season, 'First', (new \DateTimeImmutable())->sub(new \DateInterval('P4W')));
        $previousReset = $this->createMatch($season, 'Second', (new \DateTimeImmutable())->sub(new \DateInterval('P3W')));
        $previousReset->setReset();
        $this->createMatch($season, 'Third', (new \DateTimeImmutable())->sub(new \DateInterval('P2W')));
        $this->createMatch($season, 'Fourth', (new \DateTimeImmutable())->sub(new \DateInterval('P1W')));
        $thisMatch = $this->createMatch($season, 'Fifth', new \DateTimeImmutable());
        $this->em->flush();
        $resetter = self::$container->get(PositionResetter::class);
        $thisMatch = $resetter->reset($season, $thisMatch);

        $this->assertTrue($thisMatch->resetPositionChoices());
    }
}
