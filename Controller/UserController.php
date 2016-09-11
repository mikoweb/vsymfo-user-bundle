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

namespace vSymfo\Bundle\UserBundle\Controller;

use JMS\SecurityExtraBundle\Annotation\Secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use vSymfo\Bundle\CoreBundle\Crud\Crud;
use vSymfo\Bundle\PanelBundle\Controller\Controller;
use vSymfo\Bundle\UserBundle\Entity\User;
use vSymfo\Bundle\UserBundle\Manager\UserManager;
use vSymfo\Core\Controller\Interfaces\WritableInterface;

/**
 * @author Rafał Mikołajun <rafal@mikoweb.pl>
 * @package vSymfo User Bundle
 * @subpackage Controller
 */
class UserController extends Controller implements WritableInterface
{
    /**
     * @param Request $request
     *
     * @return Response
     *
     * @Secure(roles="ROLE_LIST_USER")
     */
    public function indexAction(Request $request)
    {
        $crud = $this->crudFactory()->create($this);
        $data = $crud->index($request);
        $form = $this->getManager()->buildFilterForm($this->generateUrl($crud->indexRoute()));
        $form->handleRequest($request);

        return $this->renderDocument(':list.html.twig', [
            'users' => $data->getCollection(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * {@inheritdoc}
     *
     * @Secure(roles="ROLE_NEW_USER")
     */
    public function createAction(Request $request)
    {
        $data = $this->crudFactory()->create($this)->create($request);

        return $this->renderDocument(':new.html.twig', [
            'form' => $data->getForm()->createView(),
        ]);
    }

    /**
     * {@inheritdoc}
     *
     * @Secure(roles="ROLE_NEW_USER")
     */
    public function storeAction(Request $request)
    {
        return $this->crudFactory()->create($this)->store($request)->getResponse();
    }

    /**
     * {@inheritdoc}
     *
     * @Secure(roles="ROLE_EDIT_USER")
     */
    public function editAction(Request $request)
    {
        $data = $this->crudFactory()->create($this)->edit($request, [
            'form_options' => $this->formEditOptions()
        ]);

        return $this->renderDocument(':edit.html.twig', [
            'form' => $data->getForm()->createView(),
        ]);
    }

    /**
     * {@inheritdoc}
     *
     * @Secure(roles="ROLE_EDIT_USER")
     */
    public function updateAction(Request $request)
    {
        return $this->crudFactory()->create($this)->update($request, [
            'form_options' => $this->formEditOptions()
        ])->getResponse();
    }

    /**
     * @param User $entity
     *
     * @return Response
     *
     * @Security("is_granted('user_lock_access', entity)")
     */
    public function lockAction(User $entity)
    {
        /** @var Crud $crud */
        $crud = $this->crudFactory()->create($this);
        $this->getManager()->lock($entity);
        $crud->addFlash('success', 'lock_successful');

        return $this->redirectToRoute($crud->indexRoute());
    }

    /**
     * @param User $entity
     *
     * @return Response
     *
     * @Security("is_granted('user_unlock_access', entity)")
     */
    public function unlockAction(User $entity)
    {
        /** @var Crud $crud */
        $crud = $this->crudFactory()->create($this);
        $this->getManager()->unlock($entity);
        $crud->addFlash('success', 'unlock_successful');

        return $this->redirectToRoute($crud->indexRoute());
    }

    /**
     * {@inheritdoc}
     */
    public function getCrudOptions()
    {
        return [
            'route_prefix' => $this->getParameter('vsymfo_user.user_controller.route_prefix'),
            'manager' => $this->getManager(),
            'message_prefix' => $this->getParameter('vsymfo_user.user_controller.message_prefix'),
        ];
    }

    /**
     * @return UserManager
     */
    protected function getManager()
    {
        return $this->get($this->getParameter('vsymfo_user.user_controller.manager'));
    }

    /**
     * {@inheritdoc}
     */
    protected function getViewPrefix()
    {
        return $this->getParameter('vsymfo_user.user_controller.view_prefix');
    }

    /**
     * @return array
     */
    protected function formEditOptions()
    {
        return [
            'validation_groups' => ['Default', '_Profile', 'change_group'],
            'set_password' => false,
        ];
    }
}
