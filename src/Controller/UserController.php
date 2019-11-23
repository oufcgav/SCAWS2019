<?php

namespace App\Controller;

use App\Security\UserProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @var UserProvider
     */
    private $userProvider;

    public function __construct(UserProvider $userProvider)
    {
        $this->userProvider = $userProvider;
    }

    /**
     * @Route("/user/{username}", name="user")
     */
    public function index(string $username)
    {
        $user = $this->userProvider->loadUserByUsername($username);
        return $this->render('user.html.twig', ['user' => $user, 'predictions' => []]);
    }
}