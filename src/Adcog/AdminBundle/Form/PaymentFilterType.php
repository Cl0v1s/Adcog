<?php

namespace Adcog\AdminBundle\Form;

use Adcog\DefaultBundle\Entity\Payment;
use Adcog\DefaultBundle\Form\NameTrait;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;

/**
 * Class PaymentFilterType
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class PaymentFilterType extends AbstractType
{
    use NameTrait;
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
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
            ->add('type', 'choice', [
                'label' => 'Type de payment',
                'choices' => Payment::getTypeNameList(),
                'required' => false,
            ]);
    }
}
