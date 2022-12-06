<?php

namespace App\Controller;

use App\Entity\Animal;
use App\Entity\Enclos;
use App\Form\AnimalSupprimerType;
use App\Form\AnimalType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AnimalController extends AbstractController
{
    #[Route('/animaux/{idEnclos}', name: 'voir_animaux')]
    public function voirAnimalEnclos($idEnclos, ManagerRegistry $doctrine): Response
    {
        $enclos = $doctrine->getRepository(Enclos::class)->find($idEnclos);
        if (!$enclos) {
            throw $this->createNotFoundException("Aucun enclos avec l'id $idEnclos");
        }

        return $this->render('animaux/index.html.twig', [
            'enclos' => $enclos,
            'animaux' => $enclos->getAnimal()
        ]);
    }

    #[Route('/animal/ajouter', name: 'animal_ajouter')]
    public function ajouterAnimal(ManagerRegistry $doctrine, Request $request)
    {
        $animal = new Animal();

        // TODO : numéro d’identification a toujours exactement 14 chiffres
        // TODO : vérifier que l'enclos n'est pas plein

        $form = $this->createForm(AnimalType::class, $animal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // date de naissance ne doit pas être supérieure à la date d’arrivée au zoo
            if ($form->get('date_naissance')->getData() >= $form->get('date_arrivee')->getData()) {
                throw $this->createNotFoundException("date de naissance doit être antérieure à la date d’arrivée");
            }

            // date de départ doit être supérieure à la date d’arrivée
            if ($form->get('date_depart')->getData() <= $form->get('date_arrivee')->getData()) {
                throw $this->createNotFoundException("date de départ doit être antérieure à la date d’arrivée");
            }

            // on ne peut pas stérilisé l'animal si son sexe est non déterminé
            if ($form->get('sexe')->getData() == 'non déterminé' && $form->get('sterilise')->getData() == True) {
                throw $this->createNotFoundException("Tu ne peux pas stérilisé l'animal si son son sexe est indéterminé");
            }

            $em = $doctrine->getManager();
            $em->persist($animal);
            $em->flush();
            return $this->redirectToRoute("voir_animaux", ["idEnclos" => $animal->getEnclos()->getId()]);
        }

        return $this->render("animaux/ajouterAnimal.html.twig", ['formulaire' => $form->createView()]);
    }

    #[Route('/animal/modifier/{id}', name: 'animal_modifier')]
    public function modifierAnimal($id, ManagerRegistry $doctrine, Request $request)
    {
        $animal = $doctrine->getRepository(Animal::class)->find($id);

        if (!$animal) {
            throw $this->createNotFoundException("Aucun animal avec l'id $id");
        }

        // TODO : vérifier que l'enclos n'est pas plein

        $form = $this->createForm(AnimalType::class, $animal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // date de naissance ne doit pas être supérieure à la date d’arrivée au zoo
            if ($form->get('date_naissance')->getData() >= $form->get('date_arrivee')->getData()) {
                throw $this->createNotFoundException("date de naissance doit être antérieure à la date d’arrivée");
            }

            // date de départ doit être supérieure à la date d’arrivée
            if ($form->get('date_depart')->getData() <= $form->get('date_arrivee')->getData()) {
                throw $this->createNotFoundException("date de départ doit être antérieure à la date d’arrivée");
            }

            // on ne peut pas stérilisé l'animal si son sexe est non déterminé
            if ($form->get('sexe')->getData() == 'non déterminé' && $form->get('sterilise')->getData() == True) {
                throw $this->createNotFoundException("Tu ne peux pas stérilisé l'animal si son son sexe est indéterminé");
            }

            $em = $doctrine->getManager();
            $em->persist($animal);
            $em->flush();
            return $this->redirectToRoute("voir_animaux", ["idEnclos" => $animal->getEnclos()->getId()]);
        }

        return $this->render("animaux/modifierAnimal.html.twig", [
            'animal' => $animal,
            'formulaire' => $form->createView()
        ]);
    }

    #[Route('/animal/supprimer/{id}', name: 'animal_supprimer')]
    public function supprimerAnimal($id, ManagerRegistry $doctrine, Request $request)
    {
        $animal = $doctrine->getRepository(Animal::class)->find($id);

        if (!$animal) {
            throw $this->createNotFoundException("Aucun animal avec l'id $id");
        }

        $form = $this->createForm(AnimalSupprimerType::class, $animal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->remove($animal);
            $em->flush();
            return $this->redirectToRoute("voir_animaux", ["idEnclos" => $animal->getEnclos()->getId()]);
        }

        return $this->render("animaux/supprimerAnimal.html.twig", [
            'animal' => $animal,
            'formulaire' => $form->createView()
        ]);
    }
}
