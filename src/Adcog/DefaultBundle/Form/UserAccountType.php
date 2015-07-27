<?php

namespace Adcog\DefaultBundle\Form;

use Adcog\DefaultBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class UserAccountType
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class UserAccountType extends AbstractType
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
            ->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
                $user = $event->getData();
                if ($user instanceof User) {
                    if (null === $user->getId()) {
                        $event->getForm()->add('rawPassword', 'repeated', [
                            'type' => 'password',
                        ]);
                    }

                    if (null === $user->getSchool()) {
                        $event->getForm()->add('school', 'entity', [
                            'class' => 'Adcog\DefaultBundle\Entity\School',
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
