<?php

declare(strict_types=1);

namespace App\Action\Dashboard;

use App\Dashboard\ProfileManager;
use App\Entity\User;
use App\Form\Dto\Dashboard\ChangePassword;
use App\Form\Dto\Dashboard\Informations;
use App\Form\Type\Dashboard\ChangePasswordType;
use App\Form\Type\Dashboard\InformationsType;
use App\Security\UserManager;
use AutoMapperPlus\AutoMapperInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Twig\Environment;

final class ProfileAction
{
    private $twig;

    private $formFactory;

    private $tokenStorage;

    private $autoMapper;

    private $profileManager;

    private $userManager;

    public function __construct(
        Environment $twig,
        FormFactoryInterface $formFactory,
        TokenStorageInterface $tokenStorage,
        AutoMapperInterface $autoMapper,
        ProfileManager $profileManager,
        UserManager $userManager
    ) {
        $this->twig = $twig;
        $this->formFactory = $formFactory;
        $this->tokenStorage = $tokenStorage;
        $this->autoMapper = $autoMapper;
        $this->profileManager = $profileManager;
        $this->userManager = $userManager;
    }

    public function __invoke(Request $request, Session $session)
    {
        /** @var User $user */
        $user = $this->tokenStorage->getToken()->getUser();

        $informationsForm = $this->formFactory->create(
            InformationsType::class,
            $this->autoMapper->map($user, Informations::class)
        );

        $informationsForm->handleRequest($request);

        $changePasswordForm = $this->formFactory->create(ChangePasswordType::class);

        $changePasswordForm->handleRequest($request);

        if ($informationsForm->isSubmitted() && $informationsForm->isValid()) {
            /** @var Informations $informations */
            $informations = $informationsForm->getData();

            $this->profileManager->updateInformations($user, $informations);

            $session->getFlashBag()->add('success', 'Your profile has been updated successfully.');
        }

        if ($changePasswordForm->isSubmitted() && $changePasswordForm->isValid()) {
            /** @var ChangePassword $changePassword */
            $changePassword = $changePasswordForm->getData();

            $this->userManager->changeUserPassword($user, $changePassword->password);

            $session->getFlashBag()->add('success', 'Your password has been changed successfully.');
        }

        return new Response(
            $this->twig->render('dashboard/profile.html.twig', [
                'informationsForm' => $informationsForm->createView(),
                'changePasswordForm' => $changePasswordForm->createView(),
            ])
        );
    }
}
