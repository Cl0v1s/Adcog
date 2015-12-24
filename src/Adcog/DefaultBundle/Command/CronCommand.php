<?php

namespace Adcog\DefaultBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CronCommand
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class CronCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('adcog:cron')
            ->setDescription('Cron for all ADCOG jobs');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // @todo rappel tous les 6 mois
        
        // Date courante
        $now = new \DateTime();
        
        // Obtient la connexion
        $em = $this->getContainer()->get('doctrine.orm.default_entity_manager');
        
        // Recherche sur les utilisateurs
        $email_array = $em->getRepository('AdcogDefaultBundle:User');
        
        return 0;
    }
}
