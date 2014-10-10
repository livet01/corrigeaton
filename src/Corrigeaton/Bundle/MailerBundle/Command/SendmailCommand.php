<?php

namespace Corrigeaton\Bundle\MailerBundle\Command;


use Corrigeaton\Bundle\MailerBundle\Event\ReminderEvent;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;

class SendmailCommand extends ContainerAwareCommand {

    protected function configure()
    {
        $this
            ->setName('corrigeaton:mail:send')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get("doctrine.orm.entity_manager");

        $exams = $em->getRepository("CorrigeatonScheduleBundle:Test")->findByCorrected(false);

        $output->writeln("<info>Traitement de ".count($exams)." examens</info>");
        $count = 0;
        foreach($exams as $exam)
        {
            $teacher = $exam->getTeacher();
            if(!$teacher->isUnregistered())
            {
                $numMail = $exam->doISend();
                if($numMail != -1)
                {
                    $output->write("<info>Envoie du mail N°".$numMail." à ".$teacher." pour ".$exam->getName()." ... </info>");

                    $event = new ReminderEvent($exam);
                    $this->getContainer()->get("event_dispatcher")->dispatch("corrigeaton_mailer.event.reminder",$event);

                    $count++;
                }

            }
            else {
                $output->writeln("<comment>".$teacher." is unregistered</comment>");
            }
        }
        
        $output->writeln("<info>".$count." mails envoyés</info>");
        $em->flush();
    }
} 