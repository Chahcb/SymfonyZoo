<?php

namespace App\Controller;

use App\Entity\Animal;
use App\Entity\Enclos;
use App\Entity\Espace;
use App\Form\EnclosSupprimerType;
use App\Form\EnclosType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EnclosController extends AbstractController
{
    #[Route('/lesEnclos/{idEspace}', name: 'voir_enclos')]
    public function voirEnclosEspace($idEspace, ManagerRegistry $doctrine): Response
    {
        $espace = $doctrine->getRepository(Espace::class)->find($idEspace);
        if (!$espace) {
            throw $this->createNotFoundException("Aucun espace avec l'id $idEspace");
        }

        return $this->render('enclos/index.html.twig', [
            'espace' => $espace,
            'enclos' => $espace->getEnclos()
        ]);
    }

    #[Route('/enclos/ajouter', name: 'enclos_ajouter')]
    public function ajouterEnclos(ManagerRegistry $doctrine, Request $request): Response
    {
        $enclos = new Enclos();
        $form = $this->createForm(EnclosType::class, $enclos);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->persist($enclos);
            $em->flush();
            return $this->redirectToRoute("voir_enclos", ["idEspace" => $enclos->getEspace()->getId()]);
        }

        $repo = $doctrine->getRepository(Enclos::class);
        $enclos = $repo->findAll();

        return $this->render('enclos/ajouterEnclos.html.twig', [
            'enclos' => $enclos,
            'formulaire' => $form->createView()
        ]);
    }

    #[Route('/enclos/modifier/{id}', name: 'enclos_modifier')]
    public function modifierEnclos($id, ManagerRegistry $doctrine, Request $request)
    {
        $animaux = $doctrine->getRepository(Animal::class)->findBy(array('Enclos' => $id));
        $enclos = $doctrine->getRepository(Enclos::class)->find($id);

        if (!$enclos) {
            throw $this->createNotFoundException("Aucun enclos avec l'id $id");
        }

        $form = $this->createForm(EnclosType::class, $enclos);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // l'enclos est placé en quarantaine, alors tous les animaux qu’il contient sont mis en quarantaine
            if ($form->get('quarantaine')->getData()) {
                foreach ($animaux as $a) {
                    $a->setQuarantaine(True);
                    $em = $doctrine->getManager();
                    $em->persist($a);
                    $em->flush();
                }
            }

            $em = $doctrine->getManager();
            $em->persist($enclos);
            $em->flush();
            return $this->redirectToRoute("voir_enclos", ["idEspace" => $enclos->getEspace()->getId()]);
        }

        return $this->render("enclos/modifierEnclos.html.twig", [
            'enclos' => $enclos,
            'formulaire' => $form->createView()
        ]);
    }

    #[Route('/enclos/supprimer/{id}', name: 'enclos_supprimer')]
    public function supprimerEnclos($id, ManagerRegistry $doctrine, Request $request)
    {
        $enclos = $doctrine->getRepository(Enclos::class)->find($id);
        $animaux = $doctrine->getRepository(Animal::class)->find($id);

        if (!$enclos) {
            throw $this->createNotFoundException("Aucun enclos avec l'id $id");
        }

        // si l'enclos n'est vide pas vide on peut pas le supprimer
        if ($animaux) {
            throw $this->createNotFoundException("L'enclos n'est pas vide");
        }

        $form = $this->createForm(EnclosSupprimerType::class, $enclos);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->remove($enclos);
            $em->flush();
            return $this->redirectToRoute("voir_enclos", ["idEspace" => $enclos->getEspace()->getId()]);
        }

        return $this->render("enclos/supprimerEnclos.html.twig", [
            'enclos' => $enclos,
            'formulaire' => $form->createView()
        ]);
    }
}
