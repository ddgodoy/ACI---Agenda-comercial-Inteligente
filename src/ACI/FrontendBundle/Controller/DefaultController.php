<?php

namespace ACI\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

class DefaultController extends Controller {

    /**
     * Displays a form to create a new Customer entity.
     *
     * @Route("/login", name="login")
     * @Template()
     */
    public function loginAction() {
        $peticion = $this->getRequest();
        $sesion = $peticion->getSession();
        $error = $peticion->attributes->get(
                SecurityContext::AUTHENTICATION_ERROR, $sesion->get(SecurityContext::AUTHENTICATION_ERROR)
        );

        return $this->render('FrontendBundle:Frontend:login.html.twig', array(
                    'last_username' => $sesion->get(SecurityContext::LAST_USERNAME),
                    'error' => $error
        ));
    }

    /**
     * Displays a form to create a new Customer entity.
     *
     * @Route("/register", name="register")
     * @Template()
     */
    public function registerAction() {
        $request = $this->getRequest();
        $plan = $request->get('plan', '25139');
        $entity = new \ACI\SafetyBundle\Entity\User();
        $form = $this->createForm(new \ACI\SafetyBundle\Form\UserType(), $entity);

        return $this->render('FrontendBundle:Backend:register.html.twig', array(
                    'form' => $form->createView(),
                    'error' => false,
                    'plan' => $plan
        ));
    }

    /**
     * @Route("/login_check", name="_security_check")
     */
    public function securityCheckAction() {
        // The security layer will intercept this request
    }

    /**
     * @Route("/logout", name="_logout")
     */
    public function logoutAction() {

    }

    /**
     * @Route("/validate/email", name="validate_email")
     */
    public function validateeAction(\Symfony\Component\HttpFoundation\Request $request) {
        $result = array("valid" => true);

        $params = $request->get('prodi_safetybundle_usertype');
        $email = $params['email'];
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $em = $this->getDoctrine()->getManager();
            $repo_user = $em->getRepository("SafetyBundle:User");
            $user = $repo_user->findOneBy(array("email" => $email));
            if ($user) {
                $result['valid'] = false;
            }
        }

