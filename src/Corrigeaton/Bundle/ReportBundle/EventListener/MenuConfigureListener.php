<?php

namespace Corrigeaton\Bundle\ReportBundle\EventListener;


use Corrigeaton\Bundle\ScheduleBundle\Event\ConfigureMenuEvent;

class MenuConfigureListener {

    public function onConfigureMenu(ConfigureMenuEvent $event){
        $event->getMenu()->addChild('Rapports', array(
            'route' => 'report'
        ))->setExtra('logo','bolt');
    }
} 