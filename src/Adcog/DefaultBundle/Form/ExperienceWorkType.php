<?php

namespace Adcog\DefaultBundle\Form;

use Adcog\DefaultBundle\Entity\ExperienceWork;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class ExperienceWorkType
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class ExperienceWorkType extends AbstractType
{
    use NameTrait;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('workPosition', 'text', [
                'label' => 'Poste',
                'placeholder' => 'Ingénieur en Cognitique',
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
            'data_class' => 'Adcog\DefaultBundle\Entity\ExperienceWork',
        ]);
    }
}