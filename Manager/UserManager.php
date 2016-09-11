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

use vSymfo\Bundle\UserBundle\Entity\User;
use vSymfo\Bundle\UserBundle\Form\Type\UserFormType;
use vSymfo\Bundle\UserBundle\Form\Type\UserListFilterFormType;
use vSymfo\Bundle\UserBundle\Repository\UserRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Doctrine\UserManager as FOSUserManager;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\Paginator;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use vSymfo\Core\Entity\EntityFactoryInterface;
use vSymfo\Core\Manager\ControllerManagerAbstract;
use vSymfo\Core\Manager\SortingManager;

/**
 * @author Rafał Mikołajun <rafal@mikoweb.pl>
 * @package vSymfo User Bundle
 * @subpackage Manager
 */
class UserManager extends ControllerManagerAbstract
{
    /**
     * @var UserRepository
     */
    protected $repository;

    /**
     * @var Paginator
     */
    protected $paginator;

    /**
     * @var FOSUserManager
     */
    protected $userManager;

    /**
     * @var SortingManager
     */
    protected $sortingManager;

    /**
     * @param EntityManager $manager
     * @param FormFactory $formFactory
     * @param EntityFactoryInterface $entityFactory
     * @param UserRepository $repository
     * @param Paginator $paginator
     * @param FOSUserManager $userManager
     */
    public function __construct(
        EntityManager $manager,
        FormFactory $formFactory,
        EntityFactoryInterface $entityFactory,
        UserRepository $repository,
        Paginator $paginator,
        FOSUserManager $userManager
    ) {
        parent::__construct($manager, $formFactory, $entityFactory);
        $this->repository = $repository;
        $this->paginator = $paginator;
        $this->userManager = $userManager;
        $this->sortingManager = $this->createSortingManager();
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityClass()
    {
        return User::class;
    }

    /**
     * {@inheritdoc}
     */
    public function isRightEntity($entity)
    {
        return $entity instanceof User;
    }

    /**
     * {@inheritdoc}
     *
     * @return PaginationInterface
     */
    public function getPagination(Request $request, $limit)
    {
        if (is_array($data = $request->get('filter'))) {
            $form = $this->buildForm(null, [], UserListFilterFormType::class);
            $form->submit($data);
            $qb = $this->repository->getQueryBuilder($form->get('type')->getData());

            if ($name = $form->get('name')->getData()) {
                $this->repository->andWhereLikeNameOrEmail($qb, $name);
            }

            if ($group = $form->get('group')->getData()) {
                $this->repository->andWhereGroup($qb, $group);
            }

            $locked = $form->get('locked')->getData();
            if (is_int($locked)) {
                $this->repository->andWhereLocked($qb, $locked);
            }
        } else {
            $qb = $this->repository->getQueryBuilder();
        }

        $this->sortingManager->sort($request, $qb);
        $pagination = $this->paginator->paginate($qb, (int) $request->get('page', 1), $limit);

        return $pagination;
    }

    /**
     * {@inheritdoc}
     */
    public function formType()
    {
        return UserFormType::class;
    }

    /**
     * Form to filter users.
     *
     * @param string $url
     * 
     * @return Form
     */
    public function buildFilterForm($url)
    {
        return $this->formFactory->createNamed('filter', UserListFilterFormType::class, null, [
            'action' => $url
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function save($data)
    {
        $this->userManager->updateUser($data);
    }

    /**
     * {@inheritdoc}
     */
    public function remove($data)
    {
        $this->userManager->deleteUser($data);
    }

    /**
     * @return SortingManager
     */
    public function createSortingManager()
    {
        $sorting = new SortingManager();
        $repository = $this->repository;

        $sorting->setAllowedColumns([
            'u.id',
            'u.username',
            'u.email',
        ]);

        $sorting->addCustomSort('u.fullname', function (QueryBuilder $qb, $direction) use ($repository) {
            $repository->addOrderByFullname($qb, $direction);
        });

        return $sorting;
    }

    /**
     * @param User $user
     */
    public function lock(User $user)
    {
        $user->setLocked(true);
        $this->manager->persist($user);
        $this->manager->flush();
    }

    /**
     * @param User $user
     */
    public function unlock(User $user)
    {
        $user->setLocked(false);
        $this->manager->persist($user);
        $this->manager->flush();
    }
}
