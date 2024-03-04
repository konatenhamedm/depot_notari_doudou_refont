<?php

namespace App\Form;

use App\Entity\CourierArrive;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CourierArrive1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('numero')
            ->add('dateReception')
            ->add('objet')
            ->add('categorie')
            ->add('active')
            ->add('etat')
            ->add('type')
            ->add('existe')
            ->add('rangement')
            ->add('expediteur')
            ->add('recep')
            ->add('user')
            ->add('entreprise')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CourierArrive::class,
        ]);
    }
}
