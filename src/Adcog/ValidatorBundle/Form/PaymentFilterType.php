<?php

namespace Adcog\ValidatorBundle\Form;

use Adcog\DefaultBundle\Entity\Payment;
use Adcog\DefaultBundle\Form\NameTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class PaymentFilterType
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class PaymentFilterType extends AbstractType
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
            ->add('type', 'choice', [
                'label' => 'Type',
                'choices' => Payment::getTypeNameList(),
                'required' => false,
            ])
            ->add('not_validated', 'adcog_ternary', [
                'label' => 'En attente de validation',
            ]);
    }
}
