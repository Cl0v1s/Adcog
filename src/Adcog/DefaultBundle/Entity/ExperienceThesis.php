<?php

namespace Adcog\DefaultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ExperienceThesis
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 * @ORM\Entity(repositoryClass="Adcog\DefaultBundle\Repository\ExperienceRepository")
 */
class ExperienceThesis extends Experience
{
    /**
     * @var string
     * @ORM\Column(type="string",length=255)
     * @Assert\NotNull()
     * @Assert\NotBlank()
     * @Assert\Length(max="255",min="2")
     */
    private $thesisType;

    /**
     * @var string
     * @ORM\Column(type="string",length=255)
     * @Assert\NotNull()
     * @Assert\NotBlank()
     * @Assert\Length(max="255",min="2")
     */
    private $thesisDiscipline;

    /**
     * @var string
     * @ORM\Column(type="text")
     * @Assert\NotNull()
     * @Assert\NotBlank()
     */
    private $thesisSubject;

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return sprintf('Thèse %s "%s"', $this->getThesisType(), $this->getThesisSubject());
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return self::TYPE_THESIS;
    }

    /**
     * {@inheritdoc}
     */
    public function getStringToSlug()
    {
        return (string)$this->getThesisSubject();
    }

    /**
     * Set ThesisType
     *
     * @param string $thesisType
     *
     * @return ExperienceThesis
     */
    public function setThesisType($thesisType)
    {
        $this->thesisType = $thesisType;

        return $this;
    }

    /**
     * Get ThesisType
     *
     * @return string
     */
    public function getThesisType()
    {
        return $this->thesisType;
    }

    /**
     * Set ThesisDiscipline
     *
     * @param string $thesisDiscipline
     *
     * @return ExperienceThesis
     */
    public function setThesisDiscipline($thesisDiscipline)
    {
        $this->thesisDiscipline = $thesisDiscipline;

        return $this;
    }

    /**
     * Get ThesisDiscipline
     *
     * @return string
     */
    public function getThesisDiscipline()
    {
        return $this->thesisDiscipline;
    }

    /**
     * Set ThesisSubject
     *
     * @param string $thesisSubject
     *
     * @return ExperienceThesis
     */
    public function setThesisSubject($thesisSubject)
    {
        $this->thesisSubject = $thesisSubject;

        return $this;
    }

    /**
     * Get ThesisSubject
     *
     * @return string
     */
    public function getThesisSubject()
    {
        return $this->thesisSubject;
    }
}
