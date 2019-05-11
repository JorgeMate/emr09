<?php

namespace App\Form;

use App\Entity\Center;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Security\Core\Security;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class CenterType extends AbstractType
{

    private $isSuper;
    private $userRequestsCenterId;

    public function __construct(Security $security)
    {
        $this->isSuper = false;
        $this->userRequestsCenterId = $security->getUser()->getCenter()->getId();

        if (in_array('ROLE_SUPER_ADMIN', $security->getUser()->getRoles())) {
            $this->isSuper = true;
        }
    }

   

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {

            $center = $event->getData();
            $form = $event->getForm();
            
            if( $this->isSuper && $center->getId() <> $this->userRequestsCenterId && $center->getId() ){

                // 

                // Solo para el superusuario que no edita su mismo centro y no para centros nuevos

                $form->add('enabled', CheckboxType::class, [
                    'label' => 'label.enabled',
                    'required' => false,
                ]);

                $form->add('ssaas_account_name', TextType::class, [
                    'label' => 'Ssaas Account name',
                    'required' => false,
                ]);
                $form->add('ssaas_api_key', TextType::class, [
                    'label' => 'Ssaas API key',
                    'required' => false,
                ]);

            }

            $form->add('name', TextType::class, [
                'label' => 'label.name',
            ])

            ->add('contact_person', TextType::class, [
                'label' => 'label.contact_person',
                'required' => false,
            ])

            ->add('tel', TelType::class, [
                'label' => 'label.tel',
                'required' => false,
            ])

            ->add('email', EmailType::class, [
                'label' => 'label.email',
            ])

            ->add('address', TextType::class, [
                'label' => 'label.address',
                'required' => false,
            ])

            ->add('postcode', TextType::class, [
                'label' => 'label.postcode',
                'required' => false,
            ])
            
            ->add('city', TextType::class, [
                'label' => 'label.city',
                'required' => false,
            ]);

        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Center::class,
        ]);
    }
}
