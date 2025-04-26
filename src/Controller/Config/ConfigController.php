<?php

namespace App\Controller\Config;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ConfigController extends AbstractController
{
    #[Route(path: '/config', name: 'config')]
    public function index(Request $request): Response
    {
        $clean = \array_filter(
            $request->query->all(),
            fn($k) => $k === '' || $k[0] !== '_',
            \ARRAY_FILTER_USE_KEY
        );

        return $this->render('pages/config/config.html.twig', [
            'params' => $clean,
        ]);
    }
}
