<?php
namespace Adcog\DefaultBundle\Transformer;

use Adcog\DefaultBundle\Form\TernaryType;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class TernaryTransformer
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class TernaryTransformer implements DataTransformerInterface
{
    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        if (true === $value) {
            return TernaryType::YES;
        } elseif (false === $value) {
            return TernaryType::NO;
        }

        return TernaryType::NONE;
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        if (TernaryType::YES === $value) {
            return true;
        } elseif (TernaryType::NO === $value) {
            return false;
        }

        return null;
    }
}
