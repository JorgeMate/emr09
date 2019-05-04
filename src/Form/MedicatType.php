<?php

namespace App\Form;

use App\Entity\Medicat;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;

class MedicatType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('medicat', TextType::class, [
                'label' => 'label.medicine',

            ])            
            ->add('dosis', TextType::class, [
                'label' => 'label.dosis',
                'required' => false,

            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Medicat::class,
        ]);
    }
}
