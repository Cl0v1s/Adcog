<?php

namespace Adcog\AdminBundle\Form;

use Adcog\DefaultBundle\Entity\EventParticipation;
use Adcog\DefaultBundle\Form\NameTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class EventParticipationFilterType
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class EventParticipationFilterType extends AbstractType
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
                'required' => false,
                'class' => 'Adcog\DefaultBundle\Entity\User',
            ])
            ->add('event', 'entity', [
                'label' => 'Evenement',
                'required' => false,
                'class' => 'Adcog\DefaultBundle\Entity\Event',
            ])
            ->add('type', 'choice', [
                'label' => 'Type',
                'choices' => EventParticipation::getTypeNameList(),
                'required' => false,
            ]);
    }
}
