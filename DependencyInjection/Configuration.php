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

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Rafał Mikołajun <rafal@mikoweb.pl>
 * @package vSymfo User Bundle
 * @subpackage DependencyInjection
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @var string
     */
    private $alias;

    /**
     * @param string $alias
     */
    public function __construct($alias)
    {
        $this->alias = $alias;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root($this->alias);
        $rootNode
            ->children()
                ->arrayNode('user_controller')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('view_prefix')
                            ->defaultValue('PanelBundle:User')
                        ->end()
                        ->scalarNode('route_prefix')
                            ->defaultValue('panel_user')
                        ->end()
                        ->scalarNode('message_prefix')
                            ->defaultValue('users.messages')
                        ->end()
                        ->scalarNode('manager')
                            ->defaultValue('vsymfo_user.manager.user')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('group_controller')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('view_prefix')
                            ->defaultValue('PanelBundle:Group')
                        ->end()
                        ->scalarNode('route_prefix')
                            ->defaultValue('panel_group')
                        ->end()
                        ->scalarNode('message_prefix')
                            ->defaultValue('groups.messages')
                        ->end()
                        ->scalarNode('manager')
                            ->defaultValue('vsymfo_user.manager.group')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('security_controller')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('login_view')
                            ->defaultValue('PanelBundle:Security:login.html.twig')
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
