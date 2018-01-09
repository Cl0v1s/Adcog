<?php

namespace Adcog\DefaultBundle\Entity;

use Adcog\DefaultBundle\Entity\Common\PriceTrait;
use Adcog\DefaultBundle\Entity\Common\ValidatedTrait;
use Doctrine\ORM\Mapping as ORM;
use EB\DoctrineBundle\Entity\LoggableInterface;
use EB\DoctrineBundle\Entity\CreatedInterface;
use EB\DoctrineBundle\Entity\Doctrine\CreatedTrait;
use EB\DoctrineBundle\Entity\Doctrine\UpdatedTrait;
use EB\DoctrineBundle\Entity\UpdatedInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Payment
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 * @ORM\Entity(repositoryClass="Adcog\DefaultBundle\Repository\PaymentRepository")
 * @ORM\Table(name="adcog_payment")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr",type="string")
 * @ORM\DiscriminatorMap({"paypal"="PaymentPaypal","transfer"="PaymentTransfer","check":"PaymentCheck","cash":"PaymentCash"})
 */
abstract class Payment implements CreatedInterface, UpdatedInterface, LoggableInterface
{
    use CreatedTrait;
    use UpdatedTrait;
    use PriceTrait;
    use ValidatedTrait;

    const TYPE_CASH = 'cash';
    const TYPE_CHECK = 'check';
    const TYPE_PAYPAL = 'paypal';
    const TYPE_TRANSFER = 'transfer';

    /**
     * @var int
     * @ORM\Column(type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User", inversedBy="payments")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Assert\NotBlank()
     */
    private $user;

    /**
     * @param null|Price $price
     */
    public function __construct(Price $price = null)
    {
        if (null !== $price) {
            $this->copyPrice($price);
        }
    }

    /**
     * {@inheritdoc}
     */
    abstract public function __toString();

    /**
     * Create
     *
     * @param string     $type  Type
     * @param null|Price $price Price
     *
     * @return Payment
     */
    public static function createObject($type, Price $price = null)
    {
        $class = get_class() . mb_convert_case($type, MB_CASE_TITLE);

        return new $class($price);
    }

    /**
     * @return string[]
     */
    static public function getTypeList()
    {
        return [
            self::TYPE_PAYPAL,
            self::TYPE_TRANSFER,
            self::TYPE_CHECK,
            self::TYPE_CASH,
        ];
    }

    /**
     * @return string[]
     */
    static public function getTypeNameList()
    {
        return [
            self::TYPE_PAYPAL => 'Paypal',
            self::TYPE_TRANSFER => 'Virement',
            self::TYPE_CHECK => 'Chèque',
            self::TYPE_CASH => 'Espèces',
        ];
    }

    /**
     * Copy price
     *
     * @param Price $price Price
     *
     * @return Payment
     */
    public function copyPrice(Price $price)
    {
        $this
            ->setTitle($price->getTitle())
            ->setDescription($price->getDescription())
            ->setDuration($price->getDuration())
            ->setAmount($price->getAmount())
            ->setUser($this->getUser());

        return $this;
    }

    /**
     * Ended
     *
     * @return null|\DateTime
     */
    public function getEnded()
    {
        $ended = null;
        if (null !== $validated = $this->getValidated()) {
            $ended = clone $validated;
            $ended->add(new \DateInterval(sprintf('P%uY', $this->getDuration())));
        }

        return $ended;
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
     * @return Payment
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    abstract public function getType();

    /**
     * @return string
     */
    abstract public function getTypeIcon();

    /**
     * @return string
     */
    abstract public function getTypeName();

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set user
     *
     * @param User $user
     *
     * @return Payment
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Is active
     *
     * @return null|\DateTime
     */
    public function isActive()
    {
        if (null !== $ended = $this->getEnded()) {
            return $ended->getTimestamp() > time();
        }

        return false;
    }
}
