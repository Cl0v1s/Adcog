<?php

namespace Adcog\DefaultBundle\Entity;

use Adcog\DefaultBundle\Entity\Common\ValidatedTrait;
use Doctrine\ORM\Mapping as ORM;
use EB\DoctrineBundle\Entity\LoggableInterface;
use EB\DoctrineBundle\Entity\CreatedInterface;
use EB\DoctrineBundle\Entity\Doctrine\CreatedTrait;
use EB\DoctrineBundle\Entity\Doctrine\UpdatedTrait;
use EB\DoctrineBundle\Entity\UpdatedInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Comment
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 * @ORM\Entity(repositoryClass="Adcog\DefaultBundle\Repository\CommentRepository")
 * @ORM\Table(name="adcog_comment")
 */
class Comment implements CreatedInterface, UpdatedInterface, LoggableInterface
{
    use CreatedTrait;
    use UpdatedTrait;
    use ValidatedTrait;

    /**
     * @var int
     * @ORM\Column(type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User", inversedBy="comments")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Assert\NotBlank()
     */
    private $author;

    /**
     * @var Post
     * @ORM\ManyToOne(targetEntity="Post", inversedBy="comments")
     * @Assert\NotBlank()
     */
    private $post;

    /**
     * @var string
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(min=2)
     */
    private $text;

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return sprintf('Commentaire %s', $this->getId());
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
     * @return Comment
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
     * @return Comment
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get author
     *
     * @return User
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set author
     *
     * @param User $author
     *
     * @return Comment
     */
    public function setAuthor(User $author = null)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get post
     *
     * @return Post
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * Set post
     *
     * @param Post $post
     *
     * @return Comment
     */
    public function setPost(Post $post = null)
    {
        $this->post = $post;

        return $this;
    }
}
