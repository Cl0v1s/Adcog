<?php

namespace Adcog\DefaultBundle\Form\Field;

use Adcog\DefaultBundle\Form\NameTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\Options;
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

        $placeholderDefault = function (Options $options) {
            return $options['required'] ? null : '';
        };

        $placeholderNormalizer = function (Options $options, $placeholder) use ($placeholderDefault) {
            if (is_array($placeholder)) {
                $default = $placeholderDefault($options);

                return array_merge(
                    array('year' => $default, 'month' => $default, 'day' => $default),
                    $placeholder
                );
            }

            return $placeholder;
        };

        $resolver->setNormalizers(array(
            'empty_value' => $placeholderNormalizer,
            'placeholder' => $placeholderNormalizer,
        ));
    }
}
