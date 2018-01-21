<?php

namespace Adcog\DefaultBundle\Transformer;

use Adcog\DefaultBundle\Entity\ExperienceSource;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class ExperienceSourceTransformer
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class ExperienceSourceTransformer implements DataTransformerInterface
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
        else{
            return (string)$value;
        }

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
            
            $value = $this->em->getRepository('AdcogDefaultBundle:ExperienceSource')->findOrCreate(trim($value));
        }
        return $value;
    }
}
