<?php

namespace Adcog\DefaultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class PaymentTransfer
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 * @ORM\Entity(repositoryClass="Adcog\DefaultBundle\Repository\PaymentRepository")
 */
class PaymentTransfer extends Payment
{
    /**
     * @var null|\DateTime
     * @ORM\Column(type="datetime", nullable=true)
     * @Assert\DateTime()
     */
    private $transferDate;

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        if (null === $transferDate = $this->getTransferDate()) {
            return sprintf('Virement de %u€ par %s', $this->getAmount(), $this->getUser());
        }

        return sprintf('Virement de %u€ le %s par %s', $this->getAmount(), $transferDate->format('d/m/Y'), $this->getUser());
    }

    /**
     * Get TransferDate
     *
     * @return null|\DateTime
     */
    public function getTransferDate()
    {
        return $this->transferDate;
    }

    /**
     * Set TransferDate
     *
     * @param null|\DateTime $transferDate TransferDate
     *
     * @return PaymentTransfer
     */
    public function setTransferDate(\DateTime $transferDate = null)
    {
        $this->transferDate = $transferDate;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return self::TYPE_TRANSFER;
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeName()
    {
        return 'Virement';
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeIcon()
    {
        return 'globe';
    }
}