        return new \Symfony\Component\HttpFoundation\Response(json_encode($result));
    }

    /**
     * @Route("/validate/username", name="validate_username")
     */
    public function validateuAction(\Symfony\Component\HttpFoundation\Request $request) {
        $result = array("valid" => true);

        $params = $request->get('prodi_safetybundle_usertype');
        $username = $params['username'];

        $em = $this->getDoctrine()->getManager();
        $repo_user = $em->getRepository("SafetyBundle:User");
        $customer = $repo_user->findOneBy(array("username" => $username));

        if ($customer) {
            $result['valid'] = false;
        }


        return new \Symfony\Component\HttpFoundation\Response(json_encode($result));
    }

    /**
     * Crea una nueva customer
     *
     * @Route("/user_add", name="user_create")
     */
    public function createAction(\Symfony\Component\HttpFoundation\Request $request) {
        $entity = new \ACI\SafetyBundle\Entity\User();

        $plan = $request->get('plan');

        $form = $this->createForm(new \ACI\SafetyBundle\Form\UserType(), $entity);
        $form->bind($request);
        $em = $this->getDoctrine()->getManager();

        try {
            $entity->setEnabled(true);
            $role = $em->getRepository("SafetyBundle:Role")->findOneByName("ROLE_CMS");
            if (!$role) {
                $role = new \ACI\SafetyBundle\Entity\Role();
                $role->setDescription("User CMS");
                $role->setName("ROLE_CMS");
                $em->persist($role);
                $em->flush();
            }
            $entity->addRole($role);
            $em->persist($entity);
            $em->flush();

//            //logear al user
//            try {
//                $token = new \Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken($entity, $entity->getPassword(), 'usuarios', $entity->getRoles());
//                $this->container->get('security.context')->setToken($token);
//
//            } catch (Exception $exc) {
//                echo $exc->getTraceAsString();
//            }

            $user_id = $entity->getId();
            return $this->redirect("https://subs.pinpayments.com/apptibase-test/subscribers/$user_id/subscribe/$plan/apptibase-user-$user_id");
        } catch (\Exception $exc) {
            return $this->render('FrontendBundle:Backend:register.html.twig', array(
                        'error' => $exc->getMessage(),
                        'plan' => ""
            ));
        }
    }

    /**
     * Mi cuenta
     *
     * @Route("/customer_account", name="_customer_account")
     * @Template()
     */
    public function account() {
        $user_id = $this->get("security.context")->getToken()->getUser()->getId();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('SafetyBundle:User')->find($user_id);
        return array();
    }

    /**
     * Displays a form to create a new Customer entity.
     *
     * @Route("/forgotpasswd", name="_user_forgotpasswd")
     * @Template()
     */
    public function forgotpasswdAction() {
        $entity = new \ACI\EcomerceBundle\Entity\Customer();
        $form = $this->createForm(new \ACI\EcomerceBundle\Form\ForgotType(), $entity);

        $theme = $this->get('apptibase.cms')->getTheme();
        return $this->render($theme . "Bundle:templates:forgotpasswd.html.twig", array(
                    'entity' => $entity,
                    'form' => $form->createView(),
                    'theme' => $theme,
                    'error' => ""
        ));
    }

    // codificar el passwd
    private function setSecurePassword(&$entity) {
        $confg = Yaml::parse(__DIR__ . '/../../../../app/config/security.yml');
        $params = $confg['security']['encoders'][get_class($entity)];
        $encoder = new MessageDigestPasswordEncoder($params['algorithm'], $params['encode_as_base64'], $params['iterations']);
        $password = $encoder->encodePassword($entity->getPassword(), $entity->getSalt());
        $entity->setPassword($password);
    }

    // codificar el passwd
    private function setSecurePasswordForgot(&$entity) {
        $confg = Yaml::parse(__DIR__ . '/../../../../app/config/security.yml');
        $params = $confg['security']['encoders'][get_class($entity)];
        $encoder = new MessageDigestPasswordEncoder($params['algorithm'], $params['encode_as_base64'], $params['iterations']);
        $password = $encoder->encodePassword($entity->getPasswordForgot(), $entity->getSalt());
        $entity->setPassword($password);
    }

    /**
     * Crea una nueva customer
     *
     * @Route("/send_newpass", name="send_newpass")
     */
    public function send_newpassAction(\Symfony\Component\HttpFoundation\Request $request) {

        $request = $this->getRequest();
        $email = $request->get('email');
        $em = $this->getDoctrine()->getManager();
        $repo_customer = $em->getRepository("EcomerceBundle:Customer");
        $customer = $repo_customer->findByEmail($email);
        $theme = $this->get('apptibase.cms')->getTheme();
        $entity = new \ACI\EcomerceBundle\Entity\Customer();
        $form = $this->createForm(new \ACI\EcomerceBundle\Form\ForgotType(), $entity);
        if ($customer) {

            $cadenahash = $this->get('apptibase.cms')->RandomString();
            $subject = "Cambio de contraseña";
            $body = "Estimado usuario: " . $email;
            $body.="<br>";
            $body.="Este correo es para hacerle llegar la nueva contrase&ntilde;a para que pueda entrar a nuestro sitio";
            $body.="<br>";
            $body.="<br>";
            $body.="Datos de la nueva contrase&ntilde;a generada: " . $cadenahash;
            $body.="<br>";
            $body.="Por favor, entre el siguiente enlace para su activaci&oacute;n: " . '<a href="http://www.apptibase.com/reactivar/email/' . $email . '">Activar</a>';
            $body.="<br>";
            $body.="Cualquier duda al respecto no deje de contactarnos mediante el apartado " . '<a href="http://www.apptibase.com/cms/contacto">Contacto GFD</a>';
            $body.="<br>";
            $body.="<br>";
            $body.="CONTACTO";
            $body.="<br>";
            $body.="jplanas@globalfooddivision.com,contacto@globalfooddivision.com";
            $body.="<br>";
            $body.="Tel. (55) 3095 8888 Ext. 118";
            $body.="<br>";
            $body.="<br>";
            $body.="DIRECCI&Oacute;N";
            $body.="<br>";
            $body.="Prolongaci&oacute;n Calle 18 No. 218 Col. San Pedro de los Pinos Del. &Aacute;lvaro Obreg&oacute;n M&eacute;xico D.F, C.P 01180 ";
            $body.="<br>";

            $remitente = 'no-reply@www.apptibase.com';
            $destino = $email;
            $mensaje = $body;
            $encabezados = "From: $remitente\nReply-To: $remitente\nContent-Type: text/html; charset=iso-8859-1";

            try {
                $customer->setPasswordForgot($cadenahash);
                $em->persist($customer);
                $em->flush();
                mail($destino, $subject, $mensaje, $encabezados) or die("No se ha podido enviar tu mensaje. Ha ocurrido un error");

                return $this->render($theme . "Bundle:templates:forgotsuccess.html.twig");
            } catch (\Exception $exc) {
                return $this->render($theme . "Bundle:templates:forgotpasswd.html.twig", array(
                            'error' => "Ha ocurrido un error, detalle: " . $exc->getMessage()
                ));
            }
        } else {
            return $this->render($theme . "Bundle:templates:forgotpasswd.html.twig", array(
                        'entity' => $entity,
                        'form' => $form->createView(),
                        'theme' => $theme,
                        'error' => "El email introducido no se encuentra registrado en la tienda."
            ));
        }
    }

    /**
     * Crea una nueva customer
     *
     * @Route("/reactivar/email/{email}", name="reactivar")
     */
    public function reactivarAction($email) {
        if (isset($email)) {
            $em = $this->getDoctrine()->getManager();
            $repo_customer = $em->getRepository("EcomerceBundle:Customer");
            $customer = $repo_customer->findByEmail($email);
            $theme = $this->get('apptibase.cms')->getTheme();
            $form = $this->createForm(new \ACI\EcomerceBundle\Form\RegisterType(), $customer);
            if ($customer) {

                try {
                    $this->setSecurePasswordForgot($customer);
                    $em->persist($customer);
                    $em->flush();
                    $token = new \Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken($customer, $customer->getPassword(), 'usuarios', $customer->getRoles());
                    $this->container->get('security.context')->setToken($token);


                    return $this->render($theme . "Bundle:templates:profile.html.twig", array(
                                'entity' => $customer,
                                'form' => $form->createView(),
                                'theme' => $theme
                    ));
                } catch (\Exception $exc) {
                    return $this->render($theme . "Bundle:templates:forgotpasswd.html.twig", array(
                                'error' => "Ha ocurrido un error, detalle: " . $exc->getMessage()
                    ));
                }
            } else {
                return $this->render($theme . "Bundle:templates:forgotpasswd.html.twig", array(
                            'entity' => $entity,
                            'form' => $form->createView(),
                            'theme' => $theme,
                            'error' => "Vuelva a realizar el proceso por favor."
                ));
            }
        } else {
            return $this->redirect($this->generateUrl('index'));
        }
    }

}
