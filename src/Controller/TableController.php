<?php

namespace App\Controller;

use App\Entity\Season;
use App\Repository\FixtureList;
use App\Repository\PointsTable;
use App\Repository\SeasonList;
use App\Security\UserProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TableController extends AbstractController
{
    /**
     * @var UserProvider
     */
    private $userProvider;
    /**
     * @var PointsTable
     */
    private $pointsTable;
    /**
     * @var FixtureList
     */
    private $fixtureList;
    /**
     * @var SeasonList
     */
    private $seasonList;

    public function __construct(
        UserProvider $userProvider,
        PointsTable $pointsTable,
        FixtureList $fixtureList,
        SeasonList $seasonList
    ) {
        $this->userProvider = $userProvider;
        $this->pointsTable = $pointsTable;
        $this->fixtureList = $fixtureList;
        $this->seasonList = $seasonList;
    }

    /**
     * @Route("/table", name="table")
     * @Route("/{season}/table", name="table_old")
     */
    public function index(?Season $season = null)
    {
        $season = $season ?? $this->seasonList->findCurrentSeason();
        $nextMatch = $this->fixtureList->findNextMatch();
        $users = $this->userProvider->getUsers();
        $table = $this->pointsTable->loadCurrent($season, $users, $nextMatch);

        return $this->render('table.html.twig', ['table' => $table, 'season' => $season]);
    }
}
