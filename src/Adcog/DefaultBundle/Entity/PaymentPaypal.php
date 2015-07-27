<?php

namespace Adcog\DefaultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class PaymentPaypal
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 * @ORM\Entity(repositoryClass="Adcog\DefaultBundle\Repository\PaymentRepository")
 */
class PaymentPaypal extends Payment
{
    /**
     * @var string
     * @ORM\Column(type="string", length=200, nullable=true)
     * @Assert\Length(max=200)
     */
    private $tokenId;

    /**
     * @var null|string
     * @ORM\Column(type="string", length=200, nullable=true)
     * @Assert\Length(max=200)
     */
    private $paymentId;

    /**
     * @var null|string
     * @ORM\Column(type="string", length=200, nullable=true)
     * @Assert\Length(max=200)
     */
    private $payerId;

    /**
     * @var array
     * @ORM\Column(type="array")
     */
    private $token = [];

    /**
     * @var array
     * @ORM\Column(type="array")
     */
    private $payment = [];

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return sprintf('%u€ via Paypal (%s) par %s', $this->getAmount(), $this->getTokenId() ? : 'pas de token', $this->getUser());
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return self::TYPE_PAYPAL;
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeName()
    {
        return 'Paypal';
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeIcon()
    {
        return 'qrcode';
    }

    /**
     * Get PayerId
     *
     * @return null|string
     */
    public function getPayerId()
    {
        return $this->payerId;
    }

    /**
     * Set PayerId
     *
     * @param null|string $payerId PayerId
     *
     * @return PaymentPaypal
     */
    public function setPayerId($payerId)
    {
        $this->payerId = $payerId;

        return $this;
    }

    /**
     * Get Payment
     *
     * @return array
     */
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * Set Payment
     *
     * @param array $payment Payment
     *
     * @return PaymentPaypal
     */
    public function setPayment(array $payment)
    {
        $this->payment = $payment;
        if (array_key_exists('id', $payment)) {
            $this->setPaymentId($payment['id']);
        }

        return $this;
    }

    /**
     * Get PaymentId
     *
     * @return null|string
     */
    public function getPaymentId()
    {
        return $this->paymentId;
    }

    /**
     * Set PaymentId
     *
     * @param null|string $paymentId PaymentId
     *
     * @return PaymentPaypal
     */
    public function setPaymentId($paymentId)
    {
        $this->paymentId = $paymentId;

        return $this;
    }

    /**
     * Get token
     *
     * @return array
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set token
     *
     * @param array $token
     *
     * @return PaymentPaypal
     */
    public function setToken(array $token)
    {
        $this->token = $token;
        if (array_key_exists('access_token', $token)) {
            $this->setTokenId($token['access_token']);
        }

        return $this;
    }

    /**
     * Get tokenId
     *
     * @return string
     */
    public function getTokenId()
    {
        return $this->tokenId;
    }

    /**
     * Set tokenId
     *
     * @param string $tokenId
     *
     * @return PaymentPaypal
     */
    public function setTokenId($tokenId)
    {
        $this->tokenId = $tokenId;

        return $this;
    }
}
