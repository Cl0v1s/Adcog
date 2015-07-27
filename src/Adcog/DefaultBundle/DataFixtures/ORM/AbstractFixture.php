<?php

namespace Adcog\DefaultBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture as BaseAbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

/**
 * Class AbstractFixture
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
abstract class AbstractFixture extends BaseAbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    function getOrder()
    {
        return 1;
    }
}
