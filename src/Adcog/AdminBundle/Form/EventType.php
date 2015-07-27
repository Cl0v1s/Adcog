<?php

namespace Adcog\AdminBundle\Form;

use Adcog\DefaultBundle\Entity\Event;
use Adcog\DefaultBundle\Form\NameTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class EventType
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class EventType extends AbstractType
{
    use NameTrait;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', [
                'label' => 'Nom',
            ])
            ->add('date', 'adcog_date_field', [
                'label' => 'Date',
            ])
            ->add('description', 'adcog_wysiwyg_field', [
                'label' => 'Description',
            ])
            ->add('program', 'adcog_wysiwyg_field', [
                'label' => 'Programme',
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Adcog\DefaultBundle\Entity\Event',
        ]);
    }
}
