<?php

namespace App\Form;

use App\Entity\Avis;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class AvisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('prenom')
            ->add('categorie', ChoiceType::class, [
                'choices' => [
                'Général' => 'general',
                'Chambres' => 'chambres',
                'Restaurant' => 'resto',
                'Spa' => 'spa',
                ]
            ])
            ->add('contenu')
            ->add('notation', NumberType::class, [
                'attr' => [
                    'min' => 0,
                    'max' => 5,
                ],
            ])
            // ->add('date_enregistrement')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Avis::class,
        ]);
    }
}
