<?php

namespace Adcog\AdminBundle\Form;

use Adcog\DefaultBundle\Entity\Role;
use Adcog\DefaultBundle\Form\NameTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class RoleType
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class RoleType extends AbstractType
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
            ])
            ->add('desk', 'checkbox', [
                'label' => 'Bureau',
                'required' => false,
            ])
            ->add('order', 'integer', [
                'label' => 'Classement',
                'help' => 'De 0 en haut à 100 en bas',
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Adcog\DefaultBundle\Entity\Role',
        ]);
    }
}
