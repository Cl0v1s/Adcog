<?php

namespace Adcog\DefaultBundle\Form;

use Adcog\DefaultBundle\Entity\ExperienceInternship;
use Adcog\DefaultBundle\Entity\Experience;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class ExperienceInternshipType
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class ExperienceInternshipType extends AbstractType
{
    use NameTrait;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('internshipSubject', 'text', [
                'label' => 'Sujet',
                'placeholder' => 'ex: Création d\'un outil de gestion',
            ])
            ->add('salary', 'choice', [
                'label' => 'Compensation mensuelle percue',
                'required' => false,
                'choices' => Experience::getSalaryInternshipNameList(),
            ])
            ->add('tuteur','text',[
                'label' => 'Tuteur Entreprise',
                'placeholder' => 'ex:John DOE',
                'required'=>false,
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
            'data_class' => 'Adcog\DefaultBundle\Entity\ExperienceInternship',
        ]);
    }
}
