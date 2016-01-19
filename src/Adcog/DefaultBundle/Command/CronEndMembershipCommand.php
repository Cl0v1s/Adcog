<?php

namespace Adcog\DefaultBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CronEndMembershipCommand
 *
 * @author "Nicolas Drufin" <nicolas.drufin@gmail.com>
 */
class CronEndMembershipCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('adcog:cron-end-of-membership')
            ->setDescription('Cron to send mail after end of membership')
            ->addOption(
                'months-interval',
                'm',
                InputOption::VALUE_OPTIONAL,
                'L\'intervale de temps en mois depuis que le membre a expiré, cumulable avec les jours',
                '0'
            )
            ->addOption(
                'days-interval',
                'd',
                InputOption::VALUE_OPTIONAL,
                'L\'intervale de temps en jours depuis que le membre a expiré, cumulable avec les mois',
                '0'
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
        // Mois, jours et repeat
        $months = $input->getOption('months-interval');
        $days = $input->getOption('days-interval');
        $repeat = $input->getOption('repeat');
        
        // Test des variables : cas d'erreur
        if (!is_numeric($months) || !is_int((int)$months) || !is_numeric($days) || !is_int((int)$days)) 
        {
            $output->writeln("Les options mois et/ou jours sont incorrectes, les valeurs doivent être des entiers.");
            return 1;
        }
        else if (($months == "0") && ($days == "0"))
        {
            $output->writeln("L'options repeat n'est pas compatible avec une intervalle de temps nulle.");
            return 1;
        }
        
        // Intervale
        $interval = new \DateInterval(sprintf("P%sM%sD", $months, $days));
        
        // Envoi des mails
        $this->sendMailToUsersExpired($output, $interval, $repeat);
        
        return 0;
    }
    
    /**
     * Get member expired
     */
    private function sendMailToUsersExpired(OutputInterface $output, \DateInterval $interval, $repeat = false) 
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
                // loop for repeat
                do {
                    // define date start and end
                    $datelimit->add($interval);
                    // compare date 
                    if ($datelimit->format('Y-m-d') == $datenow->format('Y-m-d'))
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
