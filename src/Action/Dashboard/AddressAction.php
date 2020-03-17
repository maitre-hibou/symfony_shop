<?php

declare(strict_types=1);

namespace App\Action\Dashboard;

use App\Dashboard\UserAddressManager;
use App\Entity\Security\User;
use App\Form\Dto\Dashboard\CreateUserAddress;
use App\Form\Type\Dashboard\CreateUserAddressType;
use App\Repository\Security\UserAddressRepository;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Twig\Environment;

final class AddressAction
{
    private $twig;

    private $flashBag;

    private $formFactory;

    private $router;

    private $tokenStorage;

    private $userAddressManager;

    private $userAddressRepository;

    public function __construct(
        Environment $twig,
        FlashBagInterface $flashBag,
        FormFactoryInterface $formFactory,
        RouterInterface $router,
        TokenStorageInterface $tokenStorage,
        UserAddressManager $userAddressManager,
        UserAddressRepository $userAddressRepository
    ) {
        $this->twig = $twig;
        $this->flashBag = $flashBag;
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->tokenStorage = $tokenStorage;
        $this->userAddressManager = $userAddressManager;
        $this->userAddressRepository = $userAddressRepository;
    }

    public function __invoke(Request $request)
    {
        /** @var User $user */
        $user = $this->tokenStorage->getToken()->getUser();

        $createUserAddressForm = $this->formFactory->create(CreateUserAddressType::class);

        $createUserAddressForm->handleRequest($request);

        if ($createUserAddressForm->isSubmitted() && $createUserAddressForm->isValid()) {
            /** @var CreateUserAddress $createUserAddress */
            $createUserAddress = $createUserAddressForm->getData();

            $this->userAddressManager->saveUserAddress($createUserAddress, $user);

            $this->flashBag->add('success', 'Address created successfully !');

            return new RedirectResponse($this->router->generate('dashboard_address'));
        }

        $userAddresses = $this->userAddressRepository->findBy(['user' => $user]);

        return new Response(
            $this->twig->render('dashboard/address.html.twig', [
                'createUserAddressForm' => $createUserAddressForm->createView(),
                'userAddresses' => $userAddresses,
            ])
        );
    }
}
