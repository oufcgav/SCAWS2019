<?php

namespace App\Controller;

use App\Repository\FixtureList;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{

    /**
     * @var FixtureList
     */
    private $fixtureList;

    public function __construct(FixtureList $fixtureList)
    {
        $this->fixtureList = $fixtureList;
    }

    /**
     * @Route("/", name="homepage")
     */
    public function index(): Response
    {
        $nextMatch = $this->fixtureList->findNextMatch();
        return $this->render('index.html.twig', ['match' => $nextMatch]);
    }
}