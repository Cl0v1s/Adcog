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
            ->add('year', 'integer', [
                'label' => 'Années',
                'required' => false,
                'attr' => ['min' => '0'],
                'empty_data'  => '0',
                'help' => 'Nombre entier représentant une durée en année',
            ])
            ->add('month', 'integer', [
                'label' => 'Mois',
                'required' => false,
                'attr' => ['min' => '0'],
                'empty_data'  => '0',
                'help' => 'Nombre entier représentant une durée en mois',
            ])
            ->add('days', 'integer', [
                'label' => 'Jours',
                'required' => false,
                'attr' => ['min' => '0'],
                'empty_data'  => '0',
                'help' => 'Nombre entier représentant une durée en jours',
            ])
            ->add('cycle', 'checkbox', [
                'label' => 'Récurrent sans fin',
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