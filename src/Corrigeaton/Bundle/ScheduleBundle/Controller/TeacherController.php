<?php

namespace Corrigeaton\Bundle\ScheduleBundle\Controller\Admin;

use Corrigeaton\Bundle\ScheduleBundle\Entity\Test;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Corrigeaton\Bundle\ScheduleBundle\Entity\Teacher;
use Corrigeaton\Bundle\ScheduleBundle\Form\TeacherType;

/**
 * Public Teacher controller.
 *
 * @Route("/")
 */
class TeacherController extends Controller
{
    /**
     * Désinscrit un enseignant
     *
     * @Route("/unregister/{id}/{token}", name="teacher_toggle_register")
     * @Method("GET")
     */
    public function unregisteredAction($id, $token)
    {
        $em = $this->getDoctrine()->getManager();

        $teacher = $em->getRepository("CorrigeatonScheduleBundle:Teacher")->find($id);
        if(!$teacher)
        {
            throw $this->createNotFoundException("Unable to find teacher entity");
        }
        if($token == $teacher->getToken())
        {
            if($teacher->getIsUnregistered()==false)
            {
                $teacher->setIsUnregistered(true);
                $what = "désabonné";
            }
            else {
                $what = "toujours désabonné";
            }

        }
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'success',
            'La professeur '.$teacher->getName().' '.$teacher->getSurname().' est '.$what.'.'
        );

        return array(
            'action'=> $what,
            'teacher' => $teacher,
        );
    }
}
