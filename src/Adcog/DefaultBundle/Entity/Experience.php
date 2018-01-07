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
 * Class Experience
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 * @ORM\Entity(repositoryClass="Adcog\DefaultBundle\Repository\ExperienceRepository")
 * @ORM\Table(name="adcog_experience")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr",type="string")
 * @ORM\DiscriminatorMap({"work"="ExperienceWork","thesis"="ExperienceThesis","study":"ExperienceStudy","internship":"ExperienceInternship"})
 */
abstract class Experience implements CreatedInterface, UpdatedInterface, SlugInterface, LoggableInterface
{
    use CreatedTrait;
    use UpdatedTrait;
    use SlugTrait;

    const SALARY_0_5 = 5;
    const SALARY_5_7 = 7;
    const SALARY_7_9 = 9;
    const SALARY_9_11 = 11;
    const SALARY_11_13 = 13;
    const SALARY_13 = 15;

    const SALARY_0_25 = 25;
    const SALARY_25_30 = 30;
    const SALARY_30_35 = 35;
    const SALARY_35_40 = 40;
    const SALARY_40_45 = 45;
    const SALARY_45 = 50;

    const TYPE_INTERNSHIP = 'internship';
    const TYPE_STUDY = 'study';
    const TYPE_THESIS = 'thesis';
    const TYPE_WORK = 'work';

    const STATUT_1= 1;
    const STATUT_2= 2;
    const STATUT_3= 3;
    const STATUT_4= 4;
    const STATUT_5= 5;
    const STATUT_6= 6;

    /**
     * @var int
     * @ORM\Column(type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var bool
     * @ORM\Column(type="boolean", options={"default":0})
     */
    private $isPublic = false;

    /**
     * @var \DateTime
     * @ORM\Column(type="date")
     * @Assert\NotBlank()
     * @Assert\DateTime()
     */
    private $started;

    /**
     * @var null|\DateTime
     * @ORM\Column(type="date", nullable=true)
     * @Assert\DateTime()
     */
    private $ended;

    /**
     * @var string
     * @ORM\Column(type="text")
     * @Assert\NotNull()
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     */
    private $description;

    /**
     * @var null|int
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Choice(callback="getSalaryList")
     */
    protected $salary;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User", inversedBy="experiences")
     * @Assert\NotBlank()
     */
    private $user;

    /**
     * @var Employer
     * @ORM\ManyToOne(targetEntity="Employer", inversedBy="experiences", cascade={"persist"})
     * @Assert\NotNull()
     */
    private $employer;

    /**
     * @var Sector[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity="Sector")
     * @Assert\Count(min="1")
     */
    private $sectors;

