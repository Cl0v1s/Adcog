<?php

namespace Adcog\DefaultBundle\EventListener;

use Adcog\DefaultBundle\Entity\Payment;
use Adcog\DefaultBundle\Entity\User;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use EB\EmailBundle\Mailer\Mailer;

/**
 * Class DoctrineUserEventListener
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class DoctrineUserEventListener
{
    /**
     * @var Mailer
     */
    private $mailer;

    /**
     * Set Mailer
     *
     * @param Mailer $mailer
     */
    public function __construct($mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof User) {
            $this->mailer->send('user_persist', $entity, ['user' => $entity]);
        }

        if ($entity instanceof Payment) {
            if (null !== $user = $entity->getUser()) {
                $this->mailer->send('payment_persist', $user, ['payment' => $entity, 'user' => $user]);
            }
        }
    }

    /**
     * @param PreUpdateEventArgs $args
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof User) {
            if (true === $args->hasChangedField('username')) {
                $this->mailer->send('user_update_email', $args->getOldValue('username'), ['user' => $entity]);
                $this->mailer->send('user_update_email', $entity, ['user' => $entity]);
            }

            if (true === $args->hasChangedField('password')) {
                $this->mailer->send('user_update_password', $entity, ['user' => $entity]);
            }
        }

        if ($entity instanceof Payment) {
            if (null !== $user = $entity->getUser()) {
                if (true === $args->hasChangedField('validated')) {
                    if (null === $args->getOldValue('validated')) {
                        $this->mailer->send('payment_validate', $user, ['payment' => $entity, 'user' => $user]);
                    }
                }
                if (true === $args->hasChangedField('invalidated')) {
                    if (null === $args->getOldValue('invalidated')) {
                        $this->mailer->send('payment_invalidate', $user, ['payment' => $entity, 'user' => $user]);
                    }
                }
            }
        }
    }
}
