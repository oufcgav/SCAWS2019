<?php

namespace App\Command;

use App\Entity\Match;
use App\Entity\TableEntry;
use App\Repository\FixtureList;
use App\Repository\PointsTable;
use App\Security\UserProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SaveHistoricTablesCommand extends Command
{
    protected static $defaultName = 'scaws:save-tables';

    /**
     * @var PointsTable
     */
    private $pointsTable;
    /**
     * @var FixtureList
     */
    private $fixtureList;
    /**
     * @var UserProvider
     */
    private $userList;
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(
        EntityManagerInterface $em,
        PointsTable $pointsTable,
        FixtureList $fixtureList,
        UserProvider $userList,
        string $name = null
    ) {
        parent::__construct($name);
        $this->pointsTable = $pointsTable;
        $this->fixtureList = $fixtureList;
        $this->userList = $userList;
        $this->em = $em;
    }

    public function run(InputInterface $input, OutputInterface $output)
    {
        $lastMatchSaved = $this->pointsTable->getLastMatchSaved();
        $lastMatchSaved = $this->fixtureList->find($lastMatchSaved);
        $allMatches = $this->fixtureList->findAll();
        if ($lastMatchSaved) {
            $matchesToSave = array_filter($allMatches, function (Match $match) use ($lastMatchSaved) {
                return $match->getId() > $lastMatchSaved->getId();
            });
        } else {
            $matchesToSave = $allMatches;
        }

        $users = $this->userList->getUsers();
        foreach ($matchesToSave as $match) {
            $table = $this->pointsTable->loadCurrent($match->getSeason(), $users, $match);
            foreach ($table as $position => $place) {
                $tableEntry = (new TableEntry())
                    ->setMatch($match)
                    ->setPoints($place->getPoints())
                    ->setUser($place->getUsername())
                    ->setBonusPoints($place->getBonusPoints())
                    ->setCurrentPosition($position + 1)
                    ->setPints($place->getPints())
                    ->setPlayed($place->getPlayed())
                ;
                $this->em->persist($tableEntry);
                $place->setTableData(0, 0, 0, 0, 0);
            }
        }
        $this->em->flush();

        return 0;
    }
}
