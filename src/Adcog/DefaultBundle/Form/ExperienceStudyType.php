<?php

namespace Adcog\DefaultBundle\Form;

use Adcog\DefaultBundle\Entity\ExperienceStudy;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class ExperienceStudyType
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class ExperienceStudyType extends AbstractType
{
    use NameTrait;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('studyDiploma', 'text', [
                'label' => 'Diplôme',
                'placeholder' => 'Diplôme d\'Ingénieur Robotique',
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
            'data_class' => 'Adcog\DefaultBundle\Entity\ExperienceStudy',
        ]);
    }
}
