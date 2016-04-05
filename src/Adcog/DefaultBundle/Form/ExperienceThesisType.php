<?php

namespace Adcog\DefaultBundle\Form;

use Adcog\DefaultBundle\Entity\ExperienceThesis;
use /** @noinspection PhpUnusedAliasInspection */
    Adcog\DefaultBundle\Form\NameTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class ExperienceThesisType
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class ExperienceThesisType extends AbstractType
{
    use NameTrait;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('thesisType', 'text', [
                'label' => 'Type',
                'placeholder' => 'ex: CIFRE',
            ])
            ->add('thesisDiscipline', 'text', [
                'label' => 'Discipline',
                'placeholder' => 'ex: Cognitique',
            ])
            ->add('thesisSubject', 'text', [
                'label' => 'Sujet',
                'placeholder' => 'ex: Analyse des comportements',
            ])
            ->add('experience', 'adcog_experience', [
                'inherit_data' => true,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Adcog\DefaultBundle\Entity\ExperienceThesis',
        ]);
    }
}
