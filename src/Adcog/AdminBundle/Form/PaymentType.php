<?php

namespace Adcog\AdminBundle\Form;

use Adcog\DefaultBundle\Entity\Payment;
use Adcog\DefaultBundle\Entity\User;
use Adcog\DefaultBundle\Form\NameTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class PaymentType
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class PaymentType extends AbstractType
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
                'class' => 'Adcog\DefaultBundle\Entity\User'
            ])
            ->add('title', 'text', [
                'label' => 'Titre',
            ])
            ->add('description', 'textarea', [
                'label' => 'Description',
            ])
            ->add('amount', 'integer', [
                'label' => 'Montant',
            ])
            ->add('duration', 'integer', [
                'label' => 'Durée',
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Adcog\DefaultBundle\Entity\Payment',
        ]);
    }
}
