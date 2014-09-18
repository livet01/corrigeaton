<?php
/**
 * Created by PhpStorm.
 * User: hugo
 * Date: 18/09/14
 * Time: 14:30
 */

namespace Corrigeaton\Bundle\ReportBundle\Listener;


use Corrigeaton\Bundle\ReportBundle\Entity\Report;
use Corrigeaton\Bundle\ReportBundle\Event\ReportEvent;

class ReportListener {
    public function onReportEvent(ReportEvent $reportEvent)
    {
        //Créer un report
        $report = new Report();
        $classReportEvent = $reportEvent->getClass();
        $reflectedClass = new \ReflectionClass($classReportEvent);
        $report->setLog($reportEvent->getLog());
        $report->setData($reflectedClass->getProperties());
        $report->setType($classReportEvent);
    }

} 