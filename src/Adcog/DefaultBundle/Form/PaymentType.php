<?php

namespace Adcog\DefaultBundle\Form;

use Adcog\DefaultBundle\Entity\Payment;
use Adcog\DefaultBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class PaymentType
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class PaymentType extends AbstractType
{
    use NameTrait;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', [
                'label' => 'Titre',
                'read_only' => true,
                'disabled' => true,
            ])
            ->add('description', 'textarea', [
                'label' => 'Description',
                'read_only' => true,
                'disabled' => true,
            ])
            ->add('amount', 'integer', [
                'label' => 'Montant',
                'placeholder' => '5',
                'help' => 'Nombre représentant une somme en euro (utiliser le "." pour la virgule)',
                'read_only' => true,
                'disabled' => true,
            ])
            ->add('duration', 'integer', [
                'label' => 'Durée',
                'placeholder' => '5',
                'help' => 'Nombre entier représentant une durée en année',
                'read_only' => true,
                'disabled' => true,
            ])
            ->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
                if ($this->authorizationChecker->isGranted(User::ROLE_ADMIN)) {
                    $event->getForm()->add('user', 'entity', [
                        'label' => 'Utilisateur',
                        'help' => 'Ce champ n\'est disponible que pour les administrateurs',
                        'class' => 'Adcog\DefaultBundle\Entity\User',
                    ]);
                }
            });;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Payment::class,
        ]);
    }
}
