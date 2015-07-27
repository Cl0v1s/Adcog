<?php

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
                'label' => 'Nom',
                'placeholder' => 'Cognitive Corp\'',
                'required' => false,
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
