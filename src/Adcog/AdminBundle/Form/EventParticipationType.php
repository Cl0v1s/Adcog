<?php

namespace Adcog\AdminBundle\Form;

use Adcog\DefaultBundle\Entity\EventParticipation;
use Adcog\DefaultBundle\Form\NameTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class EventParticipationType
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class EventParticipationType extends AbstractType
{
    use NameTrait;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user', 'entity', [
                'label' => 'Utilisateur',
                'class' => 'Adcog\DefaultBundle\Entity\User',
            ])
            ->add('event', 'entity', [
                'label' => 'Evenement',
                'class' => 'Adcog\DefaultBundle\Entity\Event',
            ])
            ->add('type', 'choice', [
                'label' => 'Type',
                'choices' => EventParticipation::getTypeNameList(),
            ])
            ->add('comment', 'textarea', [
                'label' => 'Commentaire',
                'required' => false,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Adcog\DefaultBundle\Entity\EventParticipation',
        ]);
    }
}
