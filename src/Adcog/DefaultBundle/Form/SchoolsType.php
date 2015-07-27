<?php

namespace Adcog\DefaultBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class SchoolsType
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class SchoolsType extends AbstractType
{
    use NameTrait;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('school', 'entity', [
                'label' => 'Promotions',
                'class' => 'Adcog\DefaultBundle\Entity\School'
            ]);
    }
}
