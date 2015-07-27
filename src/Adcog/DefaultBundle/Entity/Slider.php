<?php

namespace Adcog\DefaultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use EB\DoctrineBundle\Entity\LoggableInterface;
use EB\DoctrineBundle\Entity\CreatedInterface;
use EB\DoctrineBundle\Entity\Doctrine\CreatedTrait;
use EB\DoctrineBundle\Entity\Doctrine\FileReadableTrait;
use EB\DoctrineBundle\Entity\Doctrine\FileTrait;
use EB\DoctrineBundle\Entity\Doctrine\FileVersionableTrait;
use EB\DoctrineBundle\Entity\Doctrine\UpdatedTrait;
use EB\DoctrineBundle\Entity\FileReadableInterface;
use EB\DoctrineBundle\Entity\FileVersionableInterface;
use EB\DoctrineBundle\Entity\UpdatedInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Class Slider
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 * @ORM\Entity(repositoryClass="Adcog\DefaultBundle\Repository\SliderRepository")
 * @ORM\Table(name="adcog_slider")
 * @Assert\Callback("validate")
 */
class Slider implements CreatedInterface, UpdatedInterface, LoggableInterface, FileReadableInterface, FileVersionableInterface
{
    use CreatedTrait;
    use UpdatedTrait;
    use FileReadableTrait;
    use FileTrait;
    use FileVersionableTrait;

    /**
     * @var int
     * @ORM\Column(type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     */
    private $alt;

    /**
     * @var null|string
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max=255)
     * @Assert\Url()
     */
    private $href;

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return (string)$this->getAlt();
    }

    /**
     * Get Alt
     *
     * @return string
     */
    public function getAlt()
    {
        return $this->alt;
    }

    /**
     * Set Alt
     *
     * @param string $alt Alt
     *
     * @return Slider
     */
    public function setAlt($alt)
    {
        $this->alt = $alt;

        return $this;
    }

    /**
     * Validate
     *
     * @param ExecutionContextInterface $context
     */
    public function validate(ExecutionContextInterface $context)
    {
        $context->getViolations()->addAll($context->getValidator()->validate($this->getFile(), array_filter([
            null === $this->getId() ? new Assert\NotBlank() : null,
            new Assert\Image(),
        ])));
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

    /**
     * Get Href
     *
     * @return null|string
     */
    public function getHref()
    {
        return $this->href;
    }

    /**
     * Set Href
     *
     * @param null|string $href Href
     *
     * @return Slider
     */
    public function setHref($href)
    {
        $this->href = $href;

        return $this;
    }
}
