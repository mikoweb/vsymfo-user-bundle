<?php

/*
 * This file is part of the vSymfo package.
 *
 * website: www.mikoweb.pl
 * (c) Rafał Mikołajun <rafal@mikoweb.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace vSymfo\Bundle\UserBundle\DataFixtures\Install;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use vSymfo\Bundle\UserBundle\Entity\Role;
use vSymfo\Core\DataFixtures\AbstractRoleFixture;

/**
 * @author Rafał Mikołajun <rafal@mikoweb.pl>
 * @package vSymfo User Bundle
 * @subpackage DataFixtures
 */
class LoadRoleData extends AbstractRoleFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->fixturesDirectory = __DIR__ . '/../../Resources/fixtures/';
        $this->loadRolesFromXml($manager, 'roles.xml');

        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getRoleClass()
    {
        return Role::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 20;
    }
}
