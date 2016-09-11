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
use Symfony\Component\Validator\Constraints as Assert;
use vSymfo\Core\Entity\GroupAbstract;

/**
 * @author Rafał Mikołajun <rafal@mikoweb.pl>
 * @package vSymfo User Bundle
 * @subpackage Entity
 *
 * @ORM\Entity(repositoryClass="vSymfo\Bundle\UserBundle\Repository\GroupRepository")
 * @ORM\Table(name="user_groups")
 *
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(fields={"name"}, errorPath="name", message="group.unique_name")
 */
class Group extends GroupAbstract
{
    const NAME_ADMIN = 'groups.names.admin';
    const NAME_USER = 'groups.names.user';
    const TYPE_USER = 'groups.types.user';
    const TYPE_EMPLOYEE = 'groups.types.employee';

    /**
     * @var ArrayCollection|Role[]
     *
     * @ORM\ManyToMany(targetEntity="Role", inversedBy="groups")
     * @ORM\JoinTable(name="user_groups_roles")
     */
    protected $roles;

    /**
     * @var ArrayCollection|User[]
     *
     * @ORM\ManyToMany(targetEntity="User", mappedBy="groups")
     */
    protected $users;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", nullable=false)
     *
     * @Assert\NotBlank(message="group.type_not_blank")
     */
    protected $type;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="name", unique=true, nullable=false)
     *
     * @Assert\NotBlank(message="group.name_not_blank")
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="group_role", type="string", nullable=false)
     *
     * @Assert\NotBlank(message="group.group_role_not_blank")
     */
    protected $groupRole;

    public function __construct()
    {
        parent::__construct();
        $this->type = self::TYPE_USER;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }
}
