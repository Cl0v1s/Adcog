<?php

namespace Adcog\DefaultBundle\Transformer;

use Adcog\DefaultBundle\Entity\Sector;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class SectorTransformer
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class SectorTransformer implements DataTransformerInterface
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
            return implode(',', array_map(function (Sector $sector) {
                return (string)$sector;
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
                $value = $this->em->getRepository('AdcogDefaultBundle:Sector')->findOrCreate(trim($value));
            }
            unset($value);

            return $values;
        }

        return $value;
    }
}
