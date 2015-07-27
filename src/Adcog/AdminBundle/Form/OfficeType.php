<?php

namespace Adcog\AdminBundle\Form;

use Adcog\DefaultBundle\Entity\Office;
use Adcog\DefaultBundle\Form\NameTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class OfficeType
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class OfficeType extends AbstractType
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
            ])
            ->add('role', 'entity', [
                'label' => 'Rôle',
                'class' => 'Adcog\DefaultBundle\Entity\Role',
            ])
            ->add('started', 'adcog_date_field', [
                'label' => 'Date de début',
            ])
            ->add('ended', 'adcog_date_field', [
                'label' => 'Date de fin',
                'required' => false,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Adcog\DefaultBundle\Entity\Office',
        ]);
    }
}
