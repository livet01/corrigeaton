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
    /**
     * @var EntityManager
     */
    private $em;

    public function onReportEvent(ReportEvent $reportEvent)
    {
        $report = new Report();
        $classReportEvent = $reportEvent->getClass();
        $reflectedClass = new \ReflectionClass($classReportEvent);
        $report->setLog($reportEvent->getLog());
        $reflectedProperties = $reflectedClass->getProperties();
        $data = array();
        foreach ($reflectedProperties as $reflectedProperty)
        {
            $data[$reflectedProperty->getName()] = $reflectedProperty->getValue();
        }
        $report->setData($data);
        $report->setType($classReportEvent->get_class());
        $this->em->persist($report);
        $this->em->flush();

    }


} 