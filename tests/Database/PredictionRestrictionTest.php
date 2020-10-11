<?php

namespace App\Tests\Database;

use App\Entity\Match;
use App\Entity\Prediction;
use App\Entity\Season;
use App\Repository\FixtureList;
use App\Security\User;
use App\Service\PredictionRestriction;
use DateInterval;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Security\Core\User\UserInterface;

class PredictionRestrictionTest extends KernelTestCase
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    protected function setUp()
    {
        self::bootKernel();

        $this->em = self::$container->get('doctrine.orm.default_entity_manager');
    }


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

    protected function createSeason(): Season
    {
        $season = (new Season())
            ->setLabel('Test season')
            ->setStartDate((new DateTimeImmutable())->sub(new DateInterval('P1M')))
            ->setEndDate((new DateTimeImmutable())->add(new DateInterval('P1M')));
        $this->em->persist($season);

        return $season;
    }

    protected function createPrediction($user, $position = 'Defenders', $timing = 'Second half', ?Match $match = null): Prediction
    {
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

    private function createMatch(Season $season, string $opponent, DateTimeImmutable $date): Match
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