<?php
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


namespace Adcog\MemberBundle\Form;

use Adcog\DefaultBundle\Form\NameTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class EmployerFilterType
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class EmployerFilterType extends AbstractType
{
    use NameTrait;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        $builder
            ->add('name', 'text', [
                'label' => 'Dénomination',
                'placeholder' => 'ex: Cognitive Corp\'',
                'required' => false,
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
            ->add('employerType', 'entity', [
                'label'=> 'Type d\'entreprise',
                'required' => false,
                'class' => 'Adcog\DefaultBundle\Entity\EmployerType',
            ])
            ->add('sectors', 'entity', [
                'label' => 'Secteurs',
                'required' => false,
                'multiple' => true,
                'class' => 'Adcog\DefaultBundle\Entity\Sector',
            ]);
    }
}
