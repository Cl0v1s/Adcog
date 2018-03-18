<?php

namespace Adcog\DefaultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EmployerType
 *
 * @ORM\Table(name="adcog_employerType")
 * @ORM\Entity
 */
class EmployerType
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
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
     * {@inheritdoc}
     */
    public function __toString()
    {
        return (string)$this->getContent();
    }

    /**
     * Set content
     *
     * @param string $content
     * @return EmployerType
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
}
