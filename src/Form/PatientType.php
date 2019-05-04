<?php

namespace App\Form;

use App\Entity\Patient;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use App\Form\Type\TagsInputType;

use App\Entity\Insurance;
use App\Entity\Source;

use Symfony\Component\Security\Core\Security;

use App\Repository\SourceRepository;
use App\Repository\InsuranceRepository;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;



class PatientType extends AbstractType
{

    private $sourceRepository;
    private $insuranceRepository;
    private $centerId;
 
     public function __construct(Security $security, SourceRepository $sourceRepository, InsuranceRepository $insuranceRepository) 
     { 
        $this->sourceRepository = $sourceRepository;
        $this->insuranceRepository = $insuranceRepository;
        $this->centerId = $security->getUser()->getCenter()->getId();
     } 

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {

            $patient = $event->getData();
            $form = $event->getForm();

            //var_dump($patient);die;

            $form
            ->add('firstname', TextType::class, [
                'label' => 'label.firstname',
                'required' => false,
            ])
            ->add('lastname', TextType::class, [
                'label' => 'label.lastname',
                'required' => true,                
            ])
            ->add('sex', ChoiceType::class,[
                'label' => 'label.sex',
                'choices' => [
                    'label.male' => 0,
                    'label.female' => 1,                    
                ]
            ])
            ->add('dateOfBirth', DateType::class, [
                'widget' => 'single_text',
                //'input' => 'string',
                'format' => 'dd/MM/yyyy',
                // prevents rendering it as type="date", to avoid HTML5 date pickers
                'html5' => false,
                // adds a class that can be selected in JavaScript
                'attr' => ['class' => 'js-datepicker'],
                'label' => 'label.birthdate',      
                
            ])
            ->add('insurance', EntityType::class,[
                'label' => 'entity.title_ins',
                'class' =>  Insurance::class,
                'choices' => $this->insuranceRepository->findBy(['center' => $this->centerId], ['name' => 'ASC']),
                'choice_label' => 'name',
                'placeholder' => 'label.option',
                
            ])
            ->add('source', EntityType::class,[
                'label' => 'entity.title_sou',
                'class' =>  Source::class,
                'choices' => $this->sourceRepository->findBy(['center' => $this->centerId], ['name' => 'ASC']),
                'choice_label' => 'name',
                'placeholder' => 'label.option',
            ]);

            if($patient->getId()){

                $form
                ->add('address', TextType::class, [
                    'label' => 'label.address',
                    'required' => false,                
                ])
                ->add('house_no', TextType::class, [
                    'label' => 'label.house_no',
                    'required' => false,                
                ])
                ->add('house_txt', TextType::class, [
                    'label' => 'label.house_txt',
                    'required' => false,                
                ])
                ->add('city', TextType::class, [
                    'label' => 'label.city',                
                    'required' => false,                
                ])
                ->add('postcode', TextType::class, [
                    'label' => 'label.postcode',                
                    'required' => false,                
                ])

                ->add('email', TextType::class, [
                    'label' => 'label.email',                
                    'required' => false,                
                ])
                ->add('tel', TelType::class, [
                    'label' => 'label.tel',                
                    'required' => false,                
                ])
                ->add('cel', TelType::class, [
                    'label' => 'label.cel',                
                    'required' => false,                
                ])

                ->add('contact', TextType::class, [
                    'label' => 'label.contact',
                    'required' => false,                
                ])
                ->add('tel_con', TelType::class, [
                    'label' => 'label.tel',
                    'required' => false,                
                ])
                ->add('doctor', TextType::class, [
                    'label' => 'label.doc',
                    'required' => false,                
                ])
                ->add('tel_doc', TelType::class, [
                    'label' => 'label.tel',
                    'required' => false,                
                ])
                
                ->add('notes', TextareaType::class, [
                    'label' => 'label.notes',
                    'required' => false,  
                    'attr' => ['rows' => '5'],              
                ])

                ->add('tags', TagsInputType::class, [
                    'label' => 'label.tags',
                    'required' => false,
                ]);

            }
                    
        }); 
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Patient::class,
        ]);
    }
}
