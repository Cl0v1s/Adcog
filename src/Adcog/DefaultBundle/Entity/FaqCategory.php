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
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class FaqCategory
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 * @ORM\Entity(repositoryClass="Adcog\DefaultBundle\Repository\FaqCategoryRepository")
 * @ORM\Table(name="adcog_faq_category")
 * @UniqueEntity(fields={"name"}, errorPath="name")
 */
class FaqCategory implements CreatedInterface, UpdatedInterface, SlugInterface, LoggableInterface
{
    use CreatedTrait;
    use UpdatedTrait;
    use SlugTrait;

    /**
     * @var int
     * @ORM\Column(type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Faq[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="Faq", mappedBy="category")
     * @Assert\NotBlank()
     */
    private $faqs;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     */
    private $name;

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        $this->faqs = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getStringToSlug()
    {
        return $this->getName();
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
     * @param string $name Name
     *
     * @return FaqCategory
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return (string)$this->getName() ;
    }

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
     * Get Faqs
     *
     * @return Faq[]|ArrayCollection
     */
    public function getFaqs()
    {
        return $this->faqs;
    }

    /**
     * Add faq
     *
     * @param Faq $faq
     *
     * @return FaqCategory
     */
    public function addFaq(Faq $faq)
    {
        if (false === $this->faqs->contains($faq)) {
            $this->faqs->add($faq);
            $faq->setCategory($this);
        }

        return $this;
    }

    /**
     * Remove faq
     *
     * @param Faq $faq
     *
     * @return FaqCategory
     */
    public function removeFaq(Faq $faq)
    {
        if (true === $this->faqs->contains($faq)) {
            $this->faqs->removeElement($faq);
            $faq->setCategory(null);
        }

        return $this;
    }
}
