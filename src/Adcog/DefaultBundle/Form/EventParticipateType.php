<?php

namespace Adcog\DefaultBundle\Form;

use Adcog\DefaultBundle\Entity\EventParticipation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class EventParticipateType
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class EventParticipateType extends AbstractType
{
    use NameTrait;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', 'choice', [
                'label' => 'Rôle',
                'choices' => [
                    EventParticipation::TYPE_ORG => 'Organisateur',
                    EventParticipation::TYPE_SPEAKER => 'Présentateur',
                    EventParticipation::TYPE_SPEC => 'Spectateur',
                ],
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
