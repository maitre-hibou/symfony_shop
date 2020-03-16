<?php

declare(strict_types=1);

namespace App\Form\Type\Dashboard;

use App\Form\Dto\Dashboard\CreateUserAddress;
use App\Form\Type\CreateAddressType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CreateUserAddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('label', null, [
                'required' => false,
            ])
            ->add('default', CheckboxType::class, [
                'label' => 'Set as default address',
                'required' => false,
            ])
            ->add('address', CreateAddressType::class)
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CreateUserAddress::class,
        ]);
    }
}
