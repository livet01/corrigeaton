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

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Classroom entity.
     *
     * @Route("/", name="classroom_create")
     * @Method("POST")
     * @Template("CorrigeatonScheduleBundle:Classroom:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Classroom();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('classroom_show', array('id' => $entity->getId())));
        }

        return array(
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
     * Displays a form to create a new Classroom entity.
     *
     * @Route("/new", name="classroom_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Classroom();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Classroom entity.
     *
     * @Route("/{id}", name="classroom_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CorrigeatonScheduleBundle:Classroom')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Classroom entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
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

        $form->add('submit', 'submit', array('label' => 'Update'));

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
            $em->flush();

            return $this->redirect($this->generateUrl('classroom_edit', array('id' => $id)));
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
