<?php

namespace Adcog\MemberBundle\Form;

use Adcog\DefaultBundle\Entity\Experience;
use Adcog\DefaultBundle\Form\NameTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ExperienceFilterType
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class ExperienceFilterType extends AbstractType
{
    use NameTrait;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', 'choice', [
                'label' => 'Type',
                'placeholder' => 'Tous les types d\'expérience',
                'required' => false,
                'choices' => Experience::getTypeNameList(),
            ])
            ->add('description', 'text', [
                'label' => 'Mots clés',
                'placeholder' => 'ex: Aviation, IHM, ...',
                'required' => false,
            ])
            ->add('sectors', 'entity', [
                'label' => 'Secteurs',
                'placeholder' => 'ex: Informatique, Robotique, ...',
                'required' => false,
                'multiple' => true,
                'class' => 'Adcog\DefaultBundle\Entity\Sector',
            ]);
    }
}
