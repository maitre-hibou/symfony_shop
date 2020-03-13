<?php

declare(strict_types=1);

namespace App\Admin\Shop;

use App\Entity\Shop\AbstractProduct;
use App\Entity\Shop\DefaultProduct;
use App\Shop\ProductInterface;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\DoctrineORMAdminBundle\Filter\CallbackFilter;
use Sonata\Form\Type\EqualType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

final class ProductAdmin extends AbstractAdmin
{
    protected $translationDomain = 'shop_product';

    protected $datagridValues = [
        'type' => [
            'type' => EqualType::TYPE_IS_EQUAL,
            'value' => DefaultProduct::class,
        ],
    ];

    protected function configureDatagridFilters(DatagridMapper $filter)
    {
        $filter
            ->add(
                'type',
                CallbackFilter::class,
                [
                    'callback' => function ($queryBuilder, $alias, $field, $value) {
                        if (!$value['value']) {
                            return false;
                        }

                        $queryBuilder->andWhere(
                            $queryBuilder->expr()->isInstanceOf($alias, $value['value'])
                        );

                        return true;
                    },
                    'show_filter' => true,
                ],
                ChoiceType::class,
                [
                    'choices' => [
                        sprintf('Default Product') => DefaultProduct::class,
                    ],
                ]
            )
            ->add('status', null, [], ChoiceType::class, [
                'choices' => [
                    sprintf('Status %s', AbstractProduct::STATUS_DRAFT) => AbstractProduct::STATUS_DRAFT,
                    sprintf('Status %s', AbstractProduct::STATUS_OFFLINE) => AbstractProduct::STATUS_OFFLINE,
                    sprintf('Status %s', AbstractProduct::STATUS_ONLINE) => AbstractProduct::STATUS_ONLINE,
                ],
            ])
            ->add('categories')
        ;
    }

    protected function configureListFields(ListMapper $list)
    {
        $list
            ->add('id')
            ->addIdentifier('title')
            ->add('status', 'choice', [
                'editable' => true,
                'choices' => [
                    AbstractProduct::STATUS_DRAFT => sprintf('Status %s', AbstractProduct::STATUS_DRAFT),
                    AbstractProduct::STATUS_OFFLINE => sprintf('Status %s', AbstractProduct::STATUS_OFFLINE),
                    AbstractProduct::STATUS_ONLINE => sprintf('Status %s', AbstractProduct::STATUS_ONLINE),
                ],
            ])
        ;

        $gridFilters = $this->getFilterParameters();

        if (array_key_exists('type', $gridFilters)) {
            $typeFilter = $gridFilters['type'];

            if (DefaultProduct::class === $typeFilter['value']) {
                $list
                    ->add('stock', 'string', [
                        'editable' => true,
                    ])
                    ->add('productUnit')
                ;
            }
        }

        $list
            ->add('categories')
            ->add('unitPriceExclTax', 'string', [
                'editable' => true,
            ])
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

        $form->with('Product informations');
        $form
            ->add('title')
            ->add('description')
            ->add('status', ChoiceType::class, [
                'choices' => [
                    sprintf('Status %s', AbstractProduct::STATUS_DRAFT) => AbstractProduct::STATUS_DRAFT,
                    sprintf('Status %s', AbstractProduct::STATUS_OFFLINE) => AbstractProduct::STATUS_OFFLINE,
                    sprintf('Status %s', AbstractProduct::STATUS_ONLINE) => AbstractProduct::STATUS_ONLINE,
                ],
                'expanded' => false,
                'multiple' => false,
            ])
            ->add('categories')
        ;
        $form->end();

        if ($this->getSubject() instanceof DefaultProduct) {
            $form->with('Dimensions');
            $form
                ->add('weight')
                ->add('width', null, ['required' => false])
                ->add('height', null, ['required' => false])
                ->add('length', null, ['required' => false])
            ;
            $form->end();
        }

        $form->end();

        $form->tab('Prices & Stock');

        $form->with('Prices');
        $form
            ->add('unitPriceExclTax')
            ->add('vat', ChoiceType::class, [
                'choices' => [
                    sprintf('%s %%', ProductInterface::VAT_20) => ProductInterface::VAT_20,
                    sprintf('%s %%', ProductInterface::VAT_10) => ProductInterface::VAT_10,
                    sprintf('%s %%', ProductInterface::VAT_55) => ProductInterface::VAT_55,
                ],
            ])
        ;
        $form->end();

        if ($this->getSubject() instanceof DefaultProduct) {
            $form->with('Stock');
            $form
                ->add('productUnit')
                ->add('stock')
            ;
            $form->end();
        }

        $form->end();

        $form->tab('SEO')->with('SEO Informations');
        $form
            ->add('slug', null, ['required' => false])
            ->add('metaTitle', null, ['required' => false])
            ->add('metaDescription', null, ['required' => false])
        ;
        $form->end()->end();
    }
}
