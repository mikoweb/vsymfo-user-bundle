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

namespace vSymfo\Bundle\UserBundle\Form\Type;

use vSymfo\Bundle\UserBundle\Entity\Group;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Rafał Mikołajun <rafal@mikoweb.pl>
 * @package vSymfo User Bundle
 * @subpackage Form
 */
class UserListFilterFormType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'users.form_filter.name',
                'required' => false,
                'attr' => [
                    'placeholder' => 'users.form_filter.name_placeholder',
                ],
            ])
            ->add('type', GroupTypeFormType::class, [
                'label' => 'users.form_filter.type',
                'placeholder' => 'users.form_filter.choice_type',
                'required' => false,
                'attr' => [
                    'class' => 'select2',
                ]
            ])
            ->add('group', EntityType::class, [
                'class' => Group::class,
                'placeholder' => 'users.form_filter.choice_group',
                'label' => 'users.form_filter.group',
                'required' => false,
                'choice_translation_domain' => 'messages',
                'attr' => [
                    'class' => 'select2',
                ]
            ])
            ->add('locked', ChoiceType::class, [
                'required' => false,
                'empty_data' => null,
                'placeholder' => 'users.form_filter.all_users',
                'choices' => [
                    'users.form_filter.active_users' => 0,
                    'users.form_filter.locked_users' => 1,
                ],
                'attr' => [
                    'class' => 'select2',
                ]
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'cascade_validation' => true,
            'method' => 'get',
            'csrf_protection' => false,
        ]);
    }
}
