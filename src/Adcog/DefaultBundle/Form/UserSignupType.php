<?php

namespace Adcog\DefaultBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class UserSignupType
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class UserSignupType extends AbstractType
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
                'placeholder' => 'ex: john.doe@gmail.com',
            ])
            ->add('firstname', 'text', [
                'label' => 'Prénom',
                'placeholder' => 'ex: John',
            ])
            ->add('lastname', 'text', [
                'label' => 'Nom',
                'placeholder' => 'ex: DOE',
            ])
            ->add('rawPassword', 'repeated', [
                'first_options' => [
                    'label' => 'Mot de passe',
                    'placeholder' => '********',
                ],
                'second_options' => [
                    'label' => 'Mot de passe',
                    'placeholder' => '********',
                ],
                'type' => 'password',
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Adcog\DefaultBundle\Entity\User',
        ]);
    }
}
