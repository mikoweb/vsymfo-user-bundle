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

namespace vSymfo\Bundle\UserBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader;

/**
 * @author Rafał Mikołajun <rafal@mikoweb.pl>
 * @package vSymfo User Bundle
 * @subpackage DependencyInjection
 */
class UserExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration($this->getAlias());
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('vsymfo_user.user_controller.view_prefix', $config['user_controller']['view_prefix']);
        $container->setParameter('vsymfo_user.user_controller.route_prefix', $config['user_controller']['route_prefix']);
        $container->setParameter('vsymfo_user.user_controller.message_prefix', $config['user_controller']['message_prefix']);
        $container->setParameter('vsymfo_user.user_controller.manager', $config['user_controller']['manager']);
        $container->setParameter('vsymfo_user.group_controller.view_prefix', $config['group_controller']['view_prefix']);
        $container->setParameter('vsymfo_user.group_controller.route_prefix', $config['group_controller']['route_prefix']);
        $container->setParameter('vsymfo_user.group_controller.message_prefix', $config['group_controller']['message_prefix']);
        $container->setParameter('vsymfo_user.group_controller.manager', $config['group_controller']['manager']);
        $container->setParameter('vsymfo_user.security_controller.login_view', $config['security_controller']['login_view']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('services/forms.yml');
        $loader->load('services/managers.yml');
        $loader->load('services/repositories.yml');
        $loader->load('services/voters.yml');
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        return 'vsymfo_user';
    }
}
