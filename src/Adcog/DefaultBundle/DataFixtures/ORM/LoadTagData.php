<?php

namespace Adcog\DefaultBundle\DataFixtures\ORM;

use Adcog\DefaultBundle\Entity\Tag;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class LoadTagData
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class LoadTagData extends AbstractFixture
{
    /**
     * {@inheritdoc}
     */
    function load(ObjectManager $manager)
    {
        foreach (['Cognito\'Conf', 'Bilan'] as $tagContent) {
            $tag = new Tag();
            $tag->setContent($tagContent);
            $manager->persist($tag);

            $this->setReference($tag->getContent(), $tag);
        }

        $manager->flush();
    }
}
