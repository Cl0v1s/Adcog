<?php

namespace Adcog\DefaultBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Class UserPasswordType
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class UserPasswordType extends AbstractType
{
    use NameTrait;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
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
            ])
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
                $user = $event->getData();
                if ($user instanceof $user) {
                    $user->setPassword(uniqid());
                }
            });
    }
}
