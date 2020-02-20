<?php

declare(strict_types=1);

namespace App\Form\Type\Security;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

final class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('_username', EmailType::class, ['label' => 'Email address', 'required' => true])
            ->add('_password', PasswordType::class, ['label' => 'Password', 'required' => true])
            ->add('rememberMe', CheckboxType::class, ['label' => 'Remember me', 'required' => false])
            ->add('submit', SubmitType::class, ['label' => 'Sign in'])
        ;
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
