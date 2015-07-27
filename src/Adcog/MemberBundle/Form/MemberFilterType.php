<?php

namespace Adcog\MemberBundle\Form;

use Adcog\DefaultBundle\Form\NameTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class MemberFilterType
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class MemberFilterType extends AbstractType
{
    use NameTrait;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lastname', 'text', [
                'label' => 'Nom',
                'placeholder' => 'John',
                'required' => false,
            ])
            ->add('firstname', 'text', [
                'label' => 'Prénom',
                'placeholder' => 'DOE',
                'required' => false,
            ])
            ->add('schools', 'entity', [
                'label' => 'Promotions',
                'placeholder' => '2010',
                'required' => false,
                'multiple' => true,
                'class' => 'Adcog\DefaultBundle\Entity\School',
            ])
            ->add('city', 'text', [
                'label' => 'Ville',
                'placeholder' => 'Talence',
                'required' => false,
            ])
            ->add('country', 'text', [
                'label' => 'Pays',
                'placeholder' => 'France',
                'required' => false,
            ]);
    }
}
