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
use vSymfo\Bundle\UserBundle\Entity\Role;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Rafał Mikołajun <rafal@mikoweb.pl>
 * @package vSymfo User Bundle
 * @subpackage Form
 */
class GroupFormType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'groups.fields.name',
            ])
            ->add('type', GroupTypeFormType::class, [
                'label' => 'groups.fields.type',
                'attr' => [
                    'class' => 'select2',
                ],
            ])
            ->add('groupRole', TextType::class, [
                'required' => false,
                'label' => 'groups.fields.group_role'
            ])
            ->add('roles', EntityType::class, [
                'class' => Role::class,
                'label' => 'groups.fields.roles',
                'required' => false,
                'multiple' => true,
                'choice_label' => 'name',
                'choice_translation_domain' => 'messages',
                'group_by' => 'tag',
                'attr' => [
                    'class' => 'select2',
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'global.buttons.save',
                'attr' => [
                    'class' => 'btn-success',
                    'icon' => 'fa fa-floppy-o'
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
            'data_class' => Group::class,
            'cascade_validation' => true,
        ]);
    }
}
