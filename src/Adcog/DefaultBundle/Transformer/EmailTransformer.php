<?php

namespace Adcog\DefaultBundle\Transformer;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class EmailTransformer
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class EmailTransformer implements DataTransformerInterface
{
    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        if (null === $value) {
            return '';
        }

        if (is_array($value)) {
            return implode(',', $value);
        }

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        if (empty($value)) {
            return [];
        }

        if (is_string($value)) {
            return explode(',', $value);
        }

        return $value;
    }
}
