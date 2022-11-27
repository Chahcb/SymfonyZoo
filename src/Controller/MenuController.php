<?php

namespace App\Controller;

use App\Entity\Espace;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class MenuController extends AbstractController
{
    public function _menu(ManagerRegistry $doctrine): Response
    {
        return $this->render('menu/_menu.html.twig', [
            'espace'=>$doctrine->getRepository(Espace::class)->findAll()
        ]);
    }
}
