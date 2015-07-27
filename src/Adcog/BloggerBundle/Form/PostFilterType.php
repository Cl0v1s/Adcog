<?php

namespace Adcog\BloggerBundle\Form;

use Adcog\DefaultBundle\Form\NameTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class PostFilterType
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class PostFilterType extends AbstractType
{
    use NameTrait;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', [
                'label' => 'Titre',
                'required' => false,
            ])
            ->add('author', 'entity', [
                'label' => 'Auteur',
                'required' => false,
                'class' => 'Adcog\DefaultBundle\Entity\User',
            ])
            ->add('not_validated', 'adcog_ternary', [
                'label' => 'En attente de publication',
            ]);
    }
}
