<?php

namespace Adcog\AdminBundle\Form;

use Adcog\DefaultBundle\Entity\Price;
use Adcog\DefaultBundle\Form\NameTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class PriceType
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class PriceType extends AbstractType
{
    use NameTrait;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', [
                'label' => 'Nom',
            ])
            ->add('description', 'textarea', [
                'label' => 'Description',
            ])
            ->add('amount', 'text', [
                'label' => 'Montant',
                'placeholder' => '5',
                'help' => 'Nombre représentant une somme en euro (utiliser le "." pour la virgule)',
            ])
            ->add('duration', 'integer', [
                'label' => 'Durée',
                'placeholder' => '5',
                'help' => 'Nombre entier représentant une durée en année',
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Adcog\DefaultBundle\Entity\Price',
        ]);
    }
}
