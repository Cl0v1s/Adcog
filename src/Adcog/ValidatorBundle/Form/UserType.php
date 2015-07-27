<?php

namespace Adcog\ValidatorBundle\Form;

use Adcog\DefaultBundle\Entity\User;
use Adcog\DefaultBundle\Form\NameTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\SubmitButton;
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
                'read_only' => true,
                'disabled' => true,
            ])
            ->add('firstname', 'text', [
                'label' => 'Prénom',
            ])
            ->add('lastname', 'text', [
                'label' => 'Nom',
            ])
            ->add('school', 'entity', [
                'label' => 'Promotion',
                'class' => 'Adcog\DefaultBundle\Entity\School',
            ])
            ->add('address', 'textarea', [
                'label' => 'Adresse',
                'required' => false,
                'read_only' => true,
                'disabled' => true,
            ])
            ->add('zip', 'text', [
                'label' => 'Code postal',
                'required' => false,
                'attr' => [
                    'data-ws' => $this->router->generate('user_user_api_zip'),
                    'autocomplete' => 'off',
                ],
            ])
            ->add('city', 'text', [
                'label' => 'Ville',
                'required' => false,
                'attr' => [
                    'data-ws' => $this->router->generate('user_user_api_city'),
                    'autocomplete' => 'off',
                ],
            ])
            ->add('country', 'text', [
                'label' => 'Pays',
                'required' => false,
                'attr' => [
                    'data-ws' => $this->router->generate('user_user_api_country'),
                    'autocomplete' => 'off',
                ],
            ])
            ->add('phone', 'text', [
                'label' => 'Téléphone',
                'required' => false,
                'read_only' => true,
                'disabled' => true,
            ])
            ->add('website', 'text', [
                'label' => 'Site web',
                'required' => false,
                'read_only' => true,
                'disabled' => true,
            ])
            ->add('invalidate', 'submit', [
                'label' => 'Invalider',
            ])
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
                $user = $event->getData();
                if ($user instanceof User) {
                    $user->validate();
                    $invalidate = $event->getForm()->get('invalidate');
                    if ($invalidate instanceof SubmitButton) {
                        if ($invalidate->isClicked()) {
                            $user->invalidate();
                        }
                    }
                    $event->setData($user);
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
