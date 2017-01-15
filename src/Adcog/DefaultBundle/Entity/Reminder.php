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

    /**
     * @var int
     * @ORM\Column(type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \string
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(min=2, max=255)
     */
    private $interval;

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
     * Get date interval
     *
     * @return DateInterval
     */
    public function getDateInterval()
    {
        $date_interval = new \DateInterval($this->interval);
        if ($this->invert) {
            $date_interval->invert = 1;
        }
        return $date_interval;
    }

    /**
     * Set date interval
     *
     * @param DateInterval $date_interval
     */
    public function setDateInterval(\DateInterval $date_interval) {
        $this->interval = $this->dateIntervalToString($date_interval);
        $this->invert = $date_interval->invert;
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
     */
    public function setCycle(Boolean $has_cycle) {
        $this->cycle = $has_cycle;
    }

    /**
     * If is inverted
     *
     * @return Boolean
     */
    public function isInverted()
    {
        return $this->invert;
    }

    /**
     * Set inverted
     *
     * @param Boolean $has_invert
     */
    public function setInverted(Boolean $has_invert) {
        $this->invert = $has_invert;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        if ($this->isCycle()) {
            $cycle_indicator = "cyclique";
        }
        $invert_indicator = "après";
        if ($this->isInverted()) {
            $invert_indicator = "avant";
        }
        return sprintf("%s le %s %s",
            $invert_indicator,
            $this->interval,
            $cycle_indicator);
    }

    /**
     * @param \DateInterval $interval
     *
     * @return string
     */
    function dateIntervalToString(\DateInterval $interval) {

        // Reading all non-zero date parts.
        $date = array_filter(array(
            'Y' => $interval->y,
            'M' => $interval->m,
            'D' => $interval->d
        ));

        // Reading all non-zero time parts.
        $time = array_filter(array(
            'H' => $interval->h,
            'M' => $interval->i,
            'S' => $interval->s
        ));

        $specString = 'P';

        // Adding each part to the spec-string.
        foreach ($date as $key => $value) {
            $specString .= $value . $key;
        }
        if (count($time) > 0) {
            $specString .= 'T';
            foreach ($time as $key => $value) {
                $specString .= $value . $key;
            }
        }

        return $specString;
    }
} 