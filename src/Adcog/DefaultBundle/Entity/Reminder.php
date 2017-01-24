<?php

namespace Adcog\DefaultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use EB\DoctrineBundle\Entity\LoggableInterface;
use EB\DoctrineBundle\Entity\CreatedInterface;
use EB\DoctrineBundle\Entity\Doctrine\CreatedTrait;
use EB\DoctrineBundle\Entity\Doctrine\UpdatedTrait;
use EB\DoctrineBundle\Entity\UpdatedInterface;

/**
 * Class Reminder
 * @author "Nicolas Drufin" <nicolas.drufin@ensc.fr>
 * @ORM\Entity(repositoryClass="Adcog\DefaultBundle\Repository\ReminderRepository")
 * @ORM\Table(name="adcog_reminder")
 */
class Reminder implements CreatedInterface, UpdatedInterface, LoggableInterface {

    use CreatedTrait;
    use UpdatedTrait;

    const TYPE_BEFORE = true;
    const TYPE_AFTER = false;

    /**
     * @var int
     * @ORM\Column(type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     * @ORM\Column(type="integer", options={"default":0})
     * @Assert\Regex(pattern="/[0-9]+/")
     */
    private $year;

    /**
     * @var int
     * @ORM\Column(type="integer", options={"default":0})
     * @Assert\Regex(pattern="/[0-9]+/")
     */
    private $month;

    /**
     * @var int
     * @ORM\Column(type="integer", options={"default":0})
     * @Assert\Regex(pattern="/[0-9]+/")
     */
    private $days;

    /**
     * @var bool
     * @ORM\Column(type="boolean", options={"default":0})
     */
    private $invert = false;

    /**
     * @var bool
     * @ORM\Column(type="boolean", options={"default":0})
     */
    private $cycle = false;

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
     * Get date interval
     *
     * @return DateInterval
     */
    public function getDateInterval()
    {
        $date_interval = new \DateInterval(
            sprintf("P%uY%uM%uD",
                $this->year,
                $this->month,
                $this->days)
        );
        if ($this->invert) {
            $date_interval->invert = 1;
        }
        return $date_interval;
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
     * @return Reminder
     */
    public function setYear($year) {
        $this->year = $year;

        return $this;
    }

    /**
     * Get month
     *
     * @return int
     */
    public function getMonth()
    {
        return $this->month;
    }

    /**
     * Set month
     *
     * @param int $month
     *
     * @return Reminder
     */
    public function setMonth($month) {
        $this->month = $month;

        return $this;
    }

    /**
     * Get days
     *
     * @return int
     */
    public function getDays()
    {
        return $this->days;
    }

    /**
     * Set days
     *
     * @param int $days
     *
     * @return Reminder
     */
    public function setDays($days) {
        $this->days = $days;

        return $this;
    }

    /**
     * If is cycle
     *
     * @return Boolean
     */
    public function isCycle()
    {
        return $this->cycle;
    }

    /**
     * Set cycle
     *
     * @param Boolean $has_cycle
     *
     * @return Reminder
     */
    public function setCycle($has_cycle) {
        $this->cycle = $has_cycle;

        return $this;
    }

    /**
     * If is inverted
     *
     * @return Boolean
     */
    public function isInvert()
    {
        return $this->invert;
    }

    /**
     * Set inverted
     *
     * @param Boolean $has_invert
     *
     * @return Reminder
     */
    public function setInvert($has_invert) {
        $this->invert = $has_invert;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        $cycle_indicator = "";
        if ($this->isCycle()) {
            $cycle_indicator = "récurrent";
        }
        $invert_indicator = "après";
        if ($this->isInvert()) {
            $invert_indicator = "avant";
        }
        return sprintf("Rappel %s %u ans %u mois et %u jours %s",
            $invert_indicator,
            $this->year,
            $this->month,
            $this->days,
            $cycle_indicator);
    }

    /**
     * @return string[]
     */
    static public function getTypeInverted()
    {
        return [
            self::TYPE_BEFORE => 'Avant expiration',
            self::TYPE_AFTER => 'Après expiration',
        ];
    }
} 