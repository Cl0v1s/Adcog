<?php

namespace Adcog\DefaultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use EB\DoctrineBundle\Entity\CreatedInterface;
use EB\DoctrineBundle\Entity\Doctrine\CreatedTrait;
use EB\DoctrineBundle\Entity\Doctrine\UpdatedTrait;
use EB\DoctrineBundle\Entity\UpdatedInterface;

/**
 * ExperienceSource
 *
 * @ORM\Table(name="adcog_experience_source")
 * @ORM\Entity(repositoryClass="Adcog\DefaultBundle\Repository\ExperienceSourceRepository")
 */
class ExperienceSource implements CreatedInterface, UpdatedInterface
{
    use CreatedTrait;
    use UpdatedTrait;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Experience[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="Experience", mappedBy="experienceSource")
     */
    private $experiences;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="string", length=60)
     */
    private $content;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return ExperienceSource
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }

     /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->getContent();   
    }

    /**
     * Add experience
     *
     * @param Experience $experience
     *
     * @return ExperienceSource
     */
    public function addExperience(Experience $experience)
    {
        if (false === $this->experiences->contains($experience)) {
            $this->experiences[] = $experience;
            $experience->setExperienceSource($this);
        }

        return $this;
    }

    /**
     * Remove experience
     *
     * @param Experience $experience
     *
     * @return ExperienceSource
     */
    public function removeExperience(Experience $experience)
    {
        if (true === $this->experiences->contains($experience)) {
            $this->experiences->removeElement($experience);
            $experience->setExperienceSource(null);
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
