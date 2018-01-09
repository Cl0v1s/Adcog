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
 * Class EventParticipation
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 * @ORM\Entity(repositoryClass="Adcog\DefaultBundle\Repository\EventParticipationRepository")
 * @ORM\Table(name="adcog_event_participation")
 */
class EventParticipation implements CreatedInterface, UpdatedInterface, LoggableInterface
{
    use CreatedTrait;
    use UpdatedTrait;

    const TYPE_ORG = 'ORG';
    const TYPE_SPEAKER = 'SPEAKER';
    const TYPE_SPEC = 'SPECTATOR';

    /**
     * @var int
     * @ORM\Column(type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var Event
     * @ORM\ManyToOne(targetEntity="Event", inversedBy="participations")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Assert\NotNull()
     */
    private $event;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Assert\NotNull()
     */
    private $user;

    /**
     * @var string
     * @ORM\Column(type="string", length=20)
     * @Assert\NotNull()
     * @Assert\NotBlank()
     * @Assert\Choice(callback="getTypeList")
     */
    private $type;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        $this->setType(self::TYPE_SPEC);
    }

    /**
     * Get types
     *
     * @return string[]
     */
    public static function getTypeList()
    {
        return [
            self::TYPE_ORG,
            self::TYPE_SPEAKER,
            self::TYPE_SPEC,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return sprintf('Participation de %s à %s', $this->getUser(), $this->getEvent());
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
     * @param User $user
     *
     * @return EventParticipation
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get Event
     *
     * @return Event
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Set Event
     *
     * @param Event $event
     *
     * @return EventParticipation
     */
    public function setEvent(Event $event = null)
    {
        $this->event = $event;

        return $this;
    }

    /**
     * Get type name
     *
     * @return string
     */
    public function getTypeName()
    {
        $names = self::getTypeNameList();

        return array_key_exists($this->getType(), $names) ? $names[$this->getType()] : '-';
    }

    /**
     * Get type names
     *
     * @return string[]
     */
    public static function getTypeNameList()
    {
        return [
            self::TYPE_ORG => 'Organisateur',
            self::TYPE_SPEAKER => 'Intervenant',
            self::TYPE_SPEC => 'Spectateur',
        ];
    }

    /**
     * Get Type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set Type
     *
     * @param string $type
     *
     * @return EventParticipation
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
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
     * Get Comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set Comment
     *
     * @param string $comment
     *
     * @return EventParticipation
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }
}
