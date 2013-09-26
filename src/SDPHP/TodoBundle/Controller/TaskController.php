<?php

namespace SDPHP\TodoBundle\Controller;

use SDPHP\TodoBundle\Entity\Task;
use SDPHP\TodoBundle\Form\TaskType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Task controller.
 *
 * @Route("/")
 */
class TaskController extends Controller
{

	/**
	 * Lists all Task entities.
	 *
	 * @Route("/", name="")
	 * @Method("GET")
	 * @Template()
	 */
	public function indexAction()
	{

		$em = $this->getDoctrine()->getManager();

		$entities = $em->getRepository( 'SDPHPTodoBundle:Task' )->findAll();

		return array(
			'entities' => $entities,
		);
	}

	/**
	 * Creates a new Task entity.
	 *
	 * @Route("/", name="_create")
	 * @Method("POST")
	 * @Template("SDPHPTodoBundle:Task:new.html.twig")
	 */
	public function createAction( Request $request )
	{

		$entity = new Task();
		$form   = $this->createCreateForm( $entity );
		$form->handleRequest( $request );

		if( $form->isValid() ) {
			$em = $this->getDoctrine()->getManager();
			$em->persist( $entity );
			$em->flush();

			return $this->redirect( $this->generateUrl( '' ) );
		}

		return array(
			'entity' => $entity,
			'form'   => $form->createView(),
		);
	}

	/**
	 * Creates a form to create a Task entity.
	 *
	 * @param Task $entity The entity
	 *
	 * @return \Symfony\Component\Form\Form The form
	 */
	private function createCreateForm( Task $entity )
	{

		$form = $this->createForm(
			new TaskType(),
			$entity,
			array(
				'action' => $this->generateUrl( '_create' ),
				'method' => 'POST',
			)
		);

		$form->add( 'submit', 'submit', array( 'label' => 'Create' ) );

		return $form;
	}

	/**
	 * Toggles the status of a Task Entity
	 *
	 * @Route("/toggle/{entity}", name="_toggle")
	 * @Method("POST")
	 */
	public function toggleStatusAction( Task $entity )
	{
		$em = $this->getDoctrine()->getManager();
		$entity->setStatus( !$entity->getStatus() );
		$em->persist( $entity );

		try {
			$em->flush();
			return new JsonResponse( null, 200 );
		} catch( \Exception $e ) {
			return new JsonResponse( null, 500 );
		}
	}

	/**
	 * Displays a form to create a new Task entity.
	 *
	 * @Route("/new", name="_new")
	 * @Method("GET")
	 * @Template()
	 */
	public function newAction()
	{

		$entity = new Task();
		$form   = $this->createCreateForm( $entity );

		return array(
			'entity' => $entity,
			'form'   => $form->createView(),
		);
	}

	/**
	 * Finds and displays a Task entity.
	 *
	 * @Route("/{id}", name="_show")
	 * @Method("GET")
	 * @Template()
	 */
	public function showAction( $id )
	{

		$em = $this->getDoctrine()->getManager();

		$entity = $em->getRepository( 'SDPHPTodoBundle:Task' )->find( $id );

		if( !$entity ) {
			throw $this->createNotFoundException( 'Unable to find Task entity.' );
		}

		$deleteForm = $this->createDeleteForm( $id );

		return array(
			'entity'      => $entity,
			'delete_form' => $deleteForm->createView(),
		);
	}

	/**
	 * Creates a form to delete a Task entity by id.
	 *
	 * @param mixed $id The entity id
	 *
	 * @return \Symfony\Component\Form\Form The form
	 */
	private function createDeleteForm( $id )
	{

		return $this->createFormBuilder()
			->setAction( $this->generateUrl( '_delete', array( 'id' => $id ) ) )
			->setMethod( 'DELETE' )
			->add( 'submit', 'submit', array( 'label' => 'Delete' ) )
			->getForm();
	}

	/**
	 * Displays a form to edit an existing Task entity.
	 *
	 * @Route("/{id}/edit", name="_edit")
	 * @Method("GET")
	 * @Template()
	 */
	public function editAction( $id )
	{

		$em = $this->getDoctrine()->getManager();

		$entity = $em->getRepository( 'SDPHPTodoBundle:Task' )->find( $id );

		if( !$entity ) {
			throw $this->createNotFoundException( 'Unable to find Task entity.' );
		}

		$editForm   = $this->createEditForm( $entity );
		$deleteForm = $this->createDeleteForm( $id );

		return array(
			'entity'      => $entity,
			'edit_form'   => $editForm->createView(),
			'delete_form' => $deleteForm->createView(),
		);
	}

	/**
	 * Creates a form to edit a Task entity.
	 *
	 * @param Task $entity The entity
	 *
	 * @return \Symfony\Component\Form\Form The form
	 */
	private function createEditForm( Task $entity )
	{

		$form = $this->createForm(
			new TaskType(),
			$entity,
			array(
				'action' => $this->generateUrl( '_update', array( 'id' => $entity->getId() ) ),
				'method' => 'PUT',
			)
		);

		$form->add( 'submit', 'submit', array( 'label' => 'Update' ) );

		return $form;
	}

	/**
	 * Edits an existing Task entity.
	 *
	 * @Route("/{id}", name="_update")
	 * @Method("PUT")
	 * @Template("SDPHPTodoBundle:Task:edit.html.twig")
	 */
	public function updateAction( Request $request, $id )
	{

		$em = $this->getDoctrine()->getManager();

		$entity = $em->getRepository( 'SDPHPTodoBundle:Task' )->find( $id );

		if( !$entity ) {
			throw $this->createNotFoundException( 'Unable to find Task entity.' );
		}

		$deleteForm = $this->createDeleteForm( $id );
		$editForm   = $this->createEditForm( $entity );
		$editForm->handleRequest( $request );

		if( $editForm->isValid() ) {
			$em->flush();

			return $this->redirect( $this->generateUrl( '_edit', array( 'id' => $id ) ) );
		}

		return array(
			'entity'      => $entity,
			'edit_form'   => $editForm->createView(),
			'delete_form' => $deleteForm->createView(),
		);
	}

	/**
	 * Deletes a Task entity.
	 *
	 * @Route("/{id}", name="_delete")
	 * @Method("DELETE")
	 */
	public function deleteAction( Request $request, $id )
	{

		$form = $this->createDeleteForm( $id );
		$form->handleRequest( $request );

		if( $form->isValid() ) {
			$em     = $this->getDoctrine()->getManager();
			$entity = $em->getRepository( 'SDPHPTodoBundle:Task' )->find( $id );

			if( !$entity ) {
				throw $this->createNotFoundException( 'Unable to find Task entity.' );
			}

			$em->remove( $entity );
			$em->flush();
		}

		return $this->redirect( $this->generateUrl( '' ) );
	}
}
