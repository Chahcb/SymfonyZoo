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

        // on verifie si les animaux de l'enclos sont en quarantaine
        // 1 => True et 0 => False
        // si oui alors l'enclos reste en quarantaine sinon il ne l'est plus
        $animauxQuarantaine = count($enclos->getAnimal()->filter(fn($animal) => $animal->isQuarantaine() == 1));

        if ($animauxQuarantaine == 0) {
            $enclos->setQuarantaine(False);
            $ema = $doctrine->getManager();
            $ema->persist($enclos);
            $ema->flush();
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
        $form = $this->createForm(AnimalType::class, $animal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // la date de naissance ne doit pas être supérieure à la date d’arrivée au zoo
            if ($form->get('date_naissance')->getData() >= $form->get('date_arrivee')->getData()) {
                throw $this->createNotFoundException("La date de naissance doit être antérieure à la date d’arrivée");
            }

            // la date de départ doit être supérieure à la date d’arrivée au zoo
            if ($form->get('date_depart')->getData() <= $form->get('date_arrivee')->getData()) {
                throw $this->createNotFoundException("date de départ doit être supérieure à la date d’arrivée");
            }

            // on ne peut pas stérilisé l'animal si son sexe est indéterminé
            if ($form->get('sexe')->getData() == 'non déterminé' && $form->get('sterilise')->getData()) {
                throw $this->createNotFoundException("Tu ne peux pas stériliser l'animal si son sexe est indéterminé");
            }

            $animaux = $doctrine->getRepository(Animal::class)->findBy(array('Enclos' => $form->get('Enclos')->getData()));
            $enclos = $doctrine->getRepository(Enclos::class)->find($form->get('Enclos')->getData());

            // On ne doit pas pouvoir ajouter plus d’animaux à l’enclos qu’il ne peut en contenir
            if (count($animaux) == $enclos->getNombreMaxAnimal()) {
                throw $this->createNotFoundException("Dommage l'enclos est plein :|");
            }

            // l'enclos est placé en quarantaine, alors l'animal est mis en quarantaine
            if ($enclos->isQuarantaine()) {
                $animal->setQuarantaine(True);
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

        $form = $this->createForm(AnimalType::class, $animal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // la date de naissance ne doit pas être supérieure à la date d’arrivée au zoo
            if ($form->get('date_naissance')->getData() >= $form->get('date_arrivee')->getData()) {
                throw $this->createNotFoundException("La date de naissance doit être antérieure à la date d’arrivée");
            }

            // la date de départ doit être supérieure à la date d’arrivée au zoo
            if ($form->get('date_depart')->getData() <= $form->get('date_arrivee')->getData()) {
                throw $this->createNotFoundException("La date de départ doit être supérieure à la date d’arrivée");
            }

            // on ne peut pas stérilisé l'animal si son sexe est indéterminé
            if ($form->get('sexe')->getData() == 'non déterminé' && $form->get('sterilise')->getData()) {
                throw $this->createNotFoundException("Tu ne peux pas steriliser l'animal si son son sexe est indéterminé");
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
