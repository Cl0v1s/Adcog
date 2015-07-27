<?php

namespace Adcog\DefaultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use EB\DoctrineBundle\Entity\CreatedInterface;
use EB\DoctrineBundle\Entity\Doctrine\CreatedTrait;
use EB\DoctrineBundle\Entity\Doctrine\FileReadableTrait;
use EB\DoctrineBundle\Entity\Doctrine\FileTrait;
use EB\DoctrineBundle\Entity\Doctrine\UpdatedTrait;
use EB\DoctrineBundle\Entity\FileReadableInterface;
use EB\DoctrineBundle\Entity\UpdatedInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class File
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 * @ORM\Entity(repositoryClass="Adcog\DefaultBundle\Repository\FileRepository")
 * @ORM\Table(name="adcog_file")
 */
class File implements FileReadableInterface, CreatedInterface, UpdatedInterface
{
    use CreatedTrait;
    use UpdatedTrait;
    use FileTrait;
    use FileReadableTrait;

    /**
     * @var int
     * @ORM\Column(type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return (string)$this->getFilename();
    }

    /**
     * {@inheritdoc}
     */
    public function __sleep()
    {
        return ['filename'];
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
}
