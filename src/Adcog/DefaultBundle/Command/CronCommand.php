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
        
        // Envoi des mails
        $this->sendMailToUsersExpired($output, $interval, $pas, $input->getOption('repeat'));
        
        return 0;
    }
    
    /**
     * Get member expired
     */
    private function sendMailToUsersExpired(OutputInterface $output, \DateInterval $interval, \DateInterval $pas, $repeat = false) 
    {
        // Obtient la connexion
        $em = $this->getContainer()->get('doctrine.orm.default_entity_manager');
        
        //Mailer
        $mailer = $this->getContainer()->get('eb_email.mailer.mailer');      
        
        // get users
        $users = $em->getRepository('AdcogDefaultBundle:User')->findBy(array());
            
        // post traitement with date interval
        $datenow = new \DateTime();
        foreach ($users as $user) 
        {
            // set mail model by default
            $mailmodel = 'user_expired';
            
            // get date limit
            if (null === $datelimit = $user->getLastPaymentEnded())
            {
                // Verification de la fin de période scolaire
                if (null !== $school = $user->getSchool()) {
                    $datelimit = \DateTime::createFromFormat('Ymd', sprintf('%s0901', $school->getYear()));
                    $mailmodel = 'user_join_member';
                } 
            }

            // if has a date of account expiration
            if (null !== $datelimit)
            {
                // if day of end
                $datelimitstart = clone $datelimit;
                $datelimitend = clone $datelimit;
                $datelimitend->add($pas);
                if (($datelimit < $datenow) && ($datelimitend > $datenow)) {
                    $users_expired[] = $user;
                } else {
                    // loop for repeat
                    do {
                        // define date start and end
                        $datelimit->add($interval);
                        $datelimitend = clone $datelimit;
                        $datelimitend->add($pas);
                        // compare date 
                        if (($datelimit < $datenow) && ($datelimitend > $datenow))
                        {
                            // Affichage (debug)
                            if ($output->isVerbose()) {
                                if ('user_expired' == $mailmodel) {
                                    $output->writeln(sprintf("%s %s a expiré depuis le %s", $user->getFirstname(), $user->getLastname(), $datelimitstart->format('d/m/Y')));
                                } else {
                                    $output->writeln(sprintf("%s %s n'est plus étudiant depuis le %s", $user->getFirstname(), $user->getLastname(), $datelimitstart->format('d/m/Y')));
                                }
                            }
                            
                            // Envoi du mail
                            $mailer->send($mailmodel, $user, [
                                'user' => $user,
                                'expiration_date' => $datelimitstart
                            ]);
                            break;
                        }
                    } while (($datelimit < $datenow) && (true === $repeat));
                }
            }
        }
    }
}
