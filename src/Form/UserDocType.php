<?php

namespace App\Form;

use App\Entity\UserDoc;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Vich\UploaderBundle\Form\Type\VichFileType;

class UserDocType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('docFile', VichFileType::class, [
            'required' => false,
            'label' => ' ',
            'allow_delete' => true, 
            'download_uri' => true,
            'download_label' => '...',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserDoc::class,
        ]);
    }
}
