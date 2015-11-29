<?php

namespace Adcog\DefaultBundle\StaticContent;

use Adcog\DefaultBundle\Entity\StaticContent;
use Doctrine\ORM\EntityManager;

/**
 * Class StaticManager
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class StaticManager
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var StaticContent[]
     */
    private $cache = [];

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @return StaticContent
     */
    public function getDefaultEnsc()
    {
        return $this->getContent(StaticContent::TYPE_DEFAULT_ENSC);
    }

    /**
     * @return StaticContent
     */
    public function getDefaultEventCognitoconf()
    {
        return $this->getContent(StaticContent::TYPE_DEFAULT_EVENT_COGNITOCONF);
    }

    /**
     * @return StaticContent
     */
    public function getDefaultEventCogout()
    {
        return $this->getContent(StaticContent::TYPE_DEFAULT_EVENT_COGOUT);
    }

    /**
     * @return StaticContent
     */
    public function getDefaultEventPicognitique()
    {
        return $this->getContent(StaticContent::TYPE_DEFAULT_EVENT_PICOGNITIQUE);
    }

    /**
     * @return StaticContent
     */
    public function getDefaultIndexJoin()
    {
        return $this->getContent(StaticContent::TYPE_DEFAULT_INDEX_JOIN);
    }

    /**
     * @return StaticContent
     */
    public function getDefaultIndexOrganization()
    {
        return $this->getContent(StaticContent::TYPE_DEFAULT_INDEX_ORGANIZATION);
    }

    /**
     * @return StaticContent
     */
    public function getDefaultIndexShare()
    {
        return $this->getContent(StaticContent::TYPE_DEFAULT_INDEX_SHARE);
    }

    /**
     * @return StaticContent
     */
    public function getDefaultIndexSpeek()
    {
        return $this->getContent(StaticContent::TYPE_DEFAULT_INDEX_SPEEK);
    }

    /**
     * @return StaticContent
     */
    public function getDefaultIndexWelcome()
    {
        return $this->getContent(StaticContent::TYPE_DEFAULT_INDEX_WELCOME);
    }

    /**
     * @return StaticContent
     */
    public function getDefaultIpb()
    {
        return $this->getContent(StaticContent::TYPE_DEFAULT_IPB);
    }

    /**
     * @return StaticContent
     */
    public function getDefaultPresentationList()
    {
        return $this->getContent(StaticContent::TYPE_DEFAULT_PRESENTATION_LIST);
    }

    /**
     * @return StaticContent
     */
    public function getDefaultPresentationOffice()
    {
        return $this->getContent(StaticContent::TYPE_DEFAULT_PRESENTATION_OFFICE);
    }

    /**
     * @return StaticContent
     */
    public function getDefaultPresentationOrganization()
    {
        return $this->getContent(StaticContent::TYPE_DEFAULT_PRESENTATION_ORGANIZATION);
    }

    /**
     * @return StaticContent
     */
    public function getDefaultRegisterGo()
    {
        return $this->getContent(StaticContent::TYPE_DEFAULT_REGISTER_GO);
    }

    /**
     * @return StaticContent
     */
    public function getDefaultRegisterMore()
    {
        return $this->getContent(StaticContent::TYPE_DEFAULT_REGISTER_MORE);
    }

    /**
     * @return StaticContent
     */
    public function getDefaultRegisterWhy()
    {
        return $this->getContent(StaticContent::TYPE_DEFAULT_REGISTER_WHY);
    }

    /**
     * Content
     *
     * @param string $key
     *
     * @return StaticContent
     */
    private function getContent($key)
    {
        if (false === array_key_exists($key, $this->cache)) {
            if (null === ($this->cache[$key] = $this->em->getRepository('AdcogDefaultBundle:StaticContent')->findOneBy(['type' => $key]))) {
                $this->cache[$key] = new StaticContent($key);
            }
        }

        return $this->cache[$key];
    }
}
