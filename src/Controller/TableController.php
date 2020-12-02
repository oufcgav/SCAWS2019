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

    /**
     * @Route("/all-time", name="all_time_table")
     */
    public function allTimeTable()
    {
        $table = [];
        $users = $this->userProvider->listUsernames();
        foreach ($users as $username) {
            $table[$username] = [
                'points' => 0,
                'wins' => 0,
                'pints' => 0,
            ];
        }
        $seasons = $this->seasonList->findAll();
        $currentSeason = $this->seasonList->findCurrentSeason();
        foreach ($seasons as $season) {
            $lastMatch = $this->fixtureList->findLastMatch($season);
            if (!$lastMatch) {
                continue;
            }
            $finalTable = $this->pointsTable->loadSavedTable($lastMatch);
            $seasonPoints = [];
            foreach ($finalTable as $entry) {
                $seasonPoints[$entry->getUser()] = $entry->getPoints();
                if ($entry->getCurrentPosition() === 1 && $season !== $currentSeason) {
                    $table[$entry->getUser()]['wins']++;
                }
                $table[$entry->getUser()]['pints']+= $entry->getPints();
            }
            arsort($seasonPoints);
            $maxPoints = reset($seasonPoints);
            if ($maxPoints === 0.0) {
                continue;
            }
            foreach ($seasonPoints as $user => $points) {
                $table[$user]['points']+= ($points / $maxPoints) * 100;
            }
        }
        uasort($table, function ($a, $b) {
            if ($a['points'] === $b['points']) {
                return $b['wins'] <=> $a['wins'];
            }
            return $b['points'] <=> $a['points'];
        });

        return $this->render('allTimeTable.html.twig', ['table' => $table]);
    }
}
