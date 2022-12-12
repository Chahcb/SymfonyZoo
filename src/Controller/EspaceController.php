<?php

namespace App\Controller;

use App\Entity\Espace;
use App\Form\EspaceSupprimerType;
use App\Form\EspaceType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EspaceController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function ajouterEspace(ManagerRegistry $doctrine, Request $request): Response
    {
        $espace = new Espace();
        $form = $this->createForm(EspaceType::class, $espace);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // date de fermeture ne doit être remplie que si la date d’ouverture est également remplie
            if ($form->get('date_ouverture')->getData() == '' && $form->get('date_fermeture')->getData() != '') {
                throw $this->createNotFoundException("Tu ne peux pas mettre de date de fermeture si la date d'ouverture n'est pas rempli");
            }

            // date de fermeture doit bien sûr être supérieure à la date d'ouverture
            if ($form->get('date_ouverture')->getData() >= $form->get('date_fermeture')->getData()) {
                throw $this->createNotFoundException("La date de fermeture ne peux pas être antérieure à la date d'ouverture");
            }

            $em = $doctrine->getManager();
            $em->persist($espace);
            $em->flush();
        }

        $repo = $doctrine->getRepository(Espace::class);
        $espace = $repo->findAll();

        return $this->render('espace/index.html.twig', [
            'espace' => $espace,
            'formulaire' => $form->createView()
        ]);
    }

    #[Route('/espace/modifier/{id}', name: 'espace_modifier')]
    public function modifierEspace($id, ManagerRegistry $doctrine, Request $request)
    {
        $espace = $doctrine->getRepository(Espace::class)->find($id);

        if (!$espace) {
            throw $this->createNotFoundException("Aucun espace avec l'id $id");
        }

        $form = $this->createForm(EspaceType::class, $espace);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // date de fermeture ne doit être remplie que si la date d’ouverture est également remplie
            if ($form->get('date_ouverture')->getData() == '' && $form->get('date_fermeture')->getData() != '') {
                throw $this->createNotFoundException("Tu ne peux pas mettre de date de fermeture si la date d'ouverture n'est pas rempli");
            }

            // date de fermeture doit bien sûr être supérieure à la date d'ouverture
            if ($form->get('date_ouverture')->getData() != "" && $form->get('date_ouverture')->getData() >= $form->get('date_fermeture')->getData()) {
                throw $this->createNotFoundException("La date de fermeture ne peux pas être antérieure à la date d'ouverture");
            }

            $em = $doctrine->getManager();
            $em->persist($espace);
            $em->flush();
            return $this->redirectToRoute("app_home");
        }

        return $this->render("espace/modifierEspace.html.twig", [
            'espace' => $espace,
            'formulaire' => $form->createView()
        ]);
    }

    #[Route('/espace/supprimer/{id}', name: 'espace_supprimer')]
    public function supprimerEspace($id, ManagerRegistry $doctrine, Request $request)
    {
        $espace = $doctrine->getRepository(Espace::class)->find($id);

        if (!$espace) {
            throw $this->createNotFoundException("Aucun espace avec l'id $id");
        }

        $form = $this->createForm(EspaceSupprimerType::class, $espace);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->remove($espace);
            $em->flush();
            return $this->redirectToRoute("app_home");
        }

        return $this->render("espace/supprimerEspace.html.twig", [
            'espace' => $espace,
            'formulaire' => $form->createView()
        ]);
    }
}
