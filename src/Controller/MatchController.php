<?php

namespace App\Controller;

use App\Entity\Match;
use App\Form\Type\MatchType;
use App\Repository\FixtureList;
use App\Repository\SeasonList;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MatchController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var SeasonList
     */
    private $seasonList;
    /**
     * @var FixtureList
     */
    private $fixtureList;

    public function __construct(
        EntityManagerInterface $em,
        SeasonList $seasonList,
        FixtureList $fixtureList
    ) {
        $this->em = $em;
        $this->seasonList = $seasonList;
        $this->fixtureList = $fixtureList;
    }

    /**
     * @Route("/match", name="add_match")
     */
    public function index(Request $request)
    {
        $match = $this->fixtureList->findNextMatch();
        if (!$match) {
            $match = (new Match())
                ->setSeason($this->seasonList->findCurrentSeason())
            ;
        }
        $form = $this->createForm(MatchType::class, $match);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($match);
            $this->em->flush();

            return $this->redirectToRoute('homepage');
        }

        return $this->render('match.html.twig', ['form' => $form->createView()]);
    }
}
