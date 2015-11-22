<?php

namespace Adcog\DefaultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use EB\DoctrineBundle\Entity\LoggableInterface;
use EB\DoctrineBundle\Entity\CreatedInterface;
use EB\DoctrineBundle\Entity\Doctrine\CreatedTrait;
use EB\DoctrineBundle\Entity\Doctrine\UpdatedTrait;
use EB\DoctrineBundle\Entity\UpdatedInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class StaticContent
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 * @ORM\Entity(repositoryClass="Adcog\DefaultBundle\Repository\StaticContentRepository")
 * @ORM\Table(name="adcog_static_content")
 * @UniqueEntity(fields={"type"}, errorPath="type")
 */
class StaticContent implements CreatedInterface, UpdatedInterface, LoggableInterface
{
    use CreatedTrait;
    use UpdatedTrait;

    const TYPE_DEFAULT_INDEX_WELCOME = 'TYPE_DEFAULT_INDEX_WELCOME';
    const TYPE_DEFAULT_INDEX_ORGANIZATION = 'TYPE_DEFAULT_INDEX_ORGANIZATION';
    const TYPE_DEFAULT_INDEX_JOIN = 'TYPE_DEFAULT_INDEX_JOIN';
    const TYPE_DEFAULT_INDEX_SPEEK = 'TYPE_DEFAULT_INDEX_SPEEK';
    const TYPE_DEFAULT_INDEX_SHARE = 'TYPE_DEFAULT_INDEX_SHARE';
    const TYPE_DEFAULT_PRESENTATION_ORGANIZATION = 'TYPE_DEFAULT_PRESENTATION_ORGANIZATION';
    const TYPE_DEFAULT_PRESENTATION_LIST = 'TYPE_DEFAULT_PRESENTATION_LIST';
    const TYPE_DEFAULT_PRESENTATION_OFFICE = 'TYPE_DEFAULT_PRESENTATION_OFFICE';
    const TYPE_DEFAULT_ENSC = 'TYPE_DEFAULT_ENSC';
    const TYPE_DEFAULT_INP_BORDEAUX = 'TYPE_DEFAULT_INP_BORDEAUX';
    const TYPE_DEFAULT_EVENT_PICOGNITIQUE = 'TYPE_DEFAULT_EVENT_PICOGNITIQUE';
    const TYPE_DEFAULT_EVENT_COGNITOCONF = 'TYPE_DEFAULT_EVENT_COGNITOCONF';
    const TYPE_DEFAULT_EVENT_COGOUT = 'TYPE_DEFAULT_EVENT_COGOUT';
    const TYPE_DEFAULT_REGISTER_GO = 'TYPE_DEFAULT_REGISTER_GO';
    const TYPE_DEFAULT_REGISTER_WHY = 'TYPE_DEFAULT_REGISTER_WHY';
    const TYPE_DEFAULT_REGISTER_MORE = 'TYPE_DEFAULT_REGISTER_MORE';

    /**
     * @var int
     * @ORM\Column(type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=50, unique=true)
     * @Assert\NotBlank()
     * @Assert\Length(max=50)
     * @Assert\Choice(callback="getTypeList")
     */
    private $type;
    /**
     * @var string
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     */
    private $title;
    /**
     * @var string
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     */
    private $content;

    /**
     * @param null|string $type
     */
    public function __construct($type = null)
    {
        $this->setType($type);
    }

    /**
     * @return string[]
     */
    public static function getTypeList()
    {
        return [
            self::TYPE_DEFAULT_INDEX_WELCOME,
            self::TYPE_DEFAULT_INDEX_ORGANIZATION,
            self::TYPE_DEFAULT_INDEX_JOIN,
            self::TYPE_DEFAULT_INDEX_SPEEK,
            self::TYPE_DEFAULT_INDEX_SHARE,
            self::TYPE_DEFAULT_PRESENTATION_ORGANIZATION,
            self::TYPE_DEFAULT_PRESENTATION_LIST,
            self::TYPE_DEFAULT_PRESENTATION_OFFICE,
            self::TYPE_DEFAULT_ENSC,
            self::TYPE_DEFAULT_INP_BORDEAUX,
            self::TYPE_DEFAULT_EVENT_PICOGNITIQUE,
            self::TYPE_DEFAULT_EVENT_COGNITOCONF,
            self::TYPE_DEFAULT_EVENT_COGOUT,
            self::TYPE_DEFAULT_REGISTER_GO,
            self::TYPE_DEFAULT_REGISTER_WHY,
            self::TYPE_DEFAULT_REGISTER_MORE,
        ];
    }

    /**
     * @return string
     */
    public function getTypeName()
    {
        $names = self::getTypeNameList();

        return array_key_exists($this->getType(), $names) ? $names[$this->getType()] : '-';
    }

    /**
     * @return string[]
     */
    public static function getTypeNameList()
    {
        return [
            self::TYPE_DEFAULT_INDEX_WELCOME => 'Accueil - Bienvenue',
            self::TYPE_DEFAULT_INDEX_ORGANIZATION => 'Accueil - Association',
            self::TYPE_DEFAULT_INDEX_JOIN => 'Accueil - Adhérez',
            self::TYPE_DEFAULT_INDEX_SPEEK => 'Accueil - Intervenez',
            self::TYPE_DEFAULT_INDEX_SHARE => 'Accueil - Diffusez',
            self::TYPE_DEFAULT_PRESENTATION_ORGANIZATION => 'Présentation - Association',
            self::TYPE_DEFAULT_PRESENTATION_LIST => 'Présentation - Listes',
            self::TYPE_DEFAULT_PRESENTATION_OFFICE => 'Présentation - Bureau',
            self::TYPE_DEFAULT_ENSC => 'ENSC',
            self::TYPE_DEFAULT_INP_BORDEAUX => 'INP',
            self::TYPE_DEFAULT_EVENT_PICOGNITIQUE => 'Événements - Pi\'Cognitique',
            self::TYPE_DEFAULT_EVENT_COGNITOCONF => 'Événements - Cognito\'Conf',
            self::TYPE_DEFAULT_EVENT_COGOUT => 'Événements - Cog\'Out',
            self::TYPE_DEFAULT_REGISTER_GO => 'Inscription - Go !',
            self::TYPE_DEFAULT_REGISTER_WHY => 'Inscription - Pourquoi',
            self::TYPE_DEFAULT_REGISTER_MORE => 'Inscription - Encore plus',
        ];
    }

    /**
     * Get Type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set Type
     *
     * @param string $type Type
     *
     * @return StaticContent
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return (string)$this->getTitle();
    }

    /**
     * Get Title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set Title
     *
     * @param string $title Title
     *
     * @return StaticContent
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
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
     * Get Content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set Content
     *
     * @param string $content Content
     *
     * @return StaticContent
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }
}
