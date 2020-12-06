<?php

namespace App\Form;

use App\Entity\RestaurantTable;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class RestaurantTableType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('capacity', IntegerType::class, [
                'attr'=> ['class'=>'form-control']
            ])
            ->add('number', IntegerType::class, [
                'attr'=> ['class'=>'form-control']
            ])
            ->add('status')
            ->add('save', SubmitType::class, [
                'attr'=> ['class'=>'btn btn-success']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => RestaurantTable::class,
        ]);
    }
}
