<?php

namespace App\Form;

use App\Entity\Animal;
use App\Entity\Enclos;
use DateTime;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnimalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('date_naissance', DateType::class, [
                'format' => 'ddMMyyyy',
                'placeholder' => [
                    'year' => 'Year', 'month' => 'Month', 'day' => 'Day',
                ]])
            ->add('date_arrivee', DateType::class, [
                'format' => 'ddMMyyyy',
                'data' => new DateTime()])
            ->add('date_depart', DateType::class, [
                'format' => 'ddMMyyyy',
                'placeholder' => [
                    'year' => 'Year', 'month' => 'Month', 'day' => 'Day',
                ]])
            ->add('zoo_proprietaire')
            ->add('genre')
            ->add('espece')
            ->add('sexe', ChoiceType::class, [
                'choices' => [
                    'mâle' => 'mâle',
                    'femelle' => 'femelle',
                    'non déterminé' => 'non déterminé'
                ],
            ])
            ->add('sterilise')
            ->add('quarantaine')
            ->add('Enclos', EntityType::class, [
                'class' => Enclos::class, // choix de la classe liée
                'choice_label' => 'nom', // choix de ce qui sera affiché comme texte
                'multiple' => false,
                'expanded' => false])
            ->add('OK', SubmitType::class, ['label' => 'OK',
                'attr' => ['class' => 'btn btn-primary px-5']]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Animal::class,
        ]);
    }
}
