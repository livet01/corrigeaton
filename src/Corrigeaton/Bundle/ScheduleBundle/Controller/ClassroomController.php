<?php

namespace Corrigeaton\Bundle\ScheduleBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Corrigeaton\Bundle\ScheduleBundle\Entity\Classroom;
use Corrigeaton\Bundle\ScheduleBundle\Form\ClassroomType;

/**
 * Classroom controller.
 *
 * @Route("/classroom")
 */
class ClassroomController extends Controller
{

    /**
     * Lists all Classroom entities.
     *
     * @Route("/", name="classroom")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('CorrigeatonScheduleBundle:Classroom')->findAll();

        $deleteForms = array();
        foreach($entities as $entity){
            $deleteForms[] = $this->createDeleteForm($entity->getId())->createView();
        }

        $entity = new Classroom();
        $form   = $this->createCreateForm($entity);

        return array(
            'entities' => $entities,
            'delete_forms' => $deleteForms,
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }
    /**
     * Creates a new Classroom entity.
     *
     * @Route("/", name="classroom_create")
     * @Method("POST")
     * @Template("CorrigeatonScheduleBundle:Classroom:index.html.twig")
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('CorrigeatonScheduleBundle:Classroom')->findAll();

        $deleteForms = array();
        foreach($entities as $entity){
            $deleteForms[] = $this->createDeleteForm($entity->getId())->createView();
        }

        $entity = new Classroom();

        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $entity->setName($this->get("corrigeaton_schedule.ade_service")->findClassroomName($entity->getId()));
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',
                'La classe '.$entity->getName().' a été ajouté.'
            );

            return $this->redirect($this->generateUrl('classroom'));
        }

        return array(
            'entities' => $entities,
            'delete_forms' => $deleteForms,
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Classroom entity.
     *
     * @param Classroom $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Classroom $entity)
    {
        $form = $this->createForm(new ClassroomType(), $entity, array(
            'action' => $this->generateUrl('classroom_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to edit an existing Classroom entity.
     *
     * @Route("/{id}/edit", name="classroom_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CorrigeatonScheduleBundle:Classroom')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Classroom entity.');
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
    * Creates a form to edit a Classroom entity.
    *
    * @param Classroom $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Classroom $entity)
    {
        $form = $this->createForm(new ClassroomType(), $entity, array(
            'action' => $this->generateUrl('classroom_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Modifier'));

        return $form;
    }
    /**
     * Edits an existing Classroom entity.
     *
     * @Route("/{id}", name="classroom_update")
     * @Method("PUT")
     * @Template("CorrigeatonScheduleBundle:Classroom:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CorrigeatonScheduleBundle:Classroom')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Classroom entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $entity->setName($this->get("corrigeaton_schedule.ade_service")->findClassroomName($entity->getId()));
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'success',
                'La classe '.$entity->getName().' a été modifié.'
            );
            return $this->redirect($this->generateUrl('classroom'));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Classroom entity.
     *
     * @Route("/{id}", name="classroom_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('CorrigeatonScheduleBundle:Classroom')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Classroom entity.');
            }

            $em->remove($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'success',
                'La classe '.$entity->getName().' a été supprimé.'
            );
        }

        return $this->redirect($this->generateUrl('classroom'));
    }

    /**
     * Creates a form to delete a Classroom entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('classroom_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
