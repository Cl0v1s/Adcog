<?php

namespace Adcog\DefaultBundle\DataFixtures\ORM;

use Adcog\DefaultBundle\Entity\School;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class LoadSchoolData
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class LoadSchoolData extends AbstractFixture
{
    /**
     * {@inheritdoc}
     */
    function load(ObjectManager $manager)
    {
        for ($year = 2007; $year < (int)date('Y'); $year++) {
            $school = new School();
            $school->setName(uniqid());
            $school->setYear($year);
            $manager->persist($school);
        }

        $manager->flush();
    }
}
 