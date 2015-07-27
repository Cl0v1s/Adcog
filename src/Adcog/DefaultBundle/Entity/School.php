<?php

namespace Adcog\DefaultBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use EB\DoctrineBundle\Entity\LoggableInterface;
use EB\DoctrineBundle\Entity\CreatedInterface;
use EB\DoctrineBundle\Entity\Doctrine\CreatedTrait;
use EB\DoctrineBundle\Entity\Doctrine\FileReadableTrait;
use EB\DoctrineBundle\Entity\Doctrine\FileTrait;
use EB\DoctrineBundle\Entity\Doctrine\UpdatedTrait;
use EB\DoctrineBundle\Entity\FileReadableInterface;
use EB\DoctrineBundle\Entity\UpdatedInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Class School
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 * @ORM\Entity(repositoryClass="Adcog\DefaultBundle\Repository\SchoolRepository")
 * @ORM\Table(name="adcog_school")
 * @DoctrineAssert\UniqueEntity(fields={"year"}, errorPath="name")
 * @DoctrineAssert\UniqueEntity(fields={"name"}, errorPath="name")
 * @DoctrineAssert\UniqueEntity(fields={"year","name"}, errorPath="name")
 * @Assert\Callback("validate")
 */
class School implements CreatedInterface, UpdatedInterface, LoggableInterface, FileReadableInterface
{
    use CreatedTrait;
    use UpdatedTrait;
    use FileReadableTrait;
    use FileTrait;

    /**
     * @var int
     * @ORM\Column(type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     */
    private $name;

    /**
     * @var int
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     * @Assert\Regex(pattern="/[0-9]+/")
     */
    private $year;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max=255)
     */
    private $wikipedia;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $graduation;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Regex(pattern="/[0-9]+/")
     */
    private $graduates;

    /**
     * @var User[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="User", mappedBy="school")
     */
    private $users;

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return sprintf('(%s) %s', $this->getYear(), $this->getName());
    }

    /**
     * Get year
     *
     * @return int
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set year
     *
     * @param int $year
     *
     * @return School
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return School
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Validate
     *
     * @param ExecutionContextInterface $context
     */
    public function validate(ExecutionContextInterface $context)
    {
        $context->getViolations()->addAll($context->getValidator()->validate($this->getFile(), [new Assert\Image()]));
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
     * Get Wikipedia
     *
     * @return null|string
     */
    public function getWikipedia()
    {
        return $this->wikipedia;
    }

    /**
     * Set Wikipedia
     *
     * @param null|string $wikipedia Wikipedia
     *
     * @return School
     */
    public function setWikipedia($wikipedia)
    {
        $this->wikipedia = $wikipedia;

        return $this;
    }

    /**
     * Get Description
     *
     * @return null|string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set Description
     *
     * @param null|string $description Description
     *
     * @return School
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get Graduation
     *
     * @return null|string
     */
    public function getGraduation()
    {
        return $this->graduation;
    }

    /**
     * Set Graduation
     *
     * @param null|string $graduation Graduation
     *
     * @return School
     */
    public function setGraduation($graduation)
    {
        $this->graduation = $graduation;

        return $this;
    }

    /**
     * Get Graduates
     *
     * @return null|int
     */
    public function getGraduates()
    {
        return $this->graduates;
    }

    /**
     * Set Graduates
     *
     * @param null|int $graduates Graduates
     *
     * @return School
     */
    public function setGraduates($graduates)
    {
        $this->graduates = $graduates;

        return $this;
    }

    /**
     * Add user
     *
     * @param User $user
     *
     * @return School
     */
    public function addUser(User $user)
    {
        if (false === $this->users->contains($user)) {
            $this->users[] = $user;
            $user->setSchool($this);
        }

        return $this;
    }

    /**
     * Remove user
     *
     * @param User $user
     *
     * @return School
     */
    public function removeUser(User $user)
    {
        if (true === $this->users->contains($user)) {
            $this->users->removeElement($user);
            $user->setSchool(null);
        }

        return $this;
    }

    /**
     * Get users
     *
     * @return User[]|ArrayCollection
     */
    public function getUsers()
    {
        return $this->users;
    }
}
