<?php

namespace Adcog\ValidatorBundle\Form;

use Adcog\DefaultBundle\Entity\Payment;
use Adcog\DefaultBundle\Form\NameTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class PaymentType
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class PaymentType extends AbstractType
{
    use NameTrait;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user', 'entity', [
                'class' => 'Adcog\DefaultBundle\Entity\User',
                'read_only' => true,
                'disabled' => true,
            ])
            ->add('title', 'text', [
                'label' => 'Nom',
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
                'help' => 'Nombre représentant une somme en euro (utiliser le "." pour la virgule)',
                'read_only' => true,
                'disabled' => true,
            ])
            ->add('duration', 'integer', [
                'label' => 'Durée',
                'help' => 'Nombre entier représentant une durée en année',
                'read_only' => true,
                'disabled' => true,
            ])
            ->add('invalidate', 'submit', [
                'label' => 'Invalider',
            ])
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
                $payment = $event->getData();
                if ($payment instanceof Payment) {
                    $payment->validate();
                    $invalidate = $event->getForm()->get('invalidate');
                    if ($invalidate instanceof SubmitButton) {
                        if ($invalidate->isClicked()) {
                            $payment->invalidate();
                        }
                    }
                    $event->setData($payment);
                }
            });
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Adcog\DefaultBundle\Entity\Payment',
        ]);
    }
}
