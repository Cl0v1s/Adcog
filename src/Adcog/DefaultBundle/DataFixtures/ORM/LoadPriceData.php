<?php

namespace Adcog\DefaultBundle\DataFixtures\ORM;

use Adcog\DefaultBundle\Entity\Price;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class LoadPriceData
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class LoadPriceData extends AbstractFixture
{
    /**
     * {@inheritdoc}
     */
    function load(ObjectManager $manager)
    {
        $price1 = new Price();
        $price1->setAmount(3);
        $price1->setDescription('Inscription à l\'Association des Diplômés en Cognitique pour un durée de trois ans à compter de ce jour.');
        $price1->setDuration(3);
        $price1->setTitle('Elève de l\'ENSC');
        $manager->persist($price1);

        $price2 = new Price();
        $price2->setAmount(10);
        $price2->setDescription('Inscription à l\'Association des Diplômés en Cognitique pour un durée d\'un an à compter de ce jour.');
        $price2->setDuration(1);
        $price2->setTitle('Sans emploi - En poursuite d\'étude');
        $manager->persist($price2);

        $price3 = new Price();
        $price3->setAmount(20);
        $price3->setDescription('Inscription à l\'Association des Diplômés en Cognitique pour un durée d\'un an à compter de ce jour.');
        $price3->setDuration(1);
        $price3->setTitle('En activité - Salarié');
        $manager->persist($price3);

        $manager->flush();
    }
}
