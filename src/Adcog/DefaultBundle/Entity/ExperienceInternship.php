<?php

namespace Adcog\DefaultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ExperienceInternship
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 * @ORM\Entity(repositoryClass="Adcog\DefaultBundle\Repository\ExperienceRepository")
 */
class ExperienceInternship extends Experience
{
    /**
     * @var string
     * @ORM\Column(type="string", length=200)
     * @Assert\NotNull()
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(max=200)
     */
    private $internshipSubject;

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return sprintf('Stage "%s"', $this->getInternshipSubject());
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return self::TYPE_INTERNSHIP;
    }

    /**
     * {@inheritdoc}
     */
    public function getStringToSlug()
    {
        return (string)$this->getInternshipSubject();
    }

    /**
     * Set InternshipSubject
     *
     * @param string $internshipSubject
     *
     * @return ExperienceInternship
     */
    public function setInternshipSubject($internshipSubject)
    {
        $this->internshipSubject = $internshipSubject;

        return $this;
    }

    /**
     * Get InternshipSubject
     *
     * @return string
     */
    public function getInternshipSubject()
    {
        return $this->internshipSubject;
    }
}