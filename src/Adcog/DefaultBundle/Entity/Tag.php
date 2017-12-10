<?php

namespace Adcog\DefaultBundle\Entity;

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
 * Class Tag
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 * @ORM\Entity(repositoryClass="Adcog\DefaultBundle\Repository\TagRepository")
 * @ORM\Table(name="adcog_tag")
 */
class Tag implements SlugInterface, CreatedInterface, UpdatedInterface, LoggableInterface
{
    use SlugTrait;
    use CreatedTrait;
    use UpdatedTrait;

    /**
     * @var int
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(max=255)
     */
    private $content;

    /**
     * @param string $content
     *
     * @return Tag
     */
    public static function create($content)
    {
        $tag = new self();
        $tag->setContent($content);

        return $tag;
    }

    /**
     * {@inheritdoc}
     */
    function __toString()
    {
        return (string)$this->getContent();
    }

    /**
     * {@inheritdoc}
     */
    public function getStringToSlug()
    {
        return (string)$this;
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
     * Set content
     *
     * @param string $content
     *
     * @return Tag
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
