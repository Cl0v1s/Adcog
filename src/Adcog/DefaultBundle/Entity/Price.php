<?php

namespace Adcog\DefaultBundle\Entity;

use Adcog\DefaultBundle\Entity\Common\PriceTrait;
use Doctrine\ORM\Mapping as ORM;
use EB\DoctrineBundle\Entity\LoggableInterface;
use EB\DoctrineBundle\Entity\Doctrine\SlugTrait;
use EB\DoctrineBundle\Entity\SlugInterface;
use Symfony\Component\Validator\Constraints as Assert;
use EB\DoctrineBundle\Entity\CreatedInterface;
use EB\DoctrineBundle\Entity\Doctrine\CreatedTrait;
use EB\DoctrineBundle\Entity\Doctrine\UpdatedTrait;
use EB\DoctrineBundle\Entity\UpdatedInterface;

/**
 * Class Price
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 * @ORM\Entity(repositoryClass="Adcog\DefaultBundle\Repository\PriceRepository")
 * @ORM\Table(name="adcog_price")
 */
class Price implements CreatedInterface, UpdatedInterface, LoggableInterface, SlugInterface
{
    use CreatedTrait;
    use UpdatedTrait;
    use SlugTrait;
    use PriceTrait;

    /**
     * @var int
     * @ORM\Column(type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return sprintf(
            '%s€ pour %u an%s - %s',
            $this->getAmount(),
            $this->getDuration(),
            $this->getDuration() > 1 ? 's' : '',
            $this->getTitle()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getStringToSlug()
    {
        return (string)$this;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}