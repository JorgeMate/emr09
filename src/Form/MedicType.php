<?php

namespace App\Form;

use App\Entity\User;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;



use Symfony\Component\Security\Core\Security;

class MedicType extends AbstractType
{

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('speciality')
            ->add('tax_code')
            ->add('title')
            ->add('reg_code1')
            ->add('reg_code2')
       
            ->add('internal')
            ->add('cod');

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
