<?php

namespace Corrigeaton\Bundle\ScheduleBundle\Controller;

use Corrigeaton\Bundle\ScheduleBundle\Entity\Classroom;
use Corrigeaton\Bundle\ScheduleBundle\Entity\Test;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Corrigeaton\Bundle\ScheduleBundle\Entity\Teacher;
use Corrigeaton\Bundle\ScheduleBundle\Form\TeacherType;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;

use Symfony\Component\HttpFoundation\Response;

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
     * @Route("/unregister/{id}/{token}/all", name="teacher_unregister")
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

            return array("teacher" => $teacher);
        }
        else
        {
            throw $this->createNotFoundException("Unable to find teacher entity");
        }
    }
    /**
     * Désinscrit un enseignant
     *
     * @Route("/unregister/{id}/{token}/test/{testid}", name="teacher_unregister_test")
     * @Method("GET")
     * @Template()
     */
    public function unregisteredTestAction($id, $token,$testid)
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
                $test = $em->getRepository("CorrigeatonScheduleBundle:Test")->find($testid);
                if(!$test)
                {
                    throw $this->createNotFoundException("Unable to find test entity");
                }
                $test->setNumReminder(-1);
                $em->flush();
            }

            return array("teacher" => $teacher,"test" => $test);
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

    /**
     * @Route("/mails", name="mails")
     * @Template()
     */
    public function emailsAction()
    {
        return array(
        );
    }

    /**
     * @Route("/mails/{id}", name="mail_show")
     */
    public function mailAction($id)
    {
        if(!in_array($id,array("1","2","3","4","5","6","7","corrected"))){
            throw $this->createAccessDeniedException("Not found");
        }
        $t = new Test();
        $t->setId(-1);
        $t->setDate(new \DateTime());
        $t->setName("Exam de test");
        $t->setNumReminder($id);

        $teacher = new Teacher();
        $teacher->setId(-1);
        $teacher->setName("Prénom");
        $teacher->setSurname("Nom");
        $t->setTeacher($teacher);

        $c = new Classroom();
        $c->setName("Class A");
        $t->addClassroom($c);
        $c = new Classroom();
        $c->setName("Class B");
        $t->addClassroom($c);

        $html = $this->renderView("CorrigeatonMailerBundle:Mail:mail-" . $id . ".html.twig",array("test"=>$t));
        $css = file_get_contents($this->get("templating.helper.assets")->getUrl('bundles/corrigeatonmailer/css/main.css'));

        $inline = new CssToInlineStyles($html, $css);
        return new Response($inline->convert());
    }

    /**
     * @Route("/corrected/{id}/{token}", name="corrected")
     * @Template()
     */
    public function correctedAction($id,$token)
    {
        $em = $this->getDoctrine()->getManager();

        $test = $em->getRepository("CorrigeatonScheduleBundle:Test")->find($id);
        if(!$test)
        {
            throw $this->createNotFoundException("Unable to find test entity");
        }
        if($test->getFinishToken() != $token){
            throw $this->createNotFoundException("Unable to find test entity");
        }

        $test->setDateCorrected(new \DateTime());
        $em->flush();

        return array("test" => $test
        );
    }

}
