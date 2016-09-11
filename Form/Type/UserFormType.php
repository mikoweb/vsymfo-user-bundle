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
use vSymfo\Bundle\UserBundle\Entity\User;
use vSymfo\Bundle\UserBundle\Repository\GroupRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Rafał Mikołajun <rafal@mikoweb.pl>
 * @package vSymfo User Bundle
 * @subpackage Form
 */
class UserFormType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [
                'label' => 'users.fields.name',
                'attr' => [
                    'min' => 4,
                ]
            ])
            ->add('firstName', TextType::class, [
                'label' => 'users.fields.firstname',
                'required' => false,
            ])
            ->add('lastName', TextType::class, [
                'label' => 'users.fields.lastname',
                'required' => false,
            ])
            ->add('email', EmailType::class, [
                'label' => 'users.fields.email',
            ])
        ;

        if ($options['set_password']) {
            $builder->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'options' => ['translation_domain' => 'FOSUserBundle'],
                'first_options' => ['label' => 'form.password'],
                'second_options' => ['label' => 'form.password_confirmation'],
                'invalid_message' => 'fos_user.password.mismatch',
            ]);
        }

        $builder
            ->add('groups', EntityType::class, [
                'label' => 'users.fields.groups',
                'class' => Group::class,
                'multiple' => true,
                'choice_label' => 'name',
                'choice_translation_domain' => 'messages',
                'attr' => [
                    'class' => 'select2',
                ],
                'query_builder' => function (GroupRepository $repo) use($options) {
                    return $repo->getQueryBuilder($options['group_type']);
                },
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
            'data_class' => User::class,
            'cascade_validation' => true,
            'validation_groups' => ['Default', '_Registration', 'change_group'],
            'group_type' => null,
            'set_password' => true,
        ]);

        $resolver->setAllowedTypes('set_password', 'bool');
        $resolver->setAllowedValues('group_type', [
            null,
            Group::TYPE_USER,
            Group::TYPE_EMPLOYEE,
        ]);
    }
}
