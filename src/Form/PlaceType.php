<?php

namespace App\Form;

use App\Entity\Place;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;


class PlaceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('enabled', CheckboxType::class, [
                'label' => 'label.enabled',
                'required' => false,
            ])

            ->add('name', TextType::class, [
                'label' => 'entity.title_ins',
            ])
            ->add('contact', TextType::class, [
                'label' => 'label.contact',
                'required' => false,                
            ])
            ->add('email', EmailType::class, [
                'label' => 'label.email',
                'required' => false,
            ])
            ->add('tel', TelType::class, [
                'label' => 'label.tel',                
                'required' => false,                
            ])
            ->add('fax', TelType::class, [
                'required' => false,                
            ])
            ->add('notes', TextareaType::class, [
                'label' => 'label.notes',
                'required' => false,  
                'attr' => ['rows' => '4'],              
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Place::class,
        ]);
    }
}
