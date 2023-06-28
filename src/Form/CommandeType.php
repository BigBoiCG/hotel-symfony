<?php

namespace App\Form;

use App\Entity\Commande;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;

class CommandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date_arrivee', DateType::class, [
                'widget' => 'single_text',
                'attr' => [
                    'min' => date_format(new \DateTime('+ 1 days'), "d M y")
                ],
            ])
            ->add('date_depart', DateType::class, [
                'widget' => 'single_text',
                'attr' => [
                    'min' => date_format(new \DateTime('+ 2 days'), "d M y")
                ],
                'constraints' => [
                    new GreaterThanOrEqual(['propertyPath' => 'parent.all[date_arrivee].data',
                    'message' => 'La date de fin doit être ultérieure à la date de réservation d\'au moins 1 jour']),],
            ])
            // ->add('prix_total')
            ->add('prenom')
            ->add('nom')
            ->add('telephone')
            ->add('email')
            // ->add('date_enregistrement')
            // ->add('chambre')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commande::class,
        ]);
    }
}
