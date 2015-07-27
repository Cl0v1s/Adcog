<?php

namespace Adcog\ValidatorBundle\Form;

use Adcog\DefaultBundle\Form\NameTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class CommentFilterType
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class CommentFilterType extends AbstractType
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
                'class' => 'Adcog\DefaultBundle\Entity\User',
                'required' => false,
            ])
            ->add('post', 'entity', [
                'label' => 'Article',
                'class' => 'Adcog\DefaultBundle\Entity\Post',
                'required' => false,
            ])
            ->add('text', 'text', [
                'label' => 'Commentaire',
                'required' => false,
            ])
            ->add('not_validated', 'adcog_ternary', [
                'label' => 'En attente de validation',
            ]);
    }
}
