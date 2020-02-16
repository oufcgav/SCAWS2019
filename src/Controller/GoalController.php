<?php

namespace App\Controller;

use App\Entity\Goal;
use App\Form\Type\GoalType;
use App\Repository\FixtureList;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class GoalController extends AbstractController
{
    /**
     * @var FixtureList
     */
    private $fixtureList;
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(FixtureList $fixtureList, EntityManagerInterface $em)
    {
        $this->fixtureList = $fixtureList;
        $this->em = $em;
    }

    /**
     * @Route("/goal", name="add_goal")
     */
    public function index(Request $request)
    {
        $currentMatch = $this->fixtureList->findNextMatch();
        if (!$currentMatch) {
            $this->addFlash('error', 'You cannot add a goal as there is no current match.');
            return $this->redirectToRoute('homepage');
        }

        $goal = (new Goal())
            ->setMatchId($currentMatch->getId())
        ;
        $form = $this->createForm(GoalType::class, $goal);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($goal);
            $this->em->flush();
            return $this->redirectToRoute('homepage');
        }

        return $this->render('goal.html.twig', [
            'form' => $form->createView(),
            'match' => $currentMatch,
        ]);
    }
}