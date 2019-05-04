<?php

namespace App\Form;

use App\Entity\User;

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

/**
 * Defines the form used to edit an user.
 *
 * @author Romain Monteil <monteil.romain@gmail.com>
 */
class UserType extends AbstractType
{

    private $isAdmin;
    private $isOwner;
    private $isSuper;
    
    public function __construct(Security $security){

        $this->isAdmin = false;        
        $this->isOwner = false;
        $this->isSuper = false;

        $this->requestingUserId  = $security->getUser()->getId();
       
        if (in_array('ROLE_ADMIN', $security->getUser()->getRoles()))
        {
            $this->isAdmin = true;
        }

        if (in_array('ROLE_SUPER_ADMIN', $security->getUser()->getRoles())) {
            $this->isAdmin = true;
            $this->isSuper = true;
        }

        if($security->getUser()->getCenterUser()){
            $this->isOwner = true;
        }

    }        


    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {

            $user = $event->getData();
            $form = $event->getForm();

            if($this->isAdmin){

                $form->add('email', EmailType::class, [
                    'label' => 'label.email'
                ]);

                $form->add('medic', CheckboxType::class, [
                    'label' => 'Medic Role',
                    'required' => false,
                ]);

            } else {

                $form->add('email', TextType::class, [
                    'label' => 'label.email',
                    'disabled' => true,
                ]);
    
            }

            if($user->getId() <> $this->requestingUserId){ 
                // no se puede auto-cambiar a disabled o auto-revocar poderes

                $form->add('enabled', CheckboxType::class, [
                    'label' => 'label.enabled',
                    'required' => false,
                ]);

                if($this->isSuper || $this->isOwner){
                
                    $form->add('admin', CheckboxType::class, [
                        'label' => 'title.center_cpanel',
                        'required' => false,
                    ]);

                    if($this->isSuper){

                        $form->add('center_user', CheckboxType::class, [
                            'label' => 'title.center_owner',
                            'required' => false,
                        ]);
                    }
                }
            }

            $form->add('firstname', TextType::class, [
                'label' => 'label.firstname',
            ])
            ->add('lastname', TextType::class, [
                'label' => 'label.lastname',
            ])
            ->add('tel', TelType::class, [
                'label' => 'label.tel',
                'required' => false,
            ]);
            
        });   
  
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
