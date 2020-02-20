<?php

declare(strict_types=1);

namespace App\Form\Type\Security;

use App\Form\Dto\Security\Registration;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, ['label' => 'Email address', 'required' => true])
            ->add('firstname', TextType::class, ['label' => 'Firstname', 'required' => true])
            ->add('lastname', TextType::class, ['label' => 'Lastname', 'required' => true])
            ->add('password', RepeatedType::class, [
                'required' => true,
                'type' => PasswordType::class,
                'invalid_message' => 'Password fields must match.',
                'first_options' => ['label' => 'Password'],
                'second_options' => ['label' => 'Confirm password'],
            ])
            ->add('conditionsAccepted', CheckboxType::class, ['label' => 'I accept ... terms and conditions.'])
            ->add('optinCommercial', CheckboxType::class, ['label' => 'I accept to receive commercial offer from ... and partners.', 'required' => false])
            ->add('submit', SubmitType::class, ['label' => 'Register now'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Registration::class,
        ]);
    }
}
