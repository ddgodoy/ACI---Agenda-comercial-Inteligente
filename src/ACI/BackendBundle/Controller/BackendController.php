<?php

namespace ACI\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/admin")
 */
class BackendController extends Controller {

    /**
     * @Route("/dashboard", name="admin_dashboard")
     * @Template()
     */
    public function dashboardAction() {
        return array();
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
     * @Route("/crawlercountry", name="admin_crawlercountry")
     * @Template()
     */
    public function crawlerCountryAction() {
        $this->container->get('aci_app.crawler.rankandfiled')->parseHtmlCountries();
    }

}
