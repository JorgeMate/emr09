<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
//use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder;




class ResettingFormType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('newPassword', RepeatedType::class, [
            'type' => PasswordType::class,
            'constraints' => [
                new NotBlank(),
                new Length([
                    'min' => 5,
                    'max' => BCryptPasswordEncoder::MAX_PASSWORD_LENGTH,
                ]),
            ],
            'first_options' => [
                'label' => 'label.new_password',
            ],
            'second_options' => [
                'label' => 'label.new_password_confirm',
            ],
        ])
    ;

    }
}
