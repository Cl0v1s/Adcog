<?php

namespace Adcog\BloggerBundle\Form;

use Adcog\DefaultBundle\Form\NameTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class FaqFilterType
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class FaqFilterType extends AbstractType
{
    use NameTrait;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('category', 'entity', [
                'label' => 'Catégorie',
                'class' => 'Adcog\DefaultBundle\Entity\FaqCategory',
                'required' => false,
            ])
            ->add('title', 'text', [
                'label' => 'Titre',
                'required' => false,
            ])
            ->add('text', 'text', [
                'label' => 'Contenu',
                'required' => false,
            ]);
    }
}
