<?php

namespace App\Controller;

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

    public function __construct(UserProvider $userProvider, PointsTable $pointsTable)
    {
        $this->userProvider = $userProvider;
        $this->pointsTable = $pointsTable;
    }

    /**
     * @Route("/table", name="table")
     */
    public function index()
    {
        $users = $this->userProvider->getUsers();
        $table = $this->pointsTable->loadCurrent($users);
        return $this->render('table.html.twig', ['table' => $table]);
    }
}