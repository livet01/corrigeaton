<?php

namespace Corrigeaton\Bundle\ScheduleBundle\Controller;

use Corrigeaton\Bundle\ScheduleBundle\Entity\Test;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Corrigeaton\Bundle\ScheduleBundle\Entity\Teacher;
use Corrigeaton\Bundle\ScheduleBundle\Form\TeacherType;

/**
 * Public front office controller.
 *
 * @Route("/")
 */
class FrontController extends Controller
{

    /**
     * @Route("/", name="index")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('CorrigeatonScheduleBundle:Classroom')->findAll();
        return array(
            "classes" => $entities
        );
    }

    /**
     * Désinscrit un enseignant
     *
     * @Route("/unregister/{id}/{token}", name="teacher_unregister")
     * @Method("GET")
     * @Template()
     */
    public function unregisteredAction($id, $token)
    {
        $em = $this->getDoctrine()->getManager();

        $teacher = $em->getRepository("CorrigeatonScheduleBundle:Teacher")->find($id);
        if(!$teacher)
        {
            throw $this->createNotFoundException("Unable to find teacher entity");
        }

        if($token == $teacher->getUnregisterToken())
        {
            if($teacher->getIsUnregistered()==false)
            {
                $teacher->setIsUnregistered(true);
                $em->flush();
            }
            $this->get('session')->getFlashBag()->add(
                'email',
                'L\'adresse '.$teacher->getEmail().' a été désabonné. Vous ne receverez plus d\'email de notre part. '
            );
            return array("teacher" => $teacher);
        }
        else
        {
            throw $this->createNotFoundException("Unable to find teacher entity");
        }
    }


    /**
     * @Route("/mentions-legales", name="mentions")
     * @Template()
     */
    public function mentionsAction()
    {
        return array(
        );
    }

}
