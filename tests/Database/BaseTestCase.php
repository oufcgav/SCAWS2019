<?php

namespace App\Tests\Database;

use App\Entity\Match;
use App\Entity\Positions;
use App\Entity\Prediction;
use App\Entity\Season;
use App\Repository\FixtureList;
use DateInterval;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Faker\Generator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BaseTestCase extends KernelTestCase
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;
    /**
     * @var Generator
     */
    protected $faker;

    protected function setUp()
    {
        self::bootKernel();

        $this->em = self::$container->get('doctrine.orm.default_entity_manager');
        $this->faker = Factory::create();
    }

    protected function createSeason(): Season
    {
        $season = (new Season())
            ->setLabel('Test season')
            ->setStartDate((new DateTimeImmutable())->sub(new DateInterval('P1M')))
            ->setEndDate((new DateTimeImmutable())->add(new DateInterval('P1M')));
        $this->em->persist($season);

        return $season;
    }

    protected function createPrediction($user, $position = null, $timing = 'Second half', ?Match $match = null): Prediction
    {
        $position = $position ?? Positions::DEFENDERS()->getValue();
        $fixtureList = self::$container->get(FixtureList::class);
        $prediction = (new Prediction())
            ->setUser($user)
            ->setPosition($position)
            ->setTime($timing)
            ->setMatch($match ?? $fixtureList->findNextMatch())
            ->setAtMatch(true)
            ->setNiceTime('Yes');
        $this->em->persist($prediction);

        return $prediction;
    }

    protected function createMatch(Season $season, string $opponent, DateTimeImmutable $date): Match
    {
        $match = (new Match())
            ->setSeason($season)
            ->setCompetition('League')
            ->setDate($date)
            ->setLocation('Home')
            ->setOpponent($opponent)
        ;
        $this->em->persist($match);

        return $match;
    }
}
