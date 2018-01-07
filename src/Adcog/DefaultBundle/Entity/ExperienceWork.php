<?php

namespace Adcog\DefaultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ExperienceWork
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 * @ORM\Entity(repositoryClass="Adcog\DefaultBundle\Repository\ExperienceRepository")
 */
class ExperienceWork extends Experience
{
    /**
     * @var string
     * @ORM\Column(type="string",length=255)
     * @Assert\NotNull()
     * @Assert\NotBlank()
     * @Assert\Length(max="255",min="2")
     */
    private $workPosition;

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return (string)$this->getWorkPosition();
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return self::TYPE_WORK;
    }

    /**
     * {@inheritdoc}
     */
    public function getStringToSlug()
    {
        return (string)$this->getWorkPosition();
    }

    /**
     * Set WorkPosition
     *
     * @param string $workPosition
     *
     * @return ExperienceWork
     */
    public function setWorkPosition($workPosition)
    {
        $this->workPosition = $workPosition;

        return $this;
    }

    /**
     * Get WorkPosition
     *
     * @return string
     */
    public function getWorkPosition()
    {
        return $this->workPosition;
    }

    /**
     * @var int 
     * @ORM\Column(type="integer")
     */
    private $partTime;

    /**
     * Set partTime
     *
     * @param null|int $partTime
     *
     * @return ExperienceWork
     */
    public function setPartTime($partTime =null)
    {
        if($partTime!=null){
            $this->partTime = $partTime;
        }
        else{
            $this->partTime = 0;
        }
        

        return $this;
    }

    /**
     * Get PartTime
     *
     * @return int
     */
    public function getPartTime()
    {
        return $this->partTime;
    }

    /**
     * @var ContractType
     * @ORM\OneToOne(targetEntity="ContractType", cascade={"persist"})
     */
    private $contractType;

    /**
     * Set contractType
     *
     * @param ContractType $contracType
     *
     * @return ExperienceWork
     */
    public function setContractType($contractType)
    {
       
        $this->contractType = $contractType;
        

        return $this;
    }

    /**
     * Get PartTime
     *
     * @return ContractType
     */
    public function getContractType()
    {
        return $this->contractType;
    }


}
