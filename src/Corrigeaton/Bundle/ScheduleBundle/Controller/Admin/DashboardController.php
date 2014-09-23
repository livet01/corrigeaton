<?php

namespace Corrigeaton\Bundle\ScheduleBundle\Controller\Admin;

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
                true => $em->getRepository('CorrigeatonScheduleBundle:Test')->countTest(true),
                false => $em->getRepository('CorrigeatonScheduleBundle:Test')->countTest(false),
        ));
    }
} 