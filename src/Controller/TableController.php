<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TableController extends AbstractController
{
    public function index()
    {
        return $this->render('table.html.twig', ['table' => []]);
    }
}