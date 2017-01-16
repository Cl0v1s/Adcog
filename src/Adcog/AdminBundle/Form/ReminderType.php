<?php
/**
 * Created by PhpStorm.
 * User: ndrufin
 * Date: 16/01/2017
 * Time: 23:25
 */

namespace Adcog\AdminBundle\Form;

use Adcog\DefaultBundle\Entity\Reminder;
use Adcog\DefaultBundle\Form\NameTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ReminderType extends AbstractType
{
    use NameTrait;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('invert', 'choice', [
                'label' => 'Type de rappel',
                'choices' => Reminder::getTypeInverted(),
            ])
            ->add('interval', 'text', [
                'label' => 'Durée',
                'placeholder' => '5',
                'help' => 'Nombre entier représentant une durée en année',
            ])
            ->add('cycle', 'checkbox', [
                'label' => 'En boucle',
                'required' => false,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Adcog\DefaultBundle\Entity\Reminder',
        ]);
    }
}