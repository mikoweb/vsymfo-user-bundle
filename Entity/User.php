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
use vSymfo\Bundle\UserBundle\Entity\Traits\BlameableTrait;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use vSymfo\Core\Entity\Interfaces\BlameableEntityInterface;
use vSymfo\Core\Entity\Interfaces\SoftDeleteableInterface;
use vSymfo\Core\Entity\Interfaces\TimestampableInterface;
use vSymfo\Core\Entity\Traits\SoftDeleteableTrait;
use vSymfo\Core\Entity\Traits\TimestampableTrait;

/**
 * @author Rafał Mikołajun <rafal@mikoweb.pl>
 * @package vSymfo User Bundle
 * @subpackage Entity
 *
 * @ORM\Entity(repositoryClass="vSymfo\Bundle\UserBundle\Repository\UserRepository")
 * @ORM\Table(name="users")
 *
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @UniqueEntity(fields={"username"}, errorPath="username", message="user.unique_username")
 * @UniqueEntity(fields={"email"}, errorPath="email", message="user.unique_email")
 */
class User extends BaseUser implements
    TimestampableInterface,
    SoftDeleteableInterface,
    BlameableEntityInterface
{
    use TimestampableTrait;
    use SoftDeleteableTrait;
    use BlameableTrait;

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var ArrayCollection|Group[]
     *
     * @ORM\ManyToMany(targetEntity="Group", inversedBy="users")
     * @ORM\JoinTable(name="users_groups")
     *
     * @Assert\Count(min="1", minMessage="user.groups_not_blank", groups={"change_group"})
     */
    protected $groups;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", nullable=true)
     */
    protected $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", nullable=true)
     */
    protected $lastName;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="fos_user.username.blank", groups={"_Registration", "_Profile"})
     * @Assert\Length(min="4", max="255", minMessage="fos_user.username.short",
     *     maxMessage="fos_user.username.long", groups={"_Registration", "_Profile"})
     */
    protected $username;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="fos_user.email.blank", groups={"_Registration", "_Profile"})
     * @Assert\Length(min="2", max="254", minMessage="fos_user.email.short",
     *     maxMessage="fos_user.email.long", groups={"_Registration", "_Profile"})
     * @Assert\Email(message="fos_user.email.invalid", groups={"_Registration", "_Profile"})
     */
    protected $email;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="fos_user.password.blank", groups={"_Registration", "_ResetPassword", "_ChangePassword"})
     * @Assert\Length(min="6", max="4096", minMessage="fos_user.password.short",
     *     groups={"_Registration", "_Profile", "_ResetPassword", "_ChangePassword"})
     */
    protected $plainPassword;

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        if (empty($this->lastName)) {
            return $this->getUsername();
        }

        return trim($this->firstName . ' ' . $this->lastName);
    }

    /**
     * @return string
     */
    public function getInvertFullName()
    {
        if (empty($this->lastName)) {
            return $this->getUsername();
        }

        return trim($this->lastName . ' ' . $this->firstName);
    }
}
