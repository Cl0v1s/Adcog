<?php

namespace Adcog\DefaultBundle\Transformer;

use Adcog\DefaultBundle\Entity\Tag;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class TagTransformer
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class TagTransformer implements DataTransformerInterface
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        if (null === $value) {
            return '';
        }

        if ($value instanceof Collection) {
            return implode(',', array_map(function (Tag $tag) {
                return (string)$tag;
            }, $value->toArray()));
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
            $values = explode(',', $value);
            foreach ($values as &$value) {
                $value = $this->em->getRepository('AdcogDefaultBundle:Tag')->findOrCreate(trim($value));
            }
            unset($value);

            return $values;
        }

        return $value;
    }
}
