<?php

namespace App\Form;

use App\Entity\Historia;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class HistoriaType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {



        $year = date('Y');
        $years = array();

        for ($i = 0; $i < 100; $i++) {
            $years[] = $year - $i;
        }
    

        $builder


        ->add('date', DateType::class, [
            'widget' => 'single_text',
            //'input' => 'string',
            'format' => 'dd/MM/yyyy',
            // prevents rendering it as type="date", to avoid HTML5 date pickers
            'html5' => false,
            // adds a class that can be selected in JavaScript
            'attr' => ['class' => 'js-datepicker'],
            'label' => 'label.date',      
        
            ])
            

            ->add('problem', TextType::class, [
                'label' => 'label.problem',
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
            'data_class' => Historia::class,
        ]);
    }
}
