<?php
/**
 * Created by PhpStorm.
 * User: hugo
 * Date: 18/09/14
 * Time: 14:30
 */

namespace Corrigeaton\Bundle\ReportBundle\EventListener;


use Corrigeaton\Bundle\ReportBundle\Entity\Report;
use Corrigeaton\Bundle\ReportBundle\Event\ReportEvent;
use Doctrine\ORM\EntityManager;

class ReportListener {
    /**
     * @var EntityManager
     */
    private $em;

    function __construct($em)
    {
        $this->em = $em;
    }

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
            $reflectedProperty->setAccessible(true);
            $data[$reflectedProperty->getName()] = $reflectedProperty->getValue($classReportEvent);
        }
        $report->setData($data);
        $report->setType(get_class($classReportEvent));
        $this->em->persist($report);
        $this->em->flush();

    }


} 