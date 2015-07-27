<?php

namespace Adcog\AdminBundle\Form;

use Adcog\DefaultBundle\Form\NameTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class SchoolFilterType
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class SchoolFilterType extends AbstractType
{
    use NameTrait;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('year', 'integer', [
                'label' => 'Année',
                'required' => false,
            ])
            ->add('name', 'text', [
                'label' => 'Nom',
                'required' => false,
            ]);
    }
}
