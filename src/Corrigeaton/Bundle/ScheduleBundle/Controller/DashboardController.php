<?php

namespace Corrigeaton\Bundle\ScheduleBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Corrigeaton\Bundle\ScheduleBundle\Entity\Test;

/**
 * Dashboard controller.
 *
 */
class DashboardController extends Controller {

    /**
     * Lists all Classroom entities.
     *
     * @Route("/", name="dashboard")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        return array(
            'count' => array(
                Test::STATUS_FUTURE => $em->getRepository('CorrigeatonScheduleBundle:Test')->countTestByStatus(Test::STATUS_FUTURE),
                Test::STATUS_NOTCORRECTED => $em->getRepository('CorrigeatonScheduleBundle:Test')->countTestByStatus(Test::STATUS_NOTCORRECTED),
                Test::STATUS_CORRECTED => $em->getRepository('CorrigeatonScheduleBundle:Test')->countTestByStatus(Test::STATUS_CORRECTED)
        ));
    }
} 