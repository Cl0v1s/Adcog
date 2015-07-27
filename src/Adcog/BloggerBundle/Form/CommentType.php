<?php

namespace Adcog\BloggerBundle\Form;

use Adcog\DefaultBundle\Entity\Comment;
use Adcog\DefaultBundle\Form\NameTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class CommentType
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class CommentType extends AbstractType
{
    use NameTrait;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('author', 'entity', [
                'label' => 'Utilisateur',
                'class' => 'AdcogDefaultBundle:User',
            ])
            ->add('post', 'entity', [
                'label' => 'Article',
                'class' => 'AdcogDefaultBundle:Post',
            ])
            ->add('text', 'textarea', [
                'label' => 'Commentaire',
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Adcog\DefaultBundle\Entity\Comment',
        ]);
    }
}
