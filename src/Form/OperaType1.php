<?php

namespace App\Form;

use App\Entity\Opera;
use App\Entity\Type;
use App\Entity\Treatment;
use App\Entity\Place;
use App\Entity\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;


use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

use Symfony\Component\Security\Core\Security;

use App\Repository\TypeRepository;
use App\Repository\PlaceRepository;
use App\Repository\UserRepository;



use Symfony\Component\Routing\RouterInterface;

use Symfony\Component\Form\Extension\Core\Type\DateType;

class OperaType1 extends AbstractType
{

   private $typeRepository;
   private $placeRepository;
   private $userRepository;

   private $router;
   private $centerId;

    public function __construct(Security $security, RouterInterface $router,
    TypeRepository $typeRepository, PlaceRepository $placeRepository, UserRepository $userRepository) 
    { 
        $this->typeRepository = $typeRepository;
        $this->placeRepository = $placeRepository;
        $this->userRepository = $userRepository;

        $this->router = $router;
        $this->centerId = $security->getUser()->getCenter()->getId();
    } 

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {

            $form = $event->getForm();

            $form

            ->add('type', EntityType::class,[
                'label' => 'label.type',
                'class' =>  Type::class,
                'choices' => $this->typeRepository->findBy(['center' => $this->centerId], ['name' => 'ASC']),
                'choice_label' => 'name',
                'placeholder' => 'label.option',
                'mapped' => false,  
                
            ])

            ->add('treatment', EntityType::class,[
                'label' => 'label.treatment',
                'class' =>  Treatment::class,
                'choices' => [],
                'choice_label' => 'name',
                'placeholder' => 'label.option',
            ])

            ->add('place', EntityType::class,[
                'label' => 'label.place',
                'class' =>  Place::class,
                'choices' => $this->placeRepository->findBy([
                    'center' => $this->centerId,
                    'enabled' => true
                ], ['name' => 'ASC']),
                'choice_label' => 'name',
                'placeholder' => 'label.option',
            ])

            ->add('user', EntityType::class,[
                'label' => 'Doctor',
                'class' =>  User::class,
                'choices' => $this->userRepository->findBy([
                    'center' => $this->centerId, 'medic' => true, 'enabled' => true
                ], ['lastname' => 'ASC']),
                'choice_label' => 'lastname',
                'placeholder' => 'label.option',
            ])

            ->add('made_at', DateType::class, [
                'widget' => 'single_text',
                //'input' => 'string',
                'format' => 'dd/MM/yyyy',
                // prevents rendering it as type="date", to avoid HTML5 date pickers
                'html5' => false,
                // adds a class that can be selected in JavaScript
                'attr' => ['class' => 'js-datepicker'],
            ]);

        });

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Opera::class,
            'attr' => [
                'data-autocomplete-url' => $this->router->generate('treatments_get')
            ]
         
        ]);
    }
}


