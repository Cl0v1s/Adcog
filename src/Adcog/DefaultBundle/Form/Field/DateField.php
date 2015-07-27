<?php

namespace Adcog\DefaultBundle\Form\Field;

use Adcog\DefaultBundle\Form\NameTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class DateField
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class DateField extends AbstractType
{
    use NameTrait;

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'date';
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'widget' => 'single_text',
            'format' => 'dd/MM/yyyy',
            'attr' => [
                'autocomplete' => 'off',
            ],
        ]);
    }
}
