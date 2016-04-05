<?php

namespace Adcog\ValidatorBundle\Form;

use Adcog\DefaultBundle\Form\NameTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class UserFilterType
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class UserFilterType extends AbstractType
{
    use NameTrait;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', 'text', [
                'label' => 'Prénom',
                'placeholder' => 'ex: John',
                'required' => false,
            ])
            ->add('lastname', 'text', [
                'label' => 'Nom',
                'placeholder' => 'ex: DOE',
                'required' => false,
            ])
            ->add('school', 'entity', [
                'label' => 'Promotion',
                'class' => 'Adcog\DefaultBundle\Entity\School',
                'required' => false,
            ])
            ->add('not_validated', 'adcog_ternary', [
                'label' => 'En attente de validation',
            ]);
    }
}
