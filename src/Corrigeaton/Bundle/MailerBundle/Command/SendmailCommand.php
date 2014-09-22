<?php

namespace Corrigeaton\Bundle\MailerBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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
        $mailer = $this->getContainer()->get('mailer');
        $templating = $this->getContainer()->get('templating');

        $exams = $em->getRepository("CorrigeatonScheduleBundle:Test")->findByCorrected(false);

        $output->writeln("<info>Traitement de ".count($exams)." examens</info>");
        $count = 0;
        foreach($exams as $exam)
        {
            $teacher = $exam->getTeacher();
            if(!$teacher->isUnregistered())
            {
                $output->write("<info>Envoie du mail N°".$exam->getNumReminder()." à ".$teacher." pour ".$exam->getName()." ... </info>");

                $mail = \Swift_Message::newInstance()
                        ->setSubject("Corrigeaton - ".$exam->getName())
                        ->setFrom($this->getContainer()->getParameter("corrigeaton_mailer.email_send"))
                        ->setTo($teacher->getEmail())
                        ->setBody(
                                $templating->render("CorrigeatonMailerBundle:Mail:mail-".$exam->getNumReminder().".html.twig")
                            ,'text/html');

                if($mailer->send($mail) == 1)
                {
                    $output->writeln("<comment>OK</comment>");
                    $exam->setNumReminder($exam->getNumReminder()+1);
                } else {
                    $output->writeln("<error>ERROR</error>");
                }

                $count++;
            }
            else {
                $output->writeln("<comment>".$teacher." is unregistered</comment>");
            }
        }
        
        $output->writeln("<info>".$count." mails envoyés</info>");
        $em->flush();
    }
} 