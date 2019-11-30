<?php

namespace App\Controller;

use App\Entity\Prediction;
use App\Form\Type\PredictionType;
use App\Repository\FixtureList;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PredictionController extends AbstractController
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
     * @Route("/predict", name="add_prediction")
     */
    public function index(Request $request)
    {
        $nextMatch = $this->fixtureList->findNextMatch();
        if (!$nextMatch) {
            return $this->redirectToRoute('homepage');
        }
        $prediction = (new Prediction())
            ->setMatchId($nextMatch->getId())
            ->setUser($this->getUser()->getUsername())
        ;
        $form = $this->createForm(PredictionType::class, $prediction);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($prediction);
            $this->em->flush();
            return $this->redirectToRoute('homepage');
        }
        $febMidweek = false;
        $matchDate = $nextMatch->getDate();
        if ($matchDate->format('M') === 'Feb'
            && ($matchDate->format('N') > 1 && $matchDate->format('N') < 5)
        ) {
            $febMidweek = true;
        }
        return $this->render('predict.html.twig', [
            'form' => $form->createView(),
            'match' => $nextMatch,
            'user' => $this->getUser(),
            'febMidweek' => $febMidweek
        ]);

    }
}