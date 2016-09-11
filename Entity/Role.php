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

namespace vSymfo\Bundle\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use vSymfo\Core\Entity\RoleAbstract;

/**
 * @author Rafał Mikołajun <rafal@mikoweb.pl>
 * @package vSymfo User Bundle
 * @subpackage Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="user_roles")
 *
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(fields={"name"}, errorPath="name", message="role.unique_name")
 * @UniqueEntity(fields={"role"}, errorPath="role", message="role.unique_role")
 */
class Role extends RoleAbstract
{
    const ROLE_USER = 'ROLE_USER';
    const ROLE_ADMIN = 'ROLE_ADMIN';

    /**
     * @var ArrayCollection|Group[]
     *
     * @ORM\ManyToMany(targetEntity="Group", mappedBy="roles")
     */
    protected $groups;

    /**
     * @return ArrayCollection|Group[]
     */
    public function getGroups()
    {
        return $this->groups;
    }
}
