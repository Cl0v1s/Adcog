<?php

namespace Adcog\ValidatorBundle\Form;

use Adcog\DefaultBundle\Form\NameTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class BookmarkFilterType
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class BookmarkFilterType extends AbstractType
{
    use NameTrait;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('author', 'entity', [
                'label' => 'Auteur',
                'class' => 'Adcog\DefaultBundle\Entity\User',
                'required' => false,
            ])
            ->add('title', 'text', [
                'label' => 'Titre',
                'required' => false,
            ])
            ->add('href', 'text', [
                'label' => 'Lien',
                'required' => false,
            ])
            ->add('not_validated', 'adcog_ternary', [
                'label' => 'En attente de validation',
            ]);
    }
}
