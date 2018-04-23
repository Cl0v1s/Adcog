<?php

namespace Adcog\AdminBundle\Form;

use Adcog\DefaultBundle\Form\NameTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class EmployerFilterType
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class EmployerFilterType extends AbstractType
{
    use NameTrait;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', [
                'label' => 'Nom',
                'required' => false,
            ])
            ->add('address', 'text', [
                'label' => 'Adresse',
                'required' => false,
            ])
            ->add('zip', 'text', [
                'label' => 'Code postal',
                'required' => false,
            ])
            ->add('city', 'text', [
                'label' => 'Ville',
                'required' => false,
            ])
            ->add('country', 'text', [
                'label' => 'Pays',
                'required' => false,
            ])
            ->add('employerType','entity', [
                'label' => 'Type d\'entreprise',
                'placeholder' => 'Choisissez le type d\'entreprise',
                'class' => 'Adcog\DefaultBundle\Entity\EmployerType',
                'required' => false,
            ])
            ->add('phone', 'text', [
                'label' => 'Téléphone',
                'required' => false,
            ])
            ->add('website', 'text', [
                'label' => 'Site web',
                'required' => false,
            ])
            ->add('email', 'email', [
                'label' => 'Email',
                'required' => false,
            ]);
    }
}
