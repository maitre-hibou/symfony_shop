<?php

declare(strict_types=1);

namespace App\Action\Dashboard;

use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

final class IndexAction
{
    private $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function __invoke()
    {
        return new Response(
            $this->twig->render('dashboard/index.html.twig')
        );
    }
}
