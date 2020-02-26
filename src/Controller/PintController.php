<?php

namespace App\Controller;

use App\Entity\Pint;
use App\Form\Type\PintType;
use App\Repository\FixtureList;
use App\Repository\PintRepository;
use App\Security\User;
use App\Security\UserProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PintController extends AbstractController
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
     * @var PintRepository
     */
    private $pintRepository;

    public function __construct(FixtureList $fixtureList, EntityManagerInterface $em, PintRepository $pintRepository)
    {
        $this->fixtureList = $fixtureList;
        $this->em = $em;
        $this->pintRepository = $pintRepository;
    }

    /**
     * @Route("/pint", name="add_pint")
     */
    public function addPint(Request $request)
    {
        $currentMatch = $this->fixtureList->findNextMatch();
        if (!$currentMatch) {
            $this->addFlash('error', 'You cannot add a pint as there is no current match.');
            return $this->redirectToRoute('homepage');
        }

        $form = $this->createForm(PintType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $username = $form->getData()['user'];
            $pint = $this->pintRepository->findByUserAndMatch($username, $currentMatch);
            if (!$pint) {
                $pint = (new Pint())
                    ->setMatch($currentMatch)
                    ->setUser($username)
                ;
            }
            $pint->addPintDrunk();
            $this->em->persist($pint);
            $this->em->flush();
            return $this->redirectToRoute('table');
        }

        return $this->render('pint.html.twig', [
            'form' => $form->createView(),
            'match' => $currentMatch,
        ]);
    }

    /**
     * @Route("/mypint", name="add_my_pint")
     */
    public function addMyPint()
    {
        $currentMatch = $this->fixtureList->findNextMatch();
        if (!$currentMatch) {
            $this->addFlash('error', 'You cannot add a pint as there is no current match.');
            return $this->redirectToRoute('homepage');
        }
        /** @var User $user */
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('error', 'You must be logged in to add a pint for yourself.');
            return $this->redirectToRoute('homepage');
        }
        $pint = $this->pintRepository->findByUserAndMatch($user->getUsername(), $currentMatch);
        if (!$pint) {
            $pint = (new Pint())
                ->setMatch($currentMatch)
                ->setUser($user->getUsername())
            ;
        }
        $pint->addPintDrunk();
        $this->em->persist($pint);
        $this->em->flush();
        return $this->redirectToRoute('table');
    }
}
