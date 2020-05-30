<?php

namespace App\Form;

use App\Entity\Post;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

/**
 * Class PostType
 * @package App\Form
 */
class PostType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', null, ['label' => 'Title']);
        if ($options['type'] == 'text')
        $builder
            ->add('body', CKEditorType::class, ['label' => 'Text'])
        ;
        if ($options['type'] == 'link')
            $builder->add('link', null, ['label' => 'Link']);
        if ($options['type'] == 'image')
            $builder->add('imageFile', VichImageType::class, ['label' => 'Image']);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
            'type' => 'text',
        ]);
    }
}
