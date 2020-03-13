<?php

declare(strict_types=1);

namespace App\Admin\Shop;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

final class CategoryAdmin extends AbstractAdmin
{
    protected function configureListFields(ListMapper $list)
    {
        $list
            ->add('id')
            ->addIdentifier('name')
            ->add('updatedAt')
            ->add('_action', null, [
                'actions' => [
                    'edit' => [],
                    'delete' => [],
                ],
            ])
        ;
    }

    protected function configureFormFields(FormMapper $form)
    {
        $form->tab('Informations');

        $form->with('About');
        $form
            ->add('name')
            ->add('description')
        ;
        $form->end();

        $form->with('SEO');
        $form
            ->add('slug', null, ['required' => false])
            ->add('metaTitle', null, ['required' => false])
            ->add('metaDescription', null, ['required' => false])
        ;
        $form->end();

        $form->end();
    }
}
