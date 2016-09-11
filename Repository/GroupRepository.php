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
class GroupRepository extends EntityRepository
{
    /**
     * @param string|null $type
     *
     * @return QueryBuilder
     */
    public function getQueryBuilder($type = null)
    {
        $qb = $this->createQueryBuilder('g');

        if (is_string($type)) {
            $qb->andWhere('g.type = :group_type');
            $qb->setParameter('group_type', $type);
        }

        return $qb;
    }

    /**
     * @param Group $group
     *
     * @return integer
     */
    public function countUsers(Group $group)
    {
        $qb = $this->createQueryBuilder('g')
            ->select('COUNT(u.id)')
            ->leftJoin('g.users', 'u')
            ->where('g.id = :group_id')
            ->setParameter('group_id', $group)
        ;

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @param Group[] $groups
     *
     * @return array
     */
    public function countUsersFromArray(array $groups)
    {
        $qb = $this->createQueryBuilder('g')
            ->select('g.id')
            ->addSelect('COUNT(u.id) AS users')
            ->leftJoin('g.users', 'u')
            ->where('g.id IN (:groups)')
            ->setParameter('groups', $groups)
            ->groupBy('g.id')
        ;

        $result = [];
        $rows = $qb->getQuery()->getResult();

        foreach ($rows as $row) {
            $result['group_' . $row['id']] = $row;
        }

        return $result;
    }
}
