<?php

namespace App\Controller;

use App\Entity\Prediction;
use App\Form\Type\PredictionType;
use App\Repository\FixtureList;
use App\Repository\PredictionRepository;
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
    /**
     * @var PredictionRepository
     */
    private $predictions;

    public function __construct(FixtureList $fixtureList, PredictionRepository $predictions, EntityManagerInterface $em)
    {
        $this->fixtureList = $fixtureList;
        $this->em = $em;
        $this->predictions = $predictions;
    }

    /**
     * @Route("/predict", name="add_prediction")
     */
    public function index(Request $request)
    {
        $nextMatch = $this->fixtureList->findNextMatch();
        if (!$nextMatch) {
            $this->addFlash('error', 'You cannot add a prediction as there is no current match.');
            return $this->redirectToRoute('homepage');
        }

        $prediction = $this->predictions->findByMatchAndUser($nextMatch, $this->getUser());
        if (!$prediction) {
            $prediction = (new Prediction())
                ->setMatch($nextMatch)
                ->setUser($this->getUser()->getUsername())
            ;
        }

        $form = $this->createForm(PredictionType::class, $prediction);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($prediction->getPosition() === 'Goalkeeper') {
                $prediction->setReset();
            } else {
                $lastPredictions = $this->predictions->getLastPredictions($nextMatch, $this->getUser()->getUsername(), 2);
                $lastPredictions[] = $prediction->getPosition();
                $lastPredictions = array_unique($lastPredictions);
                if (count($lastPredictions) === 3) {
                    $prediction->setReset();
                }
            }
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