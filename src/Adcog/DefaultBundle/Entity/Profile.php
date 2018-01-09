<?php

namespace Adcog\DefaultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use EB\DoctrineBundle\Entity\LoggableInterface;
use EB\DoctrineBundle\Entity\CreatedInterface;
use EB\DoctrineBundle\Entity\Doctrine\CreatedTrait;
use EB\DoctrineBundle\Entity\Doctrine\SlugTrait;
use EB\DoctrineBundle\Entity\Doctrine\UpdatedTrait;
use EB\DoctrineBundle\Entity\SlugInterface;
use EB\DoctrineBundle\Entity\UpdatedInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Profile
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 * @ORM\Entity()
 * @ORM\Table(name="adcog_profile")
 * @DoctrineAssert\UniqueEntity(fields={"name"})
 * @DoctrineAssert\UniqueEntity(fields={"slug"})
 */
class Profile implements CreatedInterface, UpdatedInterface, LoggableInterface, SlugInterface
{
    use CreatedTrait;
    use UpdatedTrait;
    use SlugTrait;

    /**
     * @var int
     * @ORM\Column(type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var User
     * @ORM\OneToOne(targetEntity="User", inversedBy="profile")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Assert\NotBlank()
     */
    private $user;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(max="255", min="2")
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return (string)$this->getName();
    }

    /**
     * Get Name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set Name
     *
     * @param string $name Name
     *
     * @return Profile
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getStringToSlug()
    {
        return $this->getName();
    }

    /**
     * Get Id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get User
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set User
     *
     * @param User $user User
     *
     * @return Profile
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get Description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set Description
     *
     * @param string $description Description
     *
     * @return Profile
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }
}
