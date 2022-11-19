<?php

namespace App\Controller;

use App\Entity\Enclos;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class MenuController extends AbstractController
{
    public function _menu(ManagerRegistry $doctrine): Response
    {
        return $this->render('menu/_menu.html.twig', [
            'enclos'=>$doctrine->getRepository(Enclos::class)->findAll()
        ]);
    }
}
