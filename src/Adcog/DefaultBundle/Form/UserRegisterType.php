<?php

namespace Adcog\DefaultBundle\Form;

use Adcog\DefaultBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\True;

/**
 * Class UserRegisterType
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class UserRegisterType extends AbstractType
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
            ->add('school', 'entity', [
                'label' => 'Promotion',
                'placeholder' => 'Choisissez votre promotion',
                'class' => 'Adcog\DefaultBundle\Entity\School',
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
            ])
            ->add('acceptTermsOfUse', 'checkbox', [
                'label' => 'Conditions',
                'required' => true,
                'mapped' => false,
                'constraints' => [
                    new NotBlank(),
                    new True(),
                ],
            ])
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
                if (null !== $data = $event->getForm()->get('acceptTermsOfUse')->getData()) {
                    if (null !== $user = $event->getData()) {
                        if ($user instanceof User) {
                            $user->setTermsAccepted(new \DateTime());
                        }
                    }
                }
            });
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
