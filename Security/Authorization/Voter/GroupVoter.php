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

namespace vSymfo\Bundle\UserBundle\Security\Authorization\Voter;

use vSymfo\Bundle\UserBundle\Entity\Group;
use vSymfo\Bundle\UserBundle\Entity\User;
use vSymfo\Bundle\UserBundle\Repository\GroupRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use vSymfo\Core\Security\Authorization\Voter\AbstractVoter;

/**
 * @author Rafał Mikołajun <rafal@mikoweb.pl>
 * @package vSymfo User Bundle
 * @subpackage Security
 */
class GroupVoter extends AbstractVoter
{
    const GROUP_REMOVE_ACCESS = 'group_remove_access';
    const GROUP_REMOVE_IS_NO_USERS_ACCESS = 'group_remove_is_no_users_access';

    /**
     * @var GroupRepository
     */
    private $repository;

    /**
     * @param GroupRepository $repository
     */
    public function __construct(GroupRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($attribute, $subject)
    {
        return $subject instanceof Group && in_array($attribute, [
            self::GROUP_REMOVE_ACCESS,
            self::GROUP_REMOVE_IS_NO_USERS_ACCESS,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute($attribute, $entity, TokenInterface $token)
    {
        /* @var Group $entity */

        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        if ($attribute === self::GROUP_REMOVE_ACCESS) {
            return !$entity->isIrremovable()
                && $this->getAuthChecker()->isGranted('ROLE_REMOVE_GROUP');
        }

        if ($attribute === self::GROUP_REMOVE_IS_NO_USERS_ACCESS) {
            return !$entity->isIrremovable()
                && $this->getAuthChecker()->isGranted('ROLE_REMOVE_GROUP')
                && $this->repository->countUsers($entity) === 0;
        }

        return false;
    }
}
