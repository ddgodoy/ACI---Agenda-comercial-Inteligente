<?php

namespace ACI\SafetyBundle\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use ACI\SafetyBundle\Entity\User;
use ACI\SafetyBundle\Entity\Role;
use Doctrine\ORM\EntityManager;

class UserLoadData implements FixtureInterface {

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param EntityManager $manager
     */
    public function load(ObjectManager $manager) {
        $role = new Role();
        $role->setName('ROLE_CUSTOMER');
        $role->setDescription('Clientes');
        $manager->persist($role);

        $role2 = new Role();
        $role2->setName('ROLE_ADMIN');
        $role2->setDescription('Administrador');
        $manager->persist($role2);

        // Crear el usuario para la administración
        $admin = new User();
        $admin->setUserName('admin');
        $admin->setName('Administrador del Sistema');
        $admin->setEmail('admin@aci.com');
        $admin->setPassword('acipasswd');
        $admin->addRole($role);
        $manager->persist($admin);

        $manager->flush();
    }

}

?>
