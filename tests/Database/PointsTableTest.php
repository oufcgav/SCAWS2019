<?php

namespace App\Tests\Database;

use App\Repository\PointsTable;
use App\Security\User;
use App\Security\UserProvider;
use DateTimeImmutable;

class PointsTableTest extends BaseTestCase
{
    /**
     * @var PointsTable
     */
    private $pointsTable;
    /**
     * @var User[]
     */
    private $users;

    protected function setUp()
    {
        parent::setUp();

        $this->pointsTable = self::$container->get(PointsTable::class);
        $this->users = self::$container->get(UserProvider::class)->getUsers();
    }

    public function testWithNoScoresReturnsEmptyTable()
    {
        $table = $this->pointsTable->loadCurrent($this->users);

        $this->assertCount(7, $table);
    }

    public function testReturnsTableInOrder()
    {
        $season = $this->createSeason();
        $match = $this->createMatch($season, 'First match', new DateTimeImmutable());
        $this->em->flush();
        $this->createPrediction('Andy')
            ->setPoints($this->faker->numberBetween(0, 6));
        $this->createPrediction('Deadly')
            ->setPoints($this->faker->numberBetween(0, 6));
        $this->createPrediction('Smudge')
            ->setPoints($this->faker->numberBetween(0, 6));
        $this->em->flush();

        $table = $this->pointsTable->loadCurrent($this->users, $match);

        $points = 8;
        foreach ($table as $user) {
            $this->assertLessThanOrEqual($points, $user->getPoints());
            $points = $user->getPoints();
        }
    }

    public function testReturnsTableChangesFromPreviousMatch()
    {
        $season = $this->createSeason();
        $firstMatch = $this->createMatch($season, 'First match', (new DateTimeImmutable())->sub(new \DateInterval('P1W')));
        $this->em->flush();
        $this->createPrediction('Andy', 'Defenders', 'Second Half', $firstMatch)
            ->setPoints(3)
        ;
        $this->createPrediction('Deadly', 'Defenders', 'Second Half', $firstMatch)
            ->setPoints(2)
        ;
        $this->createPrediction('Smudge', 'Defenders', 'Second Half', $firstMatch)
            ->setPoints(1)
        ;
        $secondMatch = $this->createMatch($season, 'Current Match', new DateTimeImmutable());
        $this->em->flush();
        $this->createPrediction('Andy')
            ->setPoints(3)
        ;
        $this->createPrediction('Deadly')
            ->setPoints(1)
        ;
        $this->createPrediction('Smudge')
            ->setPoints(3)
        ;
        $this->em->flush();

        $table = $this->pointsTable->loadCurrent($this->users, $secondMatch);

        foreach ($table as $user) {
            switch ($user->getUsername()) {
                case 'Deadly':
                    $this->assertTrue($user->hasMovedDown());
                    $this->assertFalse($user->hasMovedUp());
                    break;
                case 'Smudge':
                    $this->assertFalse($user->hasMovedDown());
                    $this->assertTrue($user->hasMovedUp());
                    break;
                default:
                    $this->assertFalse($user->hasMovedDown());
                    $this->assertFalse($user->hasMovedUp());
                    break;
            }
        }
    }
}