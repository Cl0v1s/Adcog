<?php

namespace Adcog\DefaultBundle\DataFixtures\ORM;

use Adcog\DefaultBundle\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class LoadUserData
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class LoadUserData extends AbstractFixture
{
    /**
     * {@inheritdoc}
     */
    function load(ObjectManager $manager)
    {
        $user = new User();
        $user
            ->setUsername('cpi@adcog.fr')
            ->setFirstname('CPI')
            ->setLastname('ADCOG')
            ->setRole(User::ROLE_ADMIN)
            ->setRawPassword('pass')
            ->setBirthDate(new \Datetime());
        $manager->persist($user);

        $this->addReference('cpi', $user);

        foreach (['president', 'tresorier', 'secretaire', 'ce', 'cc'] as $username) {
            $user = new User();
            $user->setUsername($username . '@adcog.fr');
            $user->setFirstname(strtoupper($username));
            $user->setLastname('ADCOG');
            $user->setRawPassword('pass');
            $user->setBirthDate(new \Datetime());
            $manager->persist($user);
        }

        $manager->flush();
    }
}
