<?php

namespace Corrigeaton\Bundle\MailerBundle\EventListener;


use Corrigeaton\Bundle\ScheduleBundle\Event\ConfigureMenuEvent;

class MenuConfigureListener {

    public function onConfigureMenu(ConfigureMenuEvent $event){
        $event->getMenu()->addChild('Emails', array(
            'route' => 'corrigeaton_mailer_admin_mail_index'
        ))->setExtra('logo','envelope');
    }
} 