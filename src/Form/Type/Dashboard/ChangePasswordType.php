<?php

declare(strict_types=1);

namespace App\Form\Type\Dashboard;

use App\Form\Dto\Dashboard\ChangePassword;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('currentPassword', PasswordType::class, ['label' => 'Current password', 'required' => true])
            ->add('password', RepeatedType::class, [
                'required' => true,
                'type' => PasswordType::class,
                'first_options' => ['label' => 'New password'],
                'second_options' => ['label' => 'Verify new password'],
            ])
            ->add('submit', SubmitType::class, ['label' => 'Save'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ChangePassword::class,
        ]);
    }
}