    /**
     * @var ExperienceSource
     * @ORM\OneToOne(targetEntity="ExperienceSource", cascade={"persist"})
     */
    private $experienceSource;


    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    private $status;

    

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        $this->sectors = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return sprintf('Experience %s', $this->getId());
    }

    /**
     * @param string $type
     *
     * @return Experience
     */
    public static function createObject($type)
    {
        $class = sprintf(get_class() . mb_convert_case($type, MB_CASE_TITLE));

        return new $class();
    }

    /**
     * @return string[]
     */
    public static function getSalaryList()
    {
        return [
            self::SALARY_0_25,
            self::SALARY_25_30,
            self::SALARY_30_35,
            self::SALARY_35_40,
            self::SALARY_40_45,
            self::SALARY_45,
        ];
    }
     /**
     * @return string[]
     */
    public static function getSalaryInternshipList()
    {
        return [
            self::SALARY_0_5,
            self::SALARY_5_7,
            self::SALARY_7_9,
            self::SALARY_9_11,
            self::SALARY_11_13,
            self::SALARY_13,
        ];
    }

    public static function getStatutList()
    {
        return [
            self::STATUT_1,
            self::STATUT_2,
            self::STATUT_3,
            self::STATUT_4,
            self::STATUT_5,
            self::STATUT_6,
        ];
    }

    /**
     * @return string[]
     */
    public static function getSalaryNameList()
    {
        return [
            self::SALARY_0_25 => 'Moins de 25k€',
            self::SALARY_25_30 => 'Entre 25 et 30k€',
            self::SALARY_30_35 => 'Entre 30 et 35k€',
            self::SALARY_35_40 => 'Entre 35 et 40k€',
            self::SALARY_40_45 => 'Entre 40 et 45k€',
            self::SALARY_45 => 'Plus de 45k€',
        ];
    }
    /**
     * @return string[]
     */
    public static function getSalaryInternshipNameList()
    {
        return [
            self::SALARY_0_5 => 'Moins de 500€',
            self::SALARY_5_7 => 'Entre 500 et 700€',
            self::SALARY_7_9 => 'Entre 700 et 900€',
            self::SALARY_9_11 => 'Entre 900 et 1100€',
            self::SALARY_11_13 => 'Entre 1100 et 1300€',
            self::SALARY_13 => 'Plus de 1300€',
        ];
    }

    public static function getStatutNameList()
    {
        return [
            self::STATUT_1 => '1.Agriculteurs exploitants',
            self::STATUT_2 => '2.Artisans, commerçants et chefs d’entreprise',
            self::STATUT_3 => '3.Cadres et professions intellectuelles supérieures',
            self::STATUT_4 => '4.Professions Intermédiaires',
            self::STATUT_5 => '5.Employés',
            self::STATUT_6 => '6.Ouvriers',
        ];
    }

    /**
     * @return string[]
     */
    public static function getTypeList()
    {
        return [
            self::TYPE_WORK,
            self::TYPE_THESIS,
            self::TYPE_INTERNSHIP,
            self::TYPE_STUDY,
        ];
    }

    /**
     * @return string[]
     */
    public static function getTypeNameList()
    {
        return [
            self::TYPE_WORK => 'Expérience professionnelle',
            self::TYPE_THESIS => 'Thèse',
            self::TYPE_INTERNSHIP => 'Stage',
            self::TYPE_STUDY => 'Étude',
        ];
    }

    /**
     * Add Sector
     *
     * @param Sector $sector
     *
     * @return Experience
     */
    public function addSector(Sector $sector)
    {
        if (false === $this->sectors->contains($sector)) {
            $this->sectors[] = $sector;
        }

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
     * @param string $description
     *
     * @return Experience
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get Employer
     *
     * @return Employer
     */
    public function getEmployer()
    {
        return $this->employer;
    }

    /**
     * Set Employer
     *
     * @param Employer $employer
     *
     * @return Experience
     */
    public function setEmployer(Employer $employer = null)
    {
        $this->employer = $employer;

        return $this;
    }

    /**
     * Get ExperienceSource
     *
     * @return ExperienceSource
     */
    public function getExperienceSource()
    {
        return $this->experienceSource;
    }

    /**
     * Set ExperienceSource
     *
     * @param null|ExperienceSource $experienceSource
     *
     * @return Experience
     */
    public function setExperienceSource(ExperienceSource $experienceSource= null)
    {
        $this->experienceSource = $experienceSource;

        return $this;
    }

    /**
     * Get Status
     *
     * @return null\int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set Status
     *
     * @param null|int $status
     *
     * @return Experience
     */
    public function setStatus($status= null)
    {
        $this->status = $status;

        return $this;
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
     * @return Experience
     */
    public function setEnded(\DateTime $ended = null)
    {
        $this->ended = $ended;

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
     * Get IsPublic
     *
     * @return bool
     */
    public function getIsPublic()
    {
        return $this->isPublic;
    }

    /**
     * Set IsPublic
     *
     * @param bool $isPublic IsPublic
     *
     * @return Experience
     */
    public function setIsPublic($isPublic)
    {
        $this->isPublic = $isPublic;

        return $this;
    }

    /**
     * Get Salary
     *
     * @return null|int
     */
    public function getSalary()
    {
        return $this->salary;
    }

    /**
     * Set Salary
     *
     * @param int $salary
     *
     * @return Experience
     */
    public function setSalary($salary)
    {
        $this->salary = $salary;

        return $this;
    }

    /**
     * Salary name
     *
     * @return string
     */
    public function getSalaryName()
    {
        $salaries = self::getSalaryNameList();

        return array_key_exists($this->getSalary(), $salaries) ? $salaries[$this->getSalary()] : '-';
    }

    /**
     * Get Sectors
     *
     * @return Sector[]|ArrayCollection
     */
    public function getSectors()
    {
        return $this->sectors;
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
     * @return Experience
     */
    public function setStarted(\DateTime $started)
    {
        $this->started = $started;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    abstract public function getType();

    /**
     * @return string
     */
    public function getTypeName()
    {
        $names = self::getTypeNameList();

        return array_key_exists($this->getType(), $names) ? $names[$this->getType()] : '-';
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
     * @return Experience
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return bool
     * @Assert\True(message="La date de fin doit être avant la date de début.")
     */
    public function isStartedBeforeEnded()
    {
        return
            null === $this->getEnded() ||
            $this->getStarted()->getTimestamp() < $this->getEnded()->getTimestamp();
    }

    /**
     * Remove Sector
     *
     * @param Sector $sector
     *
     * @return Experience
     */
    public function removeSector(Sector $sector)
    {
        if (true === $this->sectors->contains($sector)) {
            $this->sectors->removeElement($sector);
        }

        return $this;
    }
    
    public function toArray() 
    {
        $result = get_object_vars($this);
        foreach ((array)$this as $key => $value) 
        {
            $key = trim($key);
        }

        return $result;
    }

}
