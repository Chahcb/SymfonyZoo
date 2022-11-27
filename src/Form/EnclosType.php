<?php

namespace App\Form;

use App\Entity\Enclos;
use App\Entity\Espace;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EnclosType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('superficie', IntegerType::class,
                ['attr' => ['min' => 20, 'max' => 2000]])
            ->add('nombre_max_animal', IntegerType::class,
                ['attr' => ['min' => 2, 'max' => 50]])
            ->add('quarantaine')
            ->add('Espace', EntityType::class, [
                'class' => Espace::class, // choix de la classe liée
                'choice_label' => 'nom', // choix de ce qui sera affiché comme texte
                'multiple' => false,
                'expanded' => false])
            ->add('OK', SubmitType::class, ['label' => 'OK',
                'attr' => ['class' => 'btn btn-primary px-5']]);;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Enclos::class,
        ]);
    }
}
