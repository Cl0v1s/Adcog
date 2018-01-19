<?php

namespace Adcog\DefaultBundle\Form;

use Adcog\DefaultBundle\Entity\ExperienceWork;
use Adcog\DefaultBundle\Entity\Experience;
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
                'placeholder' => 'ex: Ingénieur en Cognitique',
            ])
            ->add('salary', 'choice', [
                'label' => 'Salaire brut annuel',
                'required' => false,
                'choices' => Experience::getSalaryNameList()
            ])
            ->add('contractType','entity', [
                'label' => 'Type de Contrat',
                'placeholder' => 'Choissisez un type de contrat',
                'class' => 'Adcog\DefaultBundle\Entity\ContractType',
            ])
            ->add('partTime','checkbox',[
                'label'=> 'Temps Partiel',
                'required' => false,
            ]) 
            ->add('partTimeValue','number', [
                'placeholder'=>'A combien de % ?',
                'required'=> false,
                'label' => ' ',
                'attr' => array('style' => 'display:none'),
            ])
            ->add('status','choice', [
                'label' => 'Statut',
                'choices' => ExperienceWork::getStatutNameList(),
                'placeholder' => 'Choissisez un statut',
                /*'preferred_choices' => array('Cadres et professions intellectuelles supérieures'),*/
            ])  
            ->add('experience', 'adcog_experience', [
                'inherit_data' => true,
            ])
            ->add('salary', 'choice', [
                'label' => 'Salaire brut annuel',
                'required' => false,
                'choices' => ExperienceWork::getSalaryNameList(),
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
