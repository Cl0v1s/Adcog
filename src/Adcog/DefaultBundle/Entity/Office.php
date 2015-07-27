<?php

namespace Adcog\DefaultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use EB\DoctrineBundle\Entity\LoggableInterface;
use EB\DoctrineBundle\Entity\CreatedInterface;
use EB\DoctrineBundle\Entity\Doctrine\CreatedTrait;
use EB\DoctrineBundle\Entity\Doctrine\UpdatedTrait;
use EB\DoctrineBundle\Entity\UpdatedInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Office
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 * @ORM\Entity(repositoryClass="Adcog\DefaultBundle\Repository\OfficeRepository")
 * @ORM\Table(name="adcog_office")
 */
class Office implements CreatedInterface, UpdatedInterface, LoggableInterface
{
    use CreatedTrait;
    use UpdatedTrait;

    /**
     * @var int
     * @ORM\Column(type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var Role
     * @ORM\ManyToOne(targetEntity="Role")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Assert\NotBlank()
     */
    private $role;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank()
     * @Assert\DateTime()
     */
    private $started;

    /**
     * @var null|\DateTime
     * @ORM\Column(type="datetime", nullable=true)
     * @Assert\DateTime()
     */
    private $ended;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Assert\NotBlank()
     */
    private $user;

    /**
     * Active
     *
     * @return bool
     */
    public function isActive()
    {
        if (null === $this->getEnded()) {
            return $this->getStarted()->getTimestamp() < time();
        }

        return
            $this->getStarted()->getTimestamp() < time() &&
            $this->getEnded()->getTimestamp() > time();
    }

    /**
     * Get Ended
     *
     * @return null|\DateTime
     */
    public function getEnded()
    {
        return $this->ended;
    }

    /**
     * Set Ended
     *
     * @param null|\DateTime $ended Ended
     *
     * @return Office
     */
    public function setEnded(\DateTime $ended = null)
    {
        $this->ended = $ended;

        return $this;
    }

    /**
     * Get Started
     *
     * @return \DateTime
     */
    public function getStarted()
    {
        return $this->started;
    }

    /**
     * Set Started
     *
     * @param \DateTime $started Started
     *
     * @return Office
     */
    public function setStarted(\DateTime $started)
    {
        $this->started = $started;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return sprintf('Bureau n°%u', $this->getId());
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
     * Get role
     *
     * @return Role
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set Role
     *
     * @param Role $role
     *
     * @return Office
     */
    public function setRole(Role $role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set user
     *
     * @param User $user
     *
     * @return Office
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;

        return $this;
    }
}
