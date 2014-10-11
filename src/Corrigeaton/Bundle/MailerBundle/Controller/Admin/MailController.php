<?php

namespace Corrigeaton\Bundle\MailerBundle\Controller\Admin;

use Corrigeaton\Bundle\ScheduleBundle\Entity\Classroom;
use Corrigeaton\Bundle\ScheduleBundle\Entity\Teacher;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Corrigeaton\Bundle\ScheduleBundle\Entity\Test;
use Symfony\Component\HttpFoundation\Response;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;

class MailController extends Controller
{
    /**
     * @Route("/mail")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }

    /**
     * @Route("/mail/{id}")
     */
    public function mailAction($id)
    {
        $t = new Test();
        $t->setId(-1);
        $t->setDate(new \DateTime());
        $t->setName("Exam de test");
        $t->setNumReminder($id);

        $teacher = new Teacher();
        $teacher->setId(-1);
        $teacher->setName("Nom");
        $teacher->setSurname("Prenom");
        $t->setTeacher($teacher);

        $c = new Classroom();
        $c->setName("IR_I_A");
        $t->addClassroom($c);
        $c = new Classroom();
        $c->setName("IR_I_B");
        $t->addClassroom($c);

        $html = $this->renderView("CorrigeatonMailerBundle:Mail:mail-" . $id . ".html.twig",array("test"=>$t));
        $css = file_get_contents($this->get("templating.helper.assets")->getUrl('bundles/corrigeatonmailer/css/main.css'));

        $inline = new CssToInlineStyles($html, $css);
        return new Response($inline->convert());
    }
}
