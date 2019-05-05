<?php

namespace App\Form;

use App\Entity\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use App\Repository\TypeRepository;

use Symfony\Component\Security\Core\Security;

use Symfony\Component\Routing\RouterInterface;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;



class SelectType extends AbstractType
{

    public function __construct(Security $security, RouterInterface $router,
    TypeRepository $typeRepository) 
    { 
        $this->typeRepository = $typeRepository;

        $this->router = $router;
        $this->centerId = $security->getUser()->getCenter()->getId();
    } 


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('type', EntityType::class,[
            'label' => 'label.type',
            'class' =>  Type::class,
            'choices' => $this->typeRepository->findBy(['center' => $this->centerId], ['name' => 'ASC']),
            'choice_label' => 'name',
            'placeholder' => 'label.option',
            'mapped' => false,   
        ])
          
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Type::class,
        ]);
    }
}
