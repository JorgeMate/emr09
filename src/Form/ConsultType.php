<?php

namespace App\Form;

use App\Entity\Consult;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;


class ConsultType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            
            ->add('consult', TextType::class, [
                'label' => 'label.con',

            ])
            ->add('treatment', TextType::class, [
                'label' => 'label.treatment',
                'required' => false, 

            ])
            ->add('notes', TextareaType::class, [
                'label' => 'label.notes',
                'required' => false,  
                'attr' => ['rows' => '3'],              
            ])



        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Consult::class,
        ]);
    }
}
