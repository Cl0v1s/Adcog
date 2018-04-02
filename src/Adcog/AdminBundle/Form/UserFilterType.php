<?php

namespace Adcog\AdminBundle\Form;

use Adcog\DefaultBundle\Entity\User;
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
            ->add('username', 'text', [
                'label' => 'Email',
                'placeholder' => 'john.doe@gmail.com',
                'required' => false,
            ])
            ->add('firstname', 'text', [
                'label' => 'Prénom',
                'placeholder' => 'John',
                'required' => false,
            ])
            ->add('lastname', 'text', [
                'label' => 'Nom',
                'placeholder' => 'DOE',
                'required' => false,
            ])
            ->add('gender', 'choice',[
                'label' => 'Sexe',
                'placeholder' => "Choisissez le Sexe",
                'choices' => array(
                    'H' => "Homme",
                    'F' => "Femme"
                ),
                'required' => false,
            ])
            ->add('nationality', 'country', [
                'label' => 'Nationalité',
                'placeholder' => 'Choisissez la nationalité',
                'preferred_choices' => array('FR'),
                'required' => false,
            ])
            ->add('role', 'choice', [
                'label' => 'Rôle',
                'choices' => User::getRoleNameList(),
                'required' => false,
            ])
            ->add('school', 'entity', [
                'label' => 'Promotion',
                'class' => 'Adcog\DefaultBundle\Entity\School',
                'required' => false,
            ])
            ->add('address', 'text', [
                'label' => 'Adresse',
                'placeholder' => '109 avenue Roul',
                'required' => false,
            ])
            ->add('zip', 'text', [
                'label' => 'Code postal',
                'placeholder' => '33400',
                'required' => false,
            ])
            ->add('city', 'text', [
                'label' => 'Ville',
                'placeholder' => 'Talence',
                'required' => false,
            ])
            ->add('country', 'text', [
                'label' => 'Pays',
                'placeholder' => 'France',
                'required' => false,
            ]);
    }
}
