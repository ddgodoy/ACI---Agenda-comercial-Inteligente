<?php

namespace ACI\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ACI\BackendBundle\Form\CompanyType;

/**
 * Company controller.
 *
 * @Route("/admin/company")
 */
class CompanyController extends Controller {

    /**
     * Lists all Company entities.
     *
     * @Route("/", name="admin_company")
     * @Template()
     */
    public function indexAction() {
        return $this->render('BackendBundle:Company:index.html.twig');
    }

    /**
     * Lists all Company entities.
     *
     * @Route("/companylist", name="admin_company_list")
     * @Method("GET")
     * @Template()
     */
    public function companylistAction(Request $request) {
        $aColumns = array('id', 'name', 'cik', 'ticker', 'irs_number', 'sic', 'exchange');
        $iDisplayLength = $request->get("iDisplayLength");
        $iDisplayStart = $request->get("iDisplayStart");

        $order = $aColumns[$request->get("iSortCol_0")];
        $dir = $request->get("sSortDir_0");
        $sSearch = $request->get("sSearch");
        $entities = $this->getDoctrine()->getRepository('BackendBundle:Company')->datatable($iDisplayStart, $iDisplayLength, $order, $dir, $sSearch);
        $total = count($this->getDoctrine()->getRepository('BackendBundle:Company')->findAll());

        $output = array(
            "sEcho" => intval($request->get('sEcho')),
            "iTotalRecords" => count($entities),
            "iTotalDisplayRecords" => $total,
            "aaData" => array()
        );


        foreach ($entities as $entity) {
            $row = array();
            $row [] = $entity->getId();
            $row [] = $entity->getName();
            $row [] = $entity->getCik();
            $row [] = $entity->getTicker();
            $row [] = $entity->getIrsNumber();
            $row [] = $entity->getSic();
            $row [] = $entity->getExchange();
            $output['aaData'] [] = $row;
        }


        echo json_encode($output);
        die;
    }

    /**
     * Crea una nueva company
     *
     * @Route("/create", name="admin_company_create")
     * @Method("post")
     */
    public function createAction(Request $request) {
        $entity = new \CoolwayFestivales\BackendBundle\Entity\Company();
        $form = $this->createForm(new CompanyType(), $entity);
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
     * Displays a form to create a new Company entity.
     *
     * @Route("/new", name="admin_company_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new \CoolwayFestivales\BackendBundle\Entity\Company();
        $form = $this->createForm(new \CoolwayFestivales\BackendBundle\Form\CompanyType(), $entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a company entity.
     *
     * @Route("/show", name="admin_company_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction() {
        $id = $this->getRequest()->get("id");
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Company')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Company entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing company entity.
     *
     * @Route("/edit", name="admin_company_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction() {
        $em = $this->getDoctrine()->getManager();
        $id = $this->getRequest()->get("id");

        $entity = $em->getRepository('BackendBundle:Company')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find company entity.');
        }

        $editForm = $this->createForm(new CompanyType(), $entity);
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
     * @Route("/{id}", name="admin_company_update")
     * @Method("PUT")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BackendBundle:Company')->find($id);
        $result = array();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Company entity.');
        }
        $editForm = $this->createForm(new CompanyType(), $entity);
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
     * Deletes a Company entity.
     *
     * @Route("/{id}", name="admin_company_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('BackendBundle:Company')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Company entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_company'));
    }

    /**
     * Creates a form to delete a Company entity by id.
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
     * Elimina a petición company entities.
     * dado un array de ids
     * @Route("/bachdelete", name="admin_company_batchdelete")
     * @Template()
     */
    public function batchdeleteAction() {
        $peticion = $this->getRequest();
        $ids = $peticion->get("ids", 0, true);
        $ids = explode(",", $ids);

        $em = $this->getDoctrine()->getManager();

        $repo_company = $this->getDoctrine()->getRepository('BackendBundle:Company');

        foreach ($ids as $id) {
            $entity = $repo_company->find($id);
            try {
                $em->remove($entity);
            } catch (\Exception $e) {
                $response = array("success" => false, "message" => "no se puede eliminar este companyo");
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
