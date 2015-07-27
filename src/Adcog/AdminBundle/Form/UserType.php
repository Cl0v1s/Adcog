<?php

namespace Adcog\AdminBundle\Form;

use Adcog\DefaultBundle\Entity\User;
use Adcog\DefaultBundle\Form\NameTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class UserType
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class UserType extends AbstractType
{
    use NameTrait;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', 'email', [
                'label' => 'Email',
                'placeholder' => 'john.doe@gmail.com',
            ])
            ->add('firstname', 'text', [
                'label' => 'Prénom',
                'placeholder' => 'John',
            ])
            ->add('lastname', 'text', [
                'label' => 'Nom',
                'placeholder' => 'DOE',
            ])
            ->add('school', 'entity', [
                'label' => 'Promotion',
                'class' => 'Adcog\DefaultBundle\Entity\School',
            ])
            ->add('address', 'textarea', [
                'label' => 'Adresse',
                'placeholder' => '109 avenue Roul',
                'required' => false,
            ])
            ->add('zip', 'text', [
                'label' => 'Code postal',
                'placeholder' => '33400',
                'required' => false,
                'attr' => [
                    'data-ws' => $this->router->generate('user_user_api_zip'),
                    'autocomplete' => 'off',
                ],
            ])
            ->add('city', 'text', [
                'label' => 'Ville',
                'placeholder' => 'Talence',
                'required' => false,
                'attr' => [
                    'data-ws' => $this->router->generate('user_user_api_city'),
                    'autocomplete' => 'off',
                ],
            ])
            ->add('country', 'text', [
                'label' => 'Pays',
                'placeholder' => 'France',
                'required' => false,
                'attr' => [
                    'data-ws' => $this->router->generate('user_user_api_country'),
                    'autocomplete' => 'off',
                ],
            ])
            ->add('phone', 'text', [
                'label' => 'Téléphone',
                'placeholder' => '0566778899',
                'required' => false,
            ])
            ->add('website', 'text', [
                'label' => 'Site web',
                'placeholder' => 'http://adcog.fr',
                'required' => false,
            ])
            ->add('enabled', 'checkbox', [
                'label' => 'Compte activé',
                'required' => false,
            ])
            ->add('role', 'choice', [
                'label' => 'Rôle',
                'choices' => User::getRoleNameList(),
                'required' => false,
            ])
            ->add('accountExpired', 'adcog_date_field', [
                'label' => 'Compte expiré le',
                'required' => false,
            ])
            ->add('accountLocked', 'adcog_date_field', [
                'label' => 'Compte bloqué le',
                'required' => false,
            ])
            ->add('credentialsExpired', 'adcog_date_field', [
                'label' => 'Mot de passe expiré le',
                'required' => false,
            ])
            ->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
                $user = $event->getData();
                if ($user instanceof User) {
                    if (null === $user->getId()) {
                        $event->getForm()->add('rawPassword', 'repeated', [
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
