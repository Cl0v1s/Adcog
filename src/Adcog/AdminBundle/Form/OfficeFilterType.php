<?php

namespace Adcog\AdminBundle\Form;

use Adcog\DefaultBundle\Form\NameTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class OfficeFilterType
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class OfficeFilterType extends AbstractType
{
    use NameTrait;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user', 'entity', [
                'label' => 'Utilisateur',
                'class' => 'Adcog\DefaultBundle\Entity\User',
                'required' => false,
            ])
            ->add('role', 'entity', [
                'label' => 'Rôle',
                'class' => 'Adcog\DefaultBundle\Entity\Role',
                'required' => false,
            ])
            ->add('active', 'adcog_ternary', [
                'label' => 'Actif',
                'required' => false,
            ]);
    }
}
