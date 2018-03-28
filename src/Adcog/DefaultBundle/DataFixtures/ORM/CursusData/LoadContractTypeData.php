<?php

namespace Adcog\DefaultBundle\DataFixtures\ORM;

use Adcog\DefaultBundle\Entity\ContractType;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class LoadContractTypeData
 *
 * @author "Nicolas Drufin" <nicolas.drufin@ensc.fr>
 */
class LoadContractTypeData extends AbstractFixture
{
    /**
     * {@inheritdoc}
     */
    function load(ObjectManager $manager)
    {
        $contract1 = new ContractType();
        $contract1->setContent('CDD');
        $manager->persist($contract1);

        $contract2 = new ContractType();
        $contract2->setContent('CDI');
        $manager->persist($contract2);

        $contract3 = new ContractType();
        $contract3->setContent('Contrat de travail temporaire ou Intérim');
        $manager->persist($contract3);

        $contract4 = new ContractType();
        $contract4->setContent('Contrat d’apprentissage ou de professionnalisation');
        $manager->persist($contract4);

        $manager->flush();
    }
}
