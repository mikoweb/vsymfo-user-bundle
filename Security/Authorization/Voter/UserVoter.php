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

use vSymfo\Bundle\UserBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use vSymfo\Core\Security\Authorization\Voter\AbstractVoter;

/**
 * @author Rafał Mikołajun <rafal@mikoweb.pl>
 * @package vSymfo User Bundle
 * @subpackage Security
 */
class UserVoter extends AbstractVoter
{
    const USER_LOCK_ACCESS = 'user_lock_access';
    const USER_UNLOCK_ACCESS = 'user_unlock_access';

    /**
     * {@inheritdoc}
     */
    public function supports($attribute, $subject)
    {
        return $subject instanceof User && in_array($attribute, [
            self::USER_LOCK_ACCESS,
            self::USER_UNLOCK_ACCESS,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute($attribute, $entity, TokenInterface $token)
    {
        /* @var User $entity */

        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        if ($attribute === self::USER_LOCK_ACCESS) {
            return $entity->getId() !== $user->getId()
                && !$entity->isLocked()
                && $this->getAuthChecker()->isGranted('ROLE_LOCK_USER');
        }

        if ($attribute === self::USER_UNLOCK_ACCESS) {
            return $entity->getId() !== $user->getId()
                && $entity->isLocked()
                && $this->getAuthChecker()->isGranted('ROLE_UNLOCK_USER');
        }

        return false;
    }
}
