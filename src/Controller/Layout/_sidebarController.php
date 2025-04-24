<?php

namespace App\Controller\Layout;

use App\Repository\MenuRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class _sidebarController extends AbstractController
{
    public function __construct()
    {
        // Constructor code here if needed
    }

    #[Route('/_sidebar', name: 'app_sidebar')]
    public function index(MenuRepository $menu): Response
    {
        $rootMenu = $menu->findAll();

        $menuChildren = [];

        foreach ($rootMenu as $menu) {
            if (!empty($menu->getIdMenuParent())) {
                $parentId = $menu->getIdMenuParent(); // importante: get el id real

                if (!isset($menuChildren[$parentId])) {
                    $menuChildren[$parentId] = [];
                }
                $menuChildren[$parentId][] = $menu;
            }
        }

        // dump($menuChildren);

        return $this->render('layout/_sidebar.html.twig', [
            'menus' => $rootMenu,
            'menuChildren' => $menuChildren,
        ]);
    }
}
