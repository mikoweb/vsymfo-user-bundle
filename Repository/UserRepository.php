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

namespace vSymfo\Bundle\UserBundle\Repository;

use vSymfo\Bundle\UserBundle\Entity\Group;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * @author Rafał Mikołajun <rafal@mikoweb.pl>
 * @package vSymfo User Bundle
 * @subpackage Repository
 */
class UserRepository extends EntityRepository
{
    /**
     * @param string|null $groupType
     *
     * @return QueryBuilder
     */
    public function getQueryBuilder($groupType = null)
    {
        $qb = $this->createQueryBuilder('u')
            ->addSelect('u')
            ->addSelect('g')
            ->leftJoin('u.groups', 'g')
        ;

        if (!is_null($groupType)) {
            $qb->andWhere('g.type = :group_type');
            $qb->setParameter('group_type', $groupType, 'string');
        }

        return $qb;
    }

    /**
     * @param QueryBuilder $qb
     * @param string $direction
     */
    public function addOrderByFullname(QueryBuilder $qb, $direction)
    {
        $qb->addSelect("CONCAT(COALESCE(u.lastName, u.username), COALESCE(u.firstName, '')) as HIDDEN _concatOrderByFullname");
        $qb->addOrderBy('_concatOrderByFullname', $direction);
    }

    /**
     * @param QueryBuilder $qb
     * @param string $name
     */
    public function andWhereLikeName(QueryBuilder $qb, $name)
    {
        $qb->andWhere($qb->expr()->orX(
            'u.username LIKE :user_repo_like_name',
            "CONCAT(COALESCE(u.firstName, ''), ' ', COALESCE(u.lastName, '')) LIKE :user_repo_like_name"
        ));
        $qb->setParameter('user_repo_like_name', '%' . $name . '%');
    }

    /**
     * @param QueryBuilder $qb
     * @param string $value
     */
    public function andWhereLikeNameOrEmail(QueryBuilder $qb, $value)
    {
        $qb->andWhere($qb->expr()->orX(
            'u.username LIKE :user_repo_like_name_or_email',
            "CONCAT(COALESCE(u.firstName, ''), ' ', COALESCE(u.lastName, '')) LIKE :user_repo_like_name_or_email",
            'u.email LIKE :user_repo_like_name_or_email'
        ));
        $qb->setParameter('user_repo_like_name_or_email', '%' . $value . '%');
    }

    /**
     * @param QueryBuilder $qb
     * @param Group $group
     */
    public function andWhereGroup(QueryBuilder $qb, Group $group)
    {
        $qb->andWhere('g.id = :user_repo_group_id');
        $qb->setParameter('user_repo_group_id', $group);
    }

    /**
     * @param QueryBuilder $qb
     * @param bool $locked
     */
    public function andWhereLocked(QueryBuilder $qb, $locked)
    {
        $qb->andWhere('u.locked = :user_repo_locked');
        $qb->setParameter('user_repo_locked', $locked);
    }
}
