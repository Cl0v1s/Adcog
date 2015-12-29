<?php

namespace Adcog\DefaultBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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
            ->setDescription('Cron for all ADCOG jobs')
            ->addOption(
                'interval',
                'i',
                InputOption::VALUE_OPTIONAL,
                'L\'intervale de temps depuis que le membre a expiré',
                'P6M'
            )
            ->addOption(
                'step',
                'p',
                InputOption::VALUE_OPTIONAL,
                'Le pas de la commande planifiée (cron)',
                'P1D'
            )
            ->addOption(
               'repeat',
               'r',
               InputOption::VALUE_NONE,
               'Permet de répéter l\'intervalle dans le temps jusqu\'à aujourd\'hui'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Intervale et pas
        $interval = new \DateInterval($input->getOption('interval'));
        $pas = new \DateInterval($input->getOption('step'));
        
        // Obtient la connexion
        $em = $this->getContainer()->get('doctrine.orm.default_entity_manager');
        
        // Recherche sur les utilisateurs
        $users_array = $em->getRepository('AdcogDefaultBundle:User')->getUsersExpiredAt($interval, $pas, $input->getOption('repeat'));
        
        // Envoi des mails
        foreach ($users_array as $user) 
        {
            // Affichage (debug)
            if ($output->isVerbose()) {
                $output->writeln(sprintf("%s %s a expiré depuis le %s", $user->getFirstname(), $user->getLastname(), $user->getLastPaymentEnded()->format('d/m/Y')));
            }
            
            // Envoi du mail
            $this->getContainer()->get('eb_email.mailer.mailer')->send('user_expired', $user, [
                'user' => $user,
                'expiration_date' => $user->getLastPaymentEnded()
            ]);
        }
        
        return 0;
    }
}
