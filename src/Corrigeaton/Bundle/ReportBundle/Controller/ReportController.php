<?php

namespace Corrigeaton\Bundle\ReportBundle\Controller;

use Corrigeaton\Bundle\ReportBundle\Event\ReportEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Corrigeaton\Bundle\ReportBundle\Entity\Report;
use Corrigeaton\Bundle\ReportBundle\Form\ReportType;

/**
 * Report controller.
 *
 * @Route("/report")
 */
class ReportController extends Controller
{

    /**
     * Lists all Report entities.
     *
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('CorrigeatonReportBundle:Report')->getTenLastUnfinished();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Lists all Report entities.
     *
     * @Route("/", name="report")
     * @Method("GET")
     * @Template()
     */
    public function indexAllAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('CorrigeatonReportBundle:Report')->findAll();
        $classified = array();
        $unClassified = array();
        foreach($entities as $entity){
            if($entity->getIsFinished())
            {
                $classified[] = $entity;
            }
            else
            {
                $unClassified[] =$entity;
            }
        }


        return array(
            'classified' => $classified,
            'unClassified' => $unClassified,

        );
    }
    /**
     * Creates a new Report entity.
     *
     * @Route("/", name="report_create")
     * @Method("POST")
     * @Template("CorrigeatonReportBundle:Report:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Report();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('report_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Report entity.
     *
     * @param Report $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Report $entity)
    {
        $form = $this->createForm(new ReportType(), $entity, array(
            'action' => $this->generateUrl('report_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Report entity.
     *
     * @Route("/new", name="report_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Report();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Report entity.
     *
     * @Route("/{id}", name="report_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CorrigeatonReportBundle:Report')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Report entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Report entity.
     *
     * @Route("/{id}/edit", name="report_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CorrigeatonReportBundle:Report')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Report entity.');
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
    * Creates a form to edit a Report entity.
    *
    * @param Report $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Report $entity)
    {
        $form = $this->createForm(new ReportType(), $entity, array(
            'action' => $this->generateUrl('report_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Report entity.
     *
     * @Route("/{id}", name="report_update")
     * @Method("PUT")
     * @Template("CorrigeatonReportBundle:Report:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CorrigeatonReportBundle:Report')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Report entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('report_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Report entity.
     *
     * @Route("/{id}", name="report_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('CorrigeatonReportBundle:Report')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Report entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('report'));
    }

    /**
     * Creates a form to delete a Report entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('report_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
    /**
     * Classe un rapport
     *
     * @Route("/{id}/toggle/classified", name="report_toggle_classified")
     * @Method("GET")
     */
    public function unregisterAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $report = $em->getRepository("CorrigeatonReportBundle:Report")->find($id);
        if(!$report)
        {
            throw $this->createNotFoundException("Unable to find report entity");
        }

        if($report->getIsFinished())
        {
            $report->setIsFinished(false);
            $what = "déclassé";
        } else {
            $report->setIsFinished(true);
            $what = "classé";
        }

        $em->flush();

        $this->get('session')->getFlashBag()->add(
            'success',
            'Le report '.$report->getLog().' a été '.$what.'.'
        );

        return $this->redirect($this->generateUrl('report'));
    }
}
