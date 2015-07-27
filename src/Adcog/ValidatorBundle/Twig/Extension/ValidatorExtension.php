<?php

namespace Adcog\ValidatorBundle\Twig\Extension;

use Doctrine\ORM\EntityManager;

/**
 * Class ValidatorExtension
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class ValidatorExtension extends \Twig_Extension
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var int[]
     */
    private $counts = null;

    /**
     * @param string        $name Name
     * @param EntityManager $em   Manager
     */
    public function __construct($name, EntityManager $em)
    {
        $this->name = $name;
        $this->em = $em;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('validator_count', [$this, 'getCount']),
        ];
    }

    /**
     * Count
     *
     * @param string $domain
     *
     * @return null|int
     */
    public function getCount($domain)
    {
        if (null === $this->counts) {
            $this->counts = [
                'comment' => $this->em->getRepository('AdcogDefaultBundle:Comment')->countUnvalidatedItems(),
                'payment' => $this->em->getRepository('AdcogDefaultBundle:Payment')->countUnvalidatedItems(),
                'user' => $this->em->getRepository('AdcogDefaultBundle:User')->countUnvalidatedItems(),
                'bookmark' => $this->em->getRepository('AdcogDefaultBundle:Bookmark')->countUnvalidatedItems(),
            ];
        }

        return array_key_exists($domain, $this->counts) ? $this->counts[$domain] : null;
    }
}
