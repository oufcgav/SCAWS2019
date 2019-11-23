<?php

namespace App\Controller;

use App\Security\UserProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TableController extends AbstractController
{

    /**
     * @var UserProvider
     */
    private $userProvider;

    public function __construct(UserProvider $userProvider)
    {
        $this->userProvider = $userProvider;
    }

    public function index()
    {
        $users = $this->userProvider->getUsers();
        return $this->render('table.html.twig', ['table' => $users]);
    }
}