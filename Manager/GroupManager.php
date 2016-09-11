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

namespace vSymfo\Bundle\UserBundle\Manager;

use vSymfo\Bundle\UserBundle\Entity\Group;
use vSymfo\Bundle\UserBundle\Form\Type\GroupFormType;
use vSymfo\Bundle\UserBundle\Repository\GroupRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Knp\Component\Pager\Paginator;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use vSymfo\Core\Entity\EntityFactoryInterface;
use vSymfo\Core\Manager\ControllerManagerAbstract;

/**
 * @author Rafał Mikołajun <rafal@mikoweb.pl>
 * @package vSymfo User Bundle
 * @subpackage Manager
 */
class GroupManager extends ControllerManagerAbstract
{
    /**
     * @var GroupRepository
     */
    private $repository;

    /**
     * @var Paginator
     */
    private $paginator;

    /**
     * @param EntityManager $manager
     * @param FormFactory $formFactory
     * @param EntityFactoryInterface $entityFactory
     * @param GroupRepository $repository
     * @param Paginator $paginator
     */
    public function __construct(
        EntityManager $manager,
        FormFactory $formFactory,
        EntityFactoryInterface $entityFactory,
        GroupRepository $repository,
        Paginator $paginator
    ) {
        parent::__construct($manager, $formFactory, $entityFactory);
        $this->repository = $repository;
        $this->paginator = $paginator;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityClass()
    {
        return Group::class;
    }

    /**
     * {@inheritdoc}
     */
    public function isRightEntity($entity)
    {
        return $entity instanceof Group;
    }

    /**
     * {@inheritdoc}
     *
     * @return PaginationInterface
     */
    public function getPagination(Request $request, $limit)
    {
        $qb = $this->repository->getQueryBuilder();
        $pagination = $this->paginator->paginate($qb, (int) $request->get('page', 1), $limit);

        return $pagination;
    }

    /**
     * {@inheritdoc}
     */
    public function formType()
    {
        return GroupFormType::class;
    }

    /**
     * @param Group $group
     *
     * @return int
     */
    public function countUsers(Group $group)
    {
        return $this->repository->countUsers($group);
    }

    /**
     * {@inheritdoc}
     */
    public function remove($data)
    {
        /** @var Group $data */
        $this->invalidEntityException($data);

        if ($this->countUsers($data) > 0) {
            throw new ORMException('You can not delete that group. The Group has users.');
        }

        $this->manager->remove($data);
        $this->manager->flush();
    }

    /**
     * @param Group|Group[] $groups
     *
     * @return int|array
     */
    public function getCountUsers($groups)
    {
        if (is_array($groups)) {
            return $this->repository->countUsersFromArray($groups);
        } elseif ($groups instanceof Group) {
            return $this->repository->countUsers($groups);
        }

        throw new \InvalidArgumentException('invalid groups argument');
    }
}
