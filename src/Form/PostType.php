<?php

namespace App\Form;

use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', null, ['label' => 'Titre']);
        if ($options['type'] == 'text')
            $builder->add('body', null, ['label' => 'Texte']);
        if ($options['type'] == 'link')
            $builder->add('link', null, ['label' => 'Lien']);
        if ($options['type'] == 'image')
            $builder->add('imageFile', VichImageType::class, ['label' => 'Image']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
            'type' => 'text',
        ]);
    }
}
