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

use vSymfo\Bundle\FOSUserBundle\Controller\SecurityController as BaseController;

/**
 * @author Rafał Mikołajun <rafal@mikoweb.pl>
 * @package vSymfo User Bundle
 * @subpackage Controller
 */
class SecurityController extends BaseController
{
    /**
     * @inheritdoc
     */
    protected function renderLogin(array $data)
    {
        return $this->render($this->getParameter('vsymfo_user.security_controller.login_view'), $data);
    }
}
