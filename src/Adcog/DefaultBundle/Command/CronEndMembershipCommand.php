<?php

namespace Adcog\DefaultBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CronEndMembershipCommand
 *
 * @author "Nicolas Drufin" <nicolas.drufin@ensc.fr>
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
                'debug',
                'd',
                InputOption::VALUE_NONE,
                'Mise en mode debug'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $debug = $input->getOption('debug');
        if (true === $debug) {
            $output->writeln("Demarrage en mode debug");
        }

        // Envoi des mails
        $this->sendMailToUsersExpired($output, $debug);
        return 0;
    }

    /*
     * Print only if debug activate
     *
     */
    private function printDebug($output, $debug, $message) {
        if (true === $debug) {
            $output->writeln($message);
        }
    }
    
    /**
     * Get member expired
     */
    private function sendMailToUsersExpired(OutputInterface $output, $debug)
    {
        // Obtient la connexion
        $em = $this->getContainer()->get('doctrine.orm.default_entity_manager');
        
        //Mailer
        $mailer = $this->getContainer()->get('eb_email.mailer.mailer');      
        
        // get users
        $this->printDebug($output, $debug, "Chargement des utilisateurs ...");
        $users = $em->getRepository('AdcogDefaultBundle:User')->findBy(array());
        $this->printDebug($output, $debug, sprintf("%u utilisateurs trouve",count($users)));

        // get intervals
        $this->printDebug($output, $debug, "Chargement des rappels ...");
        $reminders = $em->getRepository('AdcogDefaultBundle:Reminder')->findBy(array());
        $this->printDebug($output, $debug, sprintf("%u rappels trouve",count($reminders)));
            
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
                $this->printDebug($output, $debug, sprintf("%s expire le %s", $user, $datelimit->format('d-m-Y')));

                // flag mail send (avoid 2 mails for a same user)
                $mail_send = false;

                // for all interval
                foreach($reminders as $reminder) {
                    // get interval
                    $interval = $reminder->getDateInterval();
                    // get cycle
                    $repeat = $reminder->isCycle();

                    // if day of end
                    $datelimitstart = clone $datelimit;
                    // loop for repeat
                    do {
                        // define date start and end
                        $datelimit->add($interval);

                        // compare date
                        if ($datelimit->format('Y-m-d') == $datenow->format('Y-m-d')) {
                            // Affichage (debug)
                            if ($output->isVerbose() || true === $debug) {
                                if ('user_expired' == $mailmodel) {
                                    $output->writeln(sprintf("\t%s %s a expiré depuis le %s", $user->getFirstname(), $user->getLastname(), $datelimitstart->format('d/m/Y')));
                                } else {
                                    $output->writeln(sprintf("\t%s %s n'est plus étudiant depuis le %s", $user->getFirstname(), $user->getLastname(), $datelimitstart->format('d/m/Y')));
                                }
                            }

                            // Envoi du mail
                            $mailer->send($mailmodel, $user, [
                                'user' => $user,
                                'expiration_date' => $datelimitstart
                            ]);
                            $mail_send = true;
                            break;
                        }
                    } while (($datelimit < $datenow) && (true === $repeat));

                    // break if mail send
                    if (true === $mail_send) {
                        break;
                    }
                }
            }
        }
    }
}
