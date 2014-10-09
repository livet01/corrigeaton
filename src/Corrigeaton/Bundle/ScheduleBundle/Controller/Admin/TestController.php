<?php

namespace Corrigeaton\Bundle\ScheduleBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Corrigeaton\Bundle\ScheduleBundle\Entity\Test;
use Corrigeaton\Bundle\ScheduleBundle\Form\TestType;

/**
 * Test controller.
 *
 * @Route("/test")
 */
class TestController extends Controller
{

    /**
     * Lists all Test entities.
     *
     * @Route("/", name="test")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('CorrigeatonScheduleBundle:Test')->findAll();

        $notCorrected = array();
        $corrected = array();


        foreach($entities as $entity){
            if($entity->isCorrected()){
                $corrected[] = $entity;
            }
            else{
                $notCorrected[] = $entity;
            }
        }

        return array(
            'corrected' => $corrected,
            'notCorrected' => $notCorrected,
        );
    }

    /**
     * Désinscrit un enseignant
     *
     * @Route("/{id}/corrected", name="test_corrected")
     * @Method("GET")
     */
    public function unregisterAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository("CorrigeatonScheduleBundle:Test")->find($id);
        if(!$entity)
        {
            throw $this->createNotFoundException("Unable to find test entity");
        }

        if($entity->isCorrected())
        {
            $entity->setDateCorrected(null);
            $what = "non corrigé";
        } else {
            $entity->setDateCorrected(new \DateTime());
            $what = "corrigé";
        }

        $em->flush();

        $this->get('session')->getFlashBag()->add(
            'success',
            'L\'examen '.$entity->getName().' a été '.$what.'.'
        );

        return $this->redirect($this->generateUrl('test'));
    }

}
