<?php

declare(strict_types=1);

namespace App\Action\Dashboard;

use App\Form\Dto\Dashboard\CreateUserAddress;
use App\Form\Type\Dashboard\CreateUserAddressType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Twig\Environment;

final class AddressAction
{
    private $twig;

    private $flashBag;

    private $formFactory;

    public function __construct(
        Environment $twig,
        FlashBagInterface $flashBag,
        FormFactoryInterface $formFactory
    ) {
        $this->twig = $twig;
        $this->flashBag = $flashBag;
        $this->formFactory = $formFactory;
    }

    public function __invoke(Request $request)
    {
        $createUserAddressForm = $this->formFactory->create(CreateUserAddressType::class);

        $createUserAddressForm->handleRequest($request);

        if ($createUserAddressForm->isSubmitted() && $createUserAddressForm->isValid()) {
            /** @var CreateUserAddress $createUserAddress */
            $createUserAddress = $createUserAddressForm->getData();

            $this->handleAddressCreation($createUserAddress);

            $this->flashBag->add('success', 'Address created successfully !');
        }

        return new Response(
            $this->twig->render('dashboard/address.html.twig', [
                'createUserAddressForm' => $createUserAddressForm->createView(),
            ])
        );
    }

    private function handleAddressCreation(CreateUserAddress $createUserAddress)
    {
        // todo : Handle address creation
    }
}
