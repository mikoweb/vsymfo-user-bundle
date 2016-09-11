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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use vSymfo\Bundle\PanelBundle\Controller\Controller;
use vSymfo\Bundle\UserBundle\Manager\GroupManager;
use vSymfo\Core\Controller\Interfaces\RemovableInterface;
use vSymfo\Core\Controller\Interfaces\WritableInterface;

/**
 * @author Rafał Mikołajun <rafal@mikoweb.pl>
 * @package vSymfo User Bundle
 * @subpackage Controller
 */
class GroupController extends Controller implements WritableInterface, RemovableInterface
{
    /**
     * @param Request $request
     *
     * @return Response
     *
     * @Secure(roles="ROLE_LIST_GROUP")
     */
    public function indexAction(Request $request)
    {
        $manager = $this->getManager();
        $data = $this->crudFactory()->create($this)->index($request);
        $groups = $data->getCollection();
        $countUsers = $manager->getCountUsers($groups->getItems());

        return $this->renderDocument(':list.html.twig', [
            'groups' => $groups,
            'count_users' => $countUsers,
        ]);
    }

    /**
     * {@inheritdoc}
     *
     * @Secure(roles="ROLE_NEW_GROUP")
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
     * @Secure(roles="ROLE_NEW_GROUP")
     */
    public function storeAction(Request $request)
    {
        return $this->crudFactory()->create($this)->store($request)->getResponse();
    }

    /**
     * {@inheritdoc}
     *
     * @Secure(roles="ROLE_EDIT_GROUP")
     */
    public function editAction(Request $request)
    {
        $data = $this->crudFactory()->create($this)->edit($request);

        return $this->renderDocument(':edit.html.twig', [
            'form' => $data->getForm()->createView(),
        ]);
    }

    /**
     * {@inheritdoc}
     *
     * @Secure(roles="ROLE_EDIT_GROUP")
     */
    public function updateAction(Request $request)
    {
        return $this->crudFactory()->create($this)->update($request)->getResponse();
    }

    /**
     * {@inheritdoc}
     */
    public function destroyAction(Request $request)
    {
        $group = $this->getManager()->findEntity($request);
        $this->denyAccessUnlessGranted('group_remove_is_no_users_access', $group);
        $data = $this->crudFactory()->create($this)->destroy($request);

        if ($redirect = $data->getResponse()) {
            return $redirect;
        }

        return $this->renderDocument(':remove.html.twig', [
            'group' => $group,
            'form' => $data->getForm()->createView(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getCrudOptions()
    {
        return [
            'route_prefix' => $this->getParameter('vsymfo_user.group_controller.route_prefix'),
            'manager' => $this->getManager(),
            'message_prefix' => $this->getParameter('vsymfo_user.group_controller.message_prefix'),
        ];
    }

    /**
     * @return GroupManager
     */
    protected function getManager()
    {
        return $this->get($this->getParameter('vsymfo_user.group_controller.manager'));
    }

    /**
     * {@inheritdoc}
     */
    protected function getViewPrefix()
    {
        return $this->getParameter('vsymfo_user.group_controller.view_prefix');
    }
}
