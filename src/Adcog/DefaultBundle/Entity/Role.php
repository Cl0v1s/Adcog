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
 * Class Role
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 * @ORM\Entity(repositoryClass="Adcog\DefaultBundle\Repository\RoleRepository")
 * @ORM\Table(name="adcog_role")
 */
class Role implements CreatedInterface, UpdatedInterface, LoggableInterface
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
     * @var string
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(max=100)
     */
    private $name;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $desk;

    /**
     * @var int
     * @ORM\Column(name="role_order", type="integer")
     * @Assert\Regex(pattern="/[0-9]+/")
     * @Assert\Range(min=0,max=100)
     */
    private $order;

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return (string)$this->getName();
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
     * Set Name
     *
     * @param string $name Name
     *
     * @return Role
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
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
     * Set Desk
     *
     * @param bool $desk Desk
     *
     * @return Role
     */
    public function setDesk($desk)
    {
        $this->desk = $desk;

        return $this;
    }

    /**
     * Get Desk
     *
     * @return bool
     */
    public function getDesk()
    {
        return $this->desk;
    }

    /**
     * Set Order
     *
     * @param int $order Order
     *
     * @return Role
     */
    public function setOrder($order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Get Order
     *
     * @return int
     */
    public function getOrder()
    {
        return $this->order;
    }
}
