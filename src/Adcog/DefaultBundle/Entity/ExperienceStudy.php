<?php

namespace Adcog\DefaultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ExperienceStudy
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 * @ORM\Entity(repositoryClass="Adcog\DefaultBundle\Repository\ExperienceRepository")
 */
class ExperienceStudy extends Experience
{
    /**
     * @var string
     * @ORM\Column(type="string",length=255)
     * @Assert\NotNull()
     * @Assert\NotBlank()
     * @Assert\Length(max="255",min="2")
     */
    private $studyDiploma;

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return sprintf('Formation "%s"', $this->getStudyDiploma());
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return self::TYPE_STUDY;
    }

    /**
     * {@inheritdoc}
     */
    public function getStringToSlug()
    {
        return (string)$this->getStudyDiploma();
    }

    /**
     * Set StudyDiploma
     *
     * @param string $studyDiploma
     *
     * @return ExperienceStudy
     */
    public function setStudyDiploma($studyDiploma)
    {
        $this->studyDiploma = $studyDiploma;

        return $this;
    }

    /**
     * Get StudyDiploma
     *
     * @return string
     */
    public function getStudyDiploma()
    {
        return $this->studyDiploma;
    }
}
