<?php

namespace Adcog\DefaultBundle\DataFixtures\ORM;

use Adcog\DefaultBundle\Entity\ExperienceSource;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class LoadExperienceSourceData
 *
 * @author "Nicolas Drufin" <nicolas.drufin@ensc.fr>
 */
class LoadExperienceSourceData extends AbstractFixture
{
    /**
     * {@inheritdoc}
     */
    function load(ObjectManager $manager)
    {
        $expsource1 = new ExperienceSource();
        $expsource1->setContent('Site d’annonces d’emploi/stages');
        $manager->persist($expsource1);

        $expsource2 = new ExperienceSource();
        $expsource2->setContent('Réseau des anciens');
        $manager->persist($expsource2);

        $expsource3 = new ExperienceSource();
        $expsource3->setContent('Réseau Linkedin');
        $manager->persist($expsource3);

        $expsource4 = new ExperienceSource();
        $expsource4->setContent('Réseau familial/amis');
        $manager->persist($expsource4);

        $expsource5 = new ExperienceSource();
        $expsource5->setContent('Annonce sur liste de mails hors cognitiliste (ErgoIHM, ...)');
        $manager->persist($expsource5);

        $expsource6 = new ExperienceSource();
        $expsource6->setContent('Offre transmise par l’école');
        $manager->persist($expsource6);

        $expsource7 = new ExperienceSource();
        $expsource7->setContent('Contact direct par l’entreprise');
        $manager->persist($expsource7);

        $manager->flush();
    }
}
