<?php

namespace Adcog\DefaultBundle\Form;

use Adcog\DefaultBundle\Entity\PaymentPaypal;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class PaymentPaypalType
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class PaymentPaypalType extends AbstractType
{
    use NameTrait;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('tokenId', 'text', [
                'label' => 'Token Paypal',
                'required' => false,
            ])
            ->add('inherited', 'adcog_payment', [
                'inherit_data' => true,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Adcog\DefaultBundle\Entity\PaymentPaypal',
        ]);
    }
}
