<?php

namespace Corrigeaton\Bundle\MailerBundle\EventListener;


use Corrigeaton\Bundle\MailerBundle\Event\ReminderEvent;
use Corrigeaton\Bundle\ScheduleBundle\Entity\Test;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Bundle\TwigBundle\Debug\TimedTwigEngine;
use Symfony\Component\Templating\Helper\CoreAssetsHelper;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;

class ReminderEventListener {

    /**
     * @var Logger
     */
    private $log;

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var EngineInterface
     */
    private $template;

    /**
     * @var CoreAssetsHelper
     */
    private $assetsHelper;

    /**
     * @var String
     */
    private $emailSend;

    function __construct(\Swift_Mailer $mailer, EngineInterface $template, CoreAssetsHelper $assetsHelper, Logger $log, $emailSend)
    {
        $this->mailer = $mailer;
        $this->template = $template;
        $this->assetsHelper = $assetsHelper;
        $this->emailSend = $emailSend;
        $this->log = $log;
    }

    public function onReminderEvent(ReminderEvent $event){
        $test = $event->getTest();

        $mail = $this->generateMail($test);

        if($this->mailer->send($mail) == 1)
        {
            $this->log->addInfo("Mail send : ".$test);
            //$test->addNumReminder();
        } else {
            $this->log->addError("Error in send mail : ".$test);
        }


    }

    /**
     * @param $test
     * @return \Swift_Mime_MimePart
     * @throws \TijsVerkoyen\CssToInlineStyles\Exception
     */
    private function generateMail(Test $test)
    {
        $html = $this->template->render("CorrigeatonMailerBundle:Mail:mail-" . $test->doISend() . ".html.twig",array("test" => $test));

        $css = file_get_contents($this->assetsHelper->getUrl('bundles/corrigeatonmailer/css/main.css'));

        $inline = new CssToInlineStyles($html, $css);

        $mail = \Swift_Message::newInstance()
            ->setSubject("Corrigeathon - " . $test->getName())
            ->setFrom($this->emailSend)
            ->setTo($test->getTeacher()->getEmail())
            ->setBody($inline->convert(), 'text/html');
        return $mail;
    }


} 