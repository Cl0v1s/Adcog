<?php

namespace Adcog\DefaultBundle\EventListener;

use Adcog\DefaultBundle\Entity\Comment;
use Adcog\DefaultBundle\Entity\Payment;
use Adcog\DefaultBundle\Entity\User;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class DoctrineAlertEventListener
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class DoctrineAlertEventListener
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Persist
     *
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $ca = $this->container->getParameter('mail_ca');

        try {
            if ($entity instanceof User) {
                $this->container->get('eb_email')->send('user_persist_alert', ['CA' => $ca], ['user' => $entity]);
            }

            if ($entity instanceof Payment) {
                $this->container->get('eb_email')->send('payment_persist_alert', ['CA' => $ca], ['payment' => $entity]);
            }

            if ($entity instanceof Comment) {
                $this->container->get('eb_email')->send('comment_persist_alert', ['CA' => $ca], ['comment' => $entity]);
            }
        } catch (\Exception $e) {
            // No way this throws exceptions ...
            $this->container->get('logger')->error(sprintf('Error when sending email : %s', $e->getMessage()));
        }
    }
}
