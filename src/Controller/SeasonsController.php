<?php

namespace App\Controller;

use App\Entity\Season;
use App\Repository\SeasonList;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SeasonsController extends AbstractController
{
    /**
     * @var SeasonList
     */
    private $seasonList;

    public function __construct(SeasonList $seasonList)
    {
        $this->seasonList = $seasonList;
    }

    /**
     * @Route("/seasons", name="list_seasons")
     */
    public function index(Request $request)
    {
        $seasons = $this->seasonList->findAll();
        usort($seasons, function (Season $a, Season $b) {
            return $b->getStartDate() <=> $a->getStartDate();
        });

        return $this->render('seasons.html.twig', [
            'seasons' => $seasons,
        ]);
    }
}
