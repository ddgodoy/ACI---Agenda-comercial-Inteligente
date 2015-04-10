<?php

namespace ACI\DatatableBundle\Twig\Extension;

use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use ACI\DatatableBundle\Util\Datatable;

class DatatableExtension extends \Twig_Extension {

    private $container;

    /**
     * class constructor 
     * 
     * @param ContainerInterface $container 
     */
    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions() {
        return array(
            'datatable' => new \Twig_Function_Method($this, 'datatable', array("is_safe" => array("html")))
        );
    }

    /**
     * Converts a string to time
     * 
     * @param string $string
     * @return int 
     */
    public function datatable($options) {
        $datatable = Datatable::getInstance($options['id']);

        $options['js'] = json_encode($options['js']);
        $options['fields'] = $datatable->getFields();
        $options['search'] = $datatable->getSearch();
        
        $main_template = 'DatatableBundle:Main:index.html.twig';
        if (isset($options['main_template'])) {
            $main_template = $options['main_template'];
        }

        return $this->container->get('templating')->render($main_template, $options);
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName() {
        return 'DatatableBundle';
    }

}