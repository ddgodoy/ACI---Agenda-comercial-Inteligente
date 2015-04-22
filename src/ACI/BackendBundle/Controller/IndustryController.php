<?php

namespace ACI\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use ACI\BackendBundle\Form\IndustryType;

/**
 * Industry controller.
 *
 * @Route("/admin/industry")
 */
class IndustryController extends Controller {

    /**
     * Lists all Industry entities.
     *
     * @Route("/", name="admin_industry")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();
        $entities = $this->getDoctrine()->getRepository('BackendBundle:Industry')->findAll();
        return $this->render('BackendBundle:Industry:index.html.twig', array("entities" => $entities));
    }

    /**
     * Lists all Industry entities.
     *
     * @Route("/list", name="admin_industry_list")
     * @Template()
     */
    public function listAction() {
        $this->_datatable();
        return $this->render('BackendBundle:Industry:list.html.twig');
    }

    /**
     * set datatable configs
     * @return \CoolwayFestivales\DatatableBundle\Util\Datatable
     */
    private function _datatable() {
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
        $qb->from("BackendBundle:Industry", "entity")
                ->orderBy("entity.id", "desc");
        $datatable = $this->get('datatable')
                ->setFields(
                        array(
                            'Nombre' => 'entity.name',
                            "_identifier_" => 'entity.id')
                )
                ->setHasAction(false)
//                ->setAcl(array("OWNER")) //OWNER,OPERATOR,VIEW
                ->setSearch(TRUE);

        $datatable->getQueryBuilder()->setDoctrineQueryBuilder($qb);
        return $datatable;
    }

    /**
     * @Route("/admin_industry_grid", name="admin_industry_grid")
     * @Template()
     */
    public function gridAction() {
        return $this->_datatable()->execute();
    }

    /**
     * @Route("/datatable", name="datatable_industry")
     * @Template()
     */
    public function datatableAction() {
        $this->_datatable();
        return $this->render('BackendBundle:Industry:index.html.twig');
    }

    /**
     * Crea una nueva industry
     *
     * @Route("/create", name="admin_industry_create")
     * @Method("post")
     */
    public function createAction(Request $request) {
        $entity = new \CoolwayFestivales\BackendBundle\Entity\Industry();
        $form = $this->createForm(new IndustryType(), $entity);
        $form->bind($request);
        $result = array();


        $em = $this->getDoctrine()->getManager();
        try {
            $em->persist($entity);
            $em->flush();

            /*
              //Integración con las ACLs
              $user = $this->get('security.context')->getToken()->getUser();
              $provider = $this->get('Apptibase.acl_manager');
              $provider->addPermission($entity, $user, MaskBuilder::MASK_OWNER, "object");
              //-----------------------------
             */

            $result['success'] = true;
            $result['mensaje'] = 'Adicionado correctamente';
        } catch (\Exception $exc) {
            $result['success'] = false;
            $result['errores'] = array('causa' => 'e_interno', 'mensaje' => $exc->getMessage());
        }

        echo json_encode($result);
        die;
    }

    /**
     * Displays a form to create a new Industry entity.
     *
     * @Route("/new", name="admin_industry_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new \CoolwayFestivales\BackendBundle\Entity\Industry();
        $form = $this->createForm(new \CoolwayFestivales\BackendBundle\Form\IndustryType(), $entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a industry entity.
     *
     * @Route("/show", name="admin_industry_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction() {
        $id = $this->getRequest()->get("id");
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Industry')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Industry entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing industry entity.
     *
     * @Route("/edit", name="admin_industry_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction() {
        $em = $this->getDoctrine()->getManager();
        $id = $this->getRequest()->get("id");

        $entity = $em->getRepository('BackendBundle:Industry')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find industry entity.');
        }

        $editForm = $this->createForm(new IndustryType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing User entity.
     *
     * @Route("/{id}", name="admin_industry_update")
     * @Method("PUT")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Industry')->find($id);
        $result = array();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Industry entity.');
        }
        $editForm = $this->createForm(new IndustryType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            try {
                $em->persist($entity);
                $em->flush();
                $result['success'] = true;
                $result['message'] = 'Transacci&oacute;n realizada exitosamente.';
            } catch (\Exception $exc) {
                $result['success'] = false;
                $result['errores'] = array('causa' => 'e_interno', 'mensaje' => $exc->getMessage());
            }
        } else {

            $result['success'] = false;
        }
        echo json_encode($result);
        die;
    }

    /**
     * Deletes a Industry entity.
     *
     * @Route("/{id}", name="admin_industry_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('BackendBundle:Industry')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Industry entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_industry'));
    }

    /**
     * Creates a form to delete a Industry entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder(array('id' => $id))
                        ->add('id', 'hidden')
                        ->getForm()
        ;
    }

    /**
     * Elimina a petición industry entities.
     * dado un array de ids
     * @Route("/bachdelete", name="admin_industry_batchdelete")
     * @Template()
     */
    public function batchdeleteAction() {
        $peticion = $this->getRequest();
        $ids = $peticion->get("ids", 0, true);
        $ids = explode(",", $ids);

        $em = $this->getDoctrine()->getManager();

        $repo_industry = $this->getDoctrine()->getRepository('BackendBundle:Industry');

        foreach ($ids as $id) {
            $entity = $repo_industry->find($id);
            try {
                $em->remove($entity);
            } catch (\Exception $e) {
                $response = array("success" => false, "message" => "no se puede eliminar este industryo");
                $result = json_encode($response);
                return new \Symfony\Component\HttpFoundation\Response($result);
            }
        }

        try {
            $em->flush();
            $response = array("success" => true, "message" => "Transacci&oacute;n realizada satisfactoriamente.");
        } catch (\Exception $e) {
            $response = array("success" => false, "message" => "No puede completar esta petición Error code: " . $e->getCode() . " Detalles:" . $e->getMessage());
        }

        $result = json_encode($response);
        return new \Symfony\Component\HttpFoundation\Response($result);
    }

    /*
     * ==================================== Funciones específicas ==================
     */



    /*
     * =============================================================================
     */
}
