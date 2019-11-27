<?php

namespace App\Controller;

use App\Entity\Match;
use App\Form\Type\MatchType;
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

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/match", name="add_match")
     */
    public function index(Request $request)
    {
        $match = new Match();
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