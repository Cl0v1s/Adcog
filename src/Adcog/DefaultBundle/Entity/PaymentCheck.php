<?php

namespace Adcog\DefaultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class PaymentCheck
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 * @ORM\Entity(repositoryClass="Adcog\DefaultBundle\Repository\PaymentRepository")
 */
class PaymentCheck extends Payment
{
    /**
     * @var null|string
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Type(type="string")
     * @Assert\Length(max=255)
     */
    private $checkNumber;

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        if(null === $number = $this->getCheckNumber()) {
            return sprintf('Chèque de %u€ par %s', $this->getAmount(), $this->getUser());
        }

        return sprintf('%u€ par chèque n°%s par %s', $this->getAmount(), $number, $this->getUser());
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return self::TYPE_CHECK;
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeName()
    {
        return 'Chèque';
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeIcon()
    {
        return 'pencil-square-o';
    }

    /**
     * Get CheckNumber
     *
     * @return string
     */
    public function getCheckNumber()
    {
        return $this->checkNumber;
    }

    /**
     * Set CheckNumber
     *
     * @param string $checkNumber CheckNumber
     *
     * @return PaymentCheck
     */
    public function setCheckNumber($checkNumber)
    {
        $this->checkNumber = $checkNumber;

        return $this;
    }
}
