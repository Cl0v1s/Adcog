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
            ->add('name', 'text', [
                'label' => 'Titre, Mots cléfs', 
                'required' => false,
            ])
            ->add('type', 'choice', [
                'label' => 'Type',
                'placeholder' => 'Tous les types d\'expérience',
                'required' => false,
                'choices' => Experience::getTypeNameList(),
            ])
            ->add('place', 'text', [
                'label' => 'Lieu (Ville, Code postal, ...)',
                'placeholder' => 'ex: Talence',
                'required' => false,
            ])
            ->add('country', 'country', [
                'label' => 'Pays',
                'preferred_choices' => array('FR'),
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
