<?php

namespace App\Form;

use App\Entity\Enclos;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EnclosSupprimerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('OK', SubmitType::class,
                ['label' => 'Supprimer',
                    'row_attr' => ['class' => 'input-group'],
                    'attr' => ['class' => 'btn btn-danger btn-lg px-4 gap-3']]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Enclos::class,
        ]);
    }
}
