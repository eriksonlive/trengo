<?php

namespace App\Controller\Layout;

use App\Repository\MenuRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class _sidebarController extends AbstractController
{
    #[Route('/_sidebar', name: 'app_sidebar')]
    public function index(MenuRepository $menuRepo): Response
    {
        $rootMenu = $menuRepo->findBy(
            ['IdMenuParent' => null],
            ['orden' => 'ASC']
        );

        $allMenus = $menuRepo->findBy([], ['orden' => 'ASC']);

        $menuChildren = [];
        foreach ($allMenus as $m) {
            if ($m->getIdMenuParent()) {
                $pid = $m->getIdMenuParent()->getId();
                $menuChildren[$pid][] = $m;
            }
        }

        foreach ($menuChildren as &$children) {
            usort($children, fn($a, $b) => (int)$a->getOrden() <=> (int)$b->getOrden());
        }
        unset($children);

        return $this->render('layout/_sidebar.html.twig', [
            'menus'        => $rootMenu,
            'menuChildren' => $menuChildren,
        ]);
    }
}
