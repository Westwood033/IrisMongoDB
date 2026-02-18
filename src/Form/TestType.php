<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class TestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('sepalLengthCm', NumberType::class, [
                'label' => 'Longueur du sépale',
                'html5' => true,
                'scale' => 4,
            ])
            ->add('sepalWidthCm', NumberType::class, [
                'label' => 'Largeur du sépale',
                'html5' => true,
                'scale' => 4,
            ])
            ->add('petalLengthCm', NumberType::class, [
                'label' => 'Longueur du pétale',
                'html5' => true,
                'scale' => 4,
            ])
            ->add('petalWidthCm', NumberType::class, [
                'label' => 'Largeur du pétale',
                'html5' => true,
                'scale' => 4,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
        ]);
    }
}