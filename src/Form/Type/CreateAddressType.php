<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\Address as AddressEntity;
use App\Form\Dto\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CreateAddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('civility', ChoiceType::class, [
                'expanded' => false,
                'choices' => [
                    AddressEntity::CIVILITY_MR_LABEL => AddressEntity::CIVILITY_MR,
                    AddressEntity::CIVILITY_MRS_LABEL => AddressEntity::CIVILITY_MRS,
                ],
                'multiple' => false,
                'required' => true,
            ])
            ->add('firstname', null, ['required' => true])
            ->add('lastname', null, ['required' => true])
            ->add('company', null, ['required' => false])
            ->add('address', null, ['required' => true])
            ->add('address2', null, ['required' => false])
            ->add('postcode', null, ['required' => true])
            ->add('city', null, ['required' => true])
            ->add('phone', null, ['required' => true])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
        ]);
    }
}
