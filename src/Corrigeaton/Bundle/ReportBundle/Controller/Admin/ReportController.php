<?php

namespace Corrigeaton\Bundle\ReportBundle\Controller\Admin;

use Corrigeaton\Bundle\ReportBundle\Event\ReportEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Corrigeaton\Bundle\ReportBundle\Entity\Report;
use Corrigeaton\Bundle\ReportBundle\Form\ReportType;

/**
 * Report controller.
 *
 * @Route("/report")
 */
class ReportController extends Controller
{

    /**
     * Lists all Report entities.
     *
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('CorrigeatonReportBundle:Report')->getTenLastUnfinished();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Lists all Report entities.
     *
     * @Route("/", name="report")
     * @Method("GET")
     * @Template()
     */
    public function indexAllAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('CorrigeatonReportBundle:Report')->findAll();
        $classified = array();
        $unClassified = array();
        foreach($entities as $entity){
            if($entity->getIsFinished())
            {
                $classified[] = $entity;
            }
            else
            {
                $unClassified[] =$entity;
            }
        }


        return array(
            'classified' => $classified,
            'unClassified' => $unClassified,

        );
    }

    /**
     * Finds and displays a Report entity.
     *
     * @Route("/{id}", name="report_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CorrigeatonReportBundle:Report')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Report entity.');
        }


        return array(
            'entity'      => $entity,
        );
    }


    /**
     * Classe un rapport
     *
     * @Route("/{id}/toggle/classified", name="report_toggle_classified")
     * @Method("GET")
     */
    public function unclassifiedAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $report = $em->getRepository("CorrigeatonReportBundle:Report")->find($id);
        if(!$report)
        {
            throw $this->createNotFoundException("Unable to find report entity");
        }

        if($report->getIsFinished())
        {
            $report->setIsFinished(false);
            $what = "déclassé";
        } else {
            $report->setIsFinished(true);
            $what = "classé";
        }

        $em->flush();

        $this->get('session')->getFlashBag()->add(
            'success',
            'Le report '.$report->getLog().' a été '.$what.'.'
        );

        return $this->redirect($this->generateUrl('report'));
    }
}
