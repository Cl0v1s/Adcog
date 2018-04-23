<?php

namespace Adcog\DefaultBundle\DataFixtures\ORM;

use Adcog\DefaultBundle\Entity\EmployerType;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class LoadEmployerTypeData
 *
 * @author "Nicolas Drufin" <nicolas.drufin@ensc.fr>
 */
class LoadEmployerTypeData extends AbstractFixture
{
    /**
     * {@inheritdoc}
     */
    function load(ObjectManager $manager)
    {
        $employerType1 = new EmployerType();
        $employerType1->setContent('GE');
        $manager->persist($employerType1);

        $employerType2 = new EmployerType();
        $employerType2->setContent('ETI');
        $manager->persist($employerType2);

        $employerType3 = new EmployerType();
        $employerType3->setContent('PME');
        $manager->persist($employerType3);

        $employerType4 = new EmployerType();
        $employerType4->setContent('TPE');
        $manager->persist($employerType4);

        $manager->flush();
    }
}
