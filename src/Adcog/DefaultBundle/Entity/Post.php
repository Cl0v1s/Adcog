<?php

namespace Adcog\DefaultBundle\Entity;

use Adcog\DefaultBundle\Entity\Common\ValidatedTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use EB\DoctrineBundle\Entity\LoggableInterface;
use EB\DoctrineBundle\Entity\CreatedInterface;
use EB\DoctrineBundle\Entity\Doctrine\CreatedTrait;
use EB\DoctrineBundle\Entity\Doctrine\FileReadableTrait;
use EB\DoctrineBundle\Entity\Doctrine\FileTrait;
use EB\DoctrineBundle\Entity\Doctrine\FileVersionableTrait;
use EB\DoctrineBundle\Entity\Doctrine\SlugTrait;
use EB\DoctrineBundle\Entity\Doctrine\UpdatedTrait;
use EB\DoctrineBundle\Entity\FileReadableInterface;
use EB\DoctrineBundle\Entity\FileVersionableInterface;
use EB\DoctrineBundle\Entity\SlugInterface;
use EB\DoctrineBundle\Entity\UpdatedInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Class Post
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 * @ORM\Entity(repositoryClass="Adcog\DefaultBundle\Repository\PostRepository")
 * @ORM\Table(name="adcog_post")
 * @Assert\Callback("validate")
 */
class Post implements CreatedInterface, UpdatedInterface, SlugInterface, LoggableInterface, FileReadableInterface, FileVersionableInterface
{
    use CreatedTrait;
    use UpdatedTrait;
    use SlugTrait;
    use FileTrait;
    use FileReadableTrait;
    use FileVersionableTrait;
    use ValidatedTrait;

    /**
     * @var int
     * @ORM\Column(type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(min=2, max=255)
     */
    private $title;

    /**
     * @var string
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(min=50)
     */
    private $text;

    /**
     * @var Comment[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="post")
     */
    private $comments;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Assert\NotBlank()
     */
    private $author;

    /**
     * @var Tag[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity="Tag", cascade={"persist"})
     * @ORM\JoinTable(name="post_tag")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Assert\Count(min=1)
     */
    private $tags;

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->tags = new ArrayCollection();
    }

    /**
     * Validate
     *
     * @param ExecutionContextInterface $context
     */
    public function validate(ExecutionContextInterface $context)
    {
        $context->getViolations()->addAll($context->getValidator()->validate($this->getFile(), [new Assert\Image()]));
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->getTitle();
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Post
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getStringToSlug()
    {
        return $this->getTitle();
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
     * Set Id
     *
     * @param int $id Id
     *
     * @return Post
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set text
     *
     * @param string $text
     *
     * @return Post
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Add comment
     *
     * @param Comment $comment
     *
     * @return Post
     */
    public function addComment(Comment $comment)
    {
        if (false === $this->comments->contains($this)) {
            $this->comments[] = $comment;
            $comment->setPost($this);
        }

        return $this;
    }

    /**
     * Remove comment
     *
     * @param Comment $comment
     *
     * @return Post
     */
    public function removeComment(Comment $comment)
    {
        if (true === $this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            $comment->setPost(null);
        }

        return $this;
    }

    /**
     * Get comments
     *
     * @return Comment[]|ArrayCollection
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Get Author
     *
     * @return Post
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set Author
     *
     * @param User $author
     *
     * @return Post
     */
    public function setAuthor(User $author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Add tag
     *
     * @param Tag $tag
     *
     * @return Post
     */
    public function addTag(Tag $tag)
    {
        if (false === $this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }

        return $this;
    }

    /**
     * Remove tag
     *
     * @param Tag $tag
     *
     * @return Post
     */
    public function removeTag(Tag $tag)
    {
        if (true === $this->tags->contains($tag)) {
            $this->tags->removeElement($tag);
        }

        return $this;
    }

    /**
     * Get tags
     *
     * @return Tag[]|ArrayCollection
     */
    public function getTags()
    {
        return $this->tags;
    }
}