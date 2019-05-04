<?php

namespace App\Form;

use App\Entity\StoredImg;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

//use Symfony\Component\Form\Extension\Core\Type\FileType;
//use Symfony\Component\Validator\Constraints\Image;



use Vich\UploaderBundle\Form\Type\VichImageType;


class StoredImgType extends AbstractType
{


    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        // ...

        $builder->add('imageFile', VichImageType::class, [
            'required' => false,
            'label' => ' ',
            'allow_delete' => true,
            'download_label' => '...',
            'download_uri' => true,
            'image_uri' => true,
            'imagine_pattern' => '...',


        ]);
    }




    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => StoredImg::class,
        ]);
    }
}
