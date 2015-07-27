<?php

namespace Adcog\DefaultBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use EB\DoctrineBundle\Entity\LoggableInterface;
use EB\DoctrineBundle\Entity\CreatedInterface;
use EB\DoctrineBundle\Entity\Doctrine\CreatedTrait;
use EB\DoctrineBundle\Entity\Doctrine\SlugTrait;
use EB\DoctrineBundle\Entity\Doctrine\UpdatedTrait;
use EB\DoctrineBundle\Entity\SlugInterface;
use EB\DoctrineBundle\Entity\UpdatedInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Event
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 * @ORM\Entity(repositoryClass="Adcog\DefaultBundle\Repository\EventRepository")
 * @ORM\Table(name="adcog_event")
 */
class Event implements CreatedInterface, UpdatedInterface, SlugInterface, LoggableInterface
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
     * @var string
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(max=255)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     */
    private $description;

    /**
     * @var string
     * @ORM\Column(type="text")
     * @Assert\NotNull()
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     */
    private $program;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     * @Assert\NotNull()
     * @Assert\DateTime()
     */
    private $date;

    /**
     * @var EventParticipation[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="EventParticipation", mappedBy="event")
     */
    private $participations;

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        $this->participations = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        if ($this->getDate()->getTimestamp() < time()) {
            return sprintf('%s (terminé)', $this->getName());
        }

        return (string)$this->getName();
    }

    /**
     * Get Date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set Date
     *
     * @param \DateTime $date
     *
     * @return Event
     */
    public function setDate(\DateTime $date = null)
    {
        $this->date = $date;

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
     * Set Name
     *
     * @param string $name
     *
     * @return Event
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
        return (string)$this->getName();
    }

    /**
     * @param User $user
     *
     * @return null|EventParticipation
     */
    public function getUserParticipation(User $user)
    {
        foreach ($this->getParticipations() as $participation) {
            if ($participation->getUser()->getId() === $user->getId()) {
                return $participation;
            }
        }

        return null;
    }

    /**
     * Get Event Participation
     *
     * @return EventParticipation[]|ArrayCollection
     */
    public function getParticipations()
    {
        return $this->participations;
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
     * @param string $description
     *
     * @return Event
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get Program
     *
     * @return string
     */
    public function getProgram()
    {
        return $this->program;
    }

    /**
     * Set Program
     *
     * @param string $program
     *
     * @return Event
     */
    public function setProgram($program)
    {
        $this->program = $program;

        return $this;
    }

    /**
     * Add Event Participation
     *
     * @param EventParticipation $participation
     *
     * @return Event
     */
    public function addParticipation(EventParticipation $participation)
    {
        if (false === $this->participations->contains($participation)) {
            $this->participations[] = $participation;
        }

        return $this;
    }

    /**
     * Remove Event Participation
     *
     * @param EventParticipation $participation
     *
     * @return Event
     */
    public function removeParticipation(EventParticipation $participation)
    {
        if (true === $this->participations->contains($participation)) {
            $this->participations->removeElement($participation);
        }

        return $this;
    }
}
