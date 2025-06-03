<?php

namespace App\Controller\Layout;

use App\Repository\MenuRepository;
use App\Repository\ProfileRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class _sidebarController extends AbstractController
{

    private RequestStack $rs;
    private MenuRepository $menuRepo;
    private ProfileRepository $profileRepo;

    public function __construct(RequestStack $rs, MenuRepository $menuRepo, ProfileRepository $profileRepo)
    {
        $this->menuRepo = $menuRepo;
        $this->profileRepo = $profileRepo;
        $this->rs = $rs;
    }

    #[Route('/_sidebar', name: 'app_sidebar')]
    public function index(): Response
    {
        $session   = $this->rs->getCurrentRequest()->getSession();
        $profileId = $session->get('profile_id');

        if (!$profileId) {
            $firstProfile = $this->profileRepo->findOneBy([], ['id' => 'ASC']);
            $profileId = $firstProfile?->getId();
        }

        if (!$profileId) {
            return $this->render('layout/_sidebar.html.twig', [
                'menus'        => [],
                'menuChildren' => [],
            ]);
        }

        $profile = $this->profileRepo->find($profileId);

        if (!$profile) {
            return $this->render('layout/_sidebar.html.twig', [
                'menus'        => [],
                'menuChildren' => [],
            ]);
        }

        $allMenus = $this->menuRepo->findAllByProfile($profile);

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
            'menus'        => $allMenus,
            'menuChildren' => $menuChildren,
        ]);
    }
}
