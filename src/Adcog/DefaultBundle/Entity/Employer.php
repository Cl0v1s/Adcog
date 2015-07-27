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
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;

/**
 * Class Employer
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 * @ORM\Entity(repositoryClass="Adcog\DefaultBundle\Repository\EmployerRepository")
 * @ORM\Table(name="adcog_employer")
 */
class Employer implements CreatedInterface, UpdatedInterface, LoggableInterface, SlugInterface
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
     * @ORM\Column(type="string",length=255)
     * @Assert\NotNull()
     * @Assert\NotBlank()
     * @Assert\Length(max=255,min=2)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="text",nullable=true)
     */
    private $address;

    /**
     * @var string
     * @ORM\Column(type="string",length=20,nullable=true)
     * @Assert\Length(max=20)
     */
    private $zip;

    /**
     * @var string
     * @ORM\Column(type="string",length=255,nullable=true)
     * @Assert\Length(max=255)
     */
    private $city;

    /**
     * @var string
     * @ORM\Column(type="string",length=255,nullable=true)
     * @Assert\Length(max=255)
     */
    private $country;

    /**
     * @var string
     * @ORM\Column(type="string",length=255,nullable=true)
     * @Assert\Length(max=255)
     */
    private $phone;

    /**
     * @var string
     * @ORM\Column(type="string",length=255,nullable=true)
     * @Assert\Length(max=255)
     * @Assert\Url()
     */
    private $website;

    /**
     * @var string
     * @ORM\Column(type="string",length=255,nullable=true)
     * @Assert\Length(max=255)
     * @Assert\Email()
     */
    private $email;

    /**
     * @var Experience[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="Experience", mappedBy="employer")
     */
    private $experiences;

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        $this->setCountry('France');
        $this->experiences = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getStringToSlug()
    {
        return $this->getName();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getName();
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
     * Set Name
     *
     * @param string $name
     *
     * @return Employer
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
     * Set Address
     *
     * @param string $address
     *
     * @return Employer
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get Address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set Zip
     *
     * @param string $zip
     *
     * @return Employer
     */
    public function setZip($zip)
    {
        $this->zip = $zip;

        return $this;
    }

    /**
     * Get Zip
     *
     * @return string
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * Set City
     *
     * @param string $city
     *
     * @return Employer
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get City
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set Country
     *
     * @param string $country
     *
     * @return Employer
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get Country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set Phone
     *
     * @param string $phone
     *
     * @return Employer
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get Phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set Website
     *
     * @param string $website
     *
     * @return Employer
     */
    public function setWebsite($website)
    {
        $this->website = $website;

        return $this;
    }

    /**
     * Get Website
     *
     * @return string
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * Set Email
     *
     * @param string $email Email
     *
     * @return Employer
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get Email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Add experience
     *
     * @param Experience $experience
     *
     * @return Employer
     */
    public function addExperience(Experience $experience)
    {
        if (false === $this->experiences->contains($experience)) {
            $this->experiences[] = $experience;
            $experience->setEmployer($this);
        }

        return $this;
    }

    /**
     * Remove experience
     *
     * @param Experience $experience
     *
     * @return Employer
     */
    public function removeExperience(Experience $experience)
    {
        if (true === $this->experiences->contains($experience)) {
            $this->experiences->removeElement($experience);
            $experience->setEmployer(null);
        }

        return $this;
    }

    /**
     * Get experiences
     *
     * @return Experience[]|ArrayCollection
     */
    public function getExperiences()
    {
        return $this->experiences;
    }
}
