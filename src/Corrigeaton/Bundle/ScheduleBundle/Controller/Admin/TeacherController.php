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
 * Teacher controller.
 *
 * @Route("/admin/teacher")
 */
class TeacherController extends Controller
{

    /**
     * Lists all Teacher entities.
     *
     * @Route("/", name="teacher")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('CorrigeatonScheduleBundle:Teacher')->findAll();

        $unregistered = array();
        $registered = array();
        foreach($entities as $entity){
            if($entity->getIsUnregistered()){
                $unregistered[] = $entity;
            }
            else {
                $registered[] = array('entity'=>$entity,
                    'count' => array(
                        true => $em->getRepository('CorrigeatonScheduleBundle:Test')->countTest(true,$entity),
                        false => $em->getRepository('CorrigeatonScheduleBundle:Test')->countTest(false,$entity),
                    ));

            }
        }

        return array(
            'registered' => $registered,
            'unregistered' => $unregistered,
        );
    }

    /**
     * Finds and displays a Teacher entity.
     *
     * @Route("/{id}", name="teacher_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $teacher = $em->getRepository('CorrigeatonScheduleBundle:Teacher')->find($id);

        if (!$teacher) {
            throw $this->createNotFoundException('Unable to find Teacher entity.');
        }

        $exams = $em->getRepository('CorrigeatonScheduleBundle:Test')->findByTeacher($teacher);

        return array(
            'teacher'      => $teacher,
            'exams'        => $exams
        );
    }

    /**
     * Displays a form to edit an existing Teacher entity.
     *
     * @Route("/{id}/edit", name="teacher_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CorrigeatonScheduleBundle:Teacher')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Teacher entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Désinscrit un enseignant
     * 
     * @Route("/{id}/toggle/register", name="teacher_toggle_register")
     * @Method("GET")
     */
    public function unregisterAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $teacher = $em->getRepository("CorrigeatonScheduleBundle:Teacher")->find($id);
        if(!$teacher)
        {
            throw $this->createNotFoundException("Unable to find teacher entity");
        }

        if($teacher->getIsUnregistered())
        {
            $teacher->setIsUnregistered(false);
            $what = "abonné";
        } else {
            $teacher->setIsUnregistered(true);
            $what = "désabonné";
        }

        $em->flush();

        $this->get('session')->getFlashBag()->add(
                'success',
                'La professeur '.$teacher->getName().' '.$teacher->getSurname().' a été '.$what.'.'
            );

        return $this->redirect($this->generateUrl('teacher'));
    }

    /**
    * Creates a form to edit a Teacher entity.
    *
    * @param Teacher $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Teacher $entity)
    {
        $form = $this->createForm(new TeacherType(), $entity, array(
            'action' => $this->generateUrl('teacher_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Teacher entity.
     *
     * @Route("/{id}", name="teacher_update")
     * @Method("PUT")
     * @Template("CorrigeatonScheduleBundle:Admin/Teacher:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CorrigeatonScheduleBundle:Teacher')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Teacher entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'success',
                $entity.' a été modifié.'
            );
            return $this->redirect($this->generateUrl('teacher_show', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Teacher entity.
     *
     * @Route("/{id}", name="teacher_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('CorrigeatonScheduleBundle:Teacher')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Teacher entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('teacher'));
    }

    /**
     * Creates a form to delete a Teacher entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('teacher_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
