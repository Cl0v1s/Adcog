<?php

namespace Adcog\DefaultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class PaymentCash
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 * @ORM\Entity(repositoryClass="Adcog\DefaultBundle\Repository\PaymentRepository")
 */
class PaymentCash extends Payment
{
    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return sprintf('%u€ en espèce par %s', $this->getAmount(), $this->getUser());
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return self::TYPE_CASH;
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeName()
    {
        return 'Espèces';
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeIcon()
    {
        return 'money';
    }
}
