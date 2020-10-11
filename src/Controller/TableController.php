<?php

namespace App\Controller;

use App\Repository\FixtureList;
use App\Repository\PointsTable;
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

    public function __construct(
        UserProvider $userProvider,
        PointsTable $pointsTable,
        FixtureList $fixtureList
    ) {
        $this->userProvider = $userProvider;
        $this->pointsTable = $pointsTable;
        $this->fixtureList = $fixtureList;
    }

    /**
     * @Route("/table", name="table")
     */
    public function index()
    {
        $nextMatch = $this->fixtureList->findNextMatch();
        $users = $this->userProvider->getUsers();
        $table = $this->pointsTable->loadCurrent($users, $nextMatch);
        return $this->render('table.html.twig', ['table' => $table]);
    }
}