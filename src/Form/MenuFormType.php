<?php

namespace App\Form;

use App\Entity\Functionality;
use App\Entity\Menu;
use App\Entity\Profile;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MenuFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('orden', null, [
                'required' => false,
                'label' => 'form.menu.order',
                'translation_domain' => 'translation',
                'label_attr' => ['class' => 'block mb-2 text-sm font-medium text-gray-900 dark:text-white'],
                'attr' => [
                    'class' => 'block py-2 ps-3 text-sm text-gray-900 border border-gray-300 rounded-lg w-full bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500'
                ]
            ])
            ->add('menu', null, [
                'label' => 'form.menu.name',
                'translation_domain' => 'translation',
                'label_attr' => ['class' => 'block mb-2 text-sm font-medium text-gray-900 dark:text-white'],
                'attr' => [
                    'class' => 'block py-2 ps-3 text-sm text-gray-900 border border-gray-300 rounded-lg w-full bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500'
                ]
            ])
            // ->add('language', null, [
            //     'label' => 'menu.language',
            //     'translation_domain' => 'translation',
            //     'label_attr' => ['class' => 'block mb-2 text-sm font-medium text-gray-900 dark:text-white'],
            //     'attr' => [
            //         'class' => 'block py-2 ps-3 text-sm text-gray-900 border border-gray-300 rounded-lg w-full bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500'
            //     ]
            // ])
            ->add('profile', EntityType::class, [
                'class' => Profile::class,
                'label' => 'form.menu.profile',
                'translation_domain' => 'translation',
                'choice_label' => 'name',
                'placeholder' => 'form.menu.select_profile',
                'label_attr' => ['class' => 'block mb-2 text-sm font-medium text-gray-900 dark:text-white'],
                'attr' => [
                    'class' => 'block py-2 ps-3 text-sm text-gray-900 border border-gray-300 rounded-lg w-full bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500'
                ]
            ])
            ->add('functionality', EntityType::class, [
                'required' => false,
                'label' => 'form.menu.functionality',
                'translation_domain' => 'translation',
                'class' => Functionality::class,
                'choice_label' => 'name',
                'placeholder'  => 'form.menu.select_functionality',
                'label_attr' => ['class' => 'block mb-2 text-sm font-medium text-gray-900 dark:text-white'],
                'attr' => [
                    'class' => 'block py-2 ps-3 text-sm text-gray-900 border border-gray-300 rounded-lg w-full bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500'
                ]
            ])
            ->add('idMenuParent', EntityType::class, [
                'required' => false,
                'label' => 'form.menu.parent_menu',
                'translation_domain' => 'translation',
                'class' => Menu::class,
                'choice_label' => 'menu',
                'placeholder' => 'form.menu.select_parent_menu',
                'label_attr' => ['class' => 'block mb-2 text-sm font-medium text-gray-900 dark:text-white'],
                'attr' => [
                    'class' => 'block py-2 ps-3 text-sm text-gray-900 border border-gray-300 rounded-lg w-full bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Menu::class,
        ]);
    }
}
