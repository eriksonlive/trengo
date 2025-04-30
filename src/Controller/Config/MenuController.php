<?php

namespace App\Controller\Config;

use App\Entity\Menu;
use App\Entity\Profile;
use App\Form\MenuFormType;
use App\Repository\FunctionalityRepository;
use App\Repository\MenuRepository;
use App\Repository\ProfileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MenuController extends AbstractController
{
    #[Route('/config/menu', name: 'config_menu')]
    public function index(
        Request $request,
        MenuRepository $menu,
        FunctionalityRepository $func,
        ProfileRepository $pro,
        RequestStack $rs,
        ProfileRepository $profileRepo
    ): Response {
        $params = array_filter(
            $request->attributes->all(),
            fn($k) => 0 !== strpos($k, '_'),
            \ARRAY_FILTER_USE_KEY
        );

        $session   = $rs->getCurrentRequest()->getSession();
        $profileId = $session->get('profile_id');

        $profile = $profileRepo->find($profileId);

        $itemPerPage = 5;
        $page = max(1, $request->attributes->get('page', 1));
        $offset = ($page - 1) * $itemPerPage;
        $paginator = $menu->getMenuPaginator($offset, $itemPerPage, $profile);
        $totalCount = count($paginator);
        $totalPages = (int) ceil($totalCount / $itemPerPage);

        $profile = $pro->findAll();
        $functionality = $func->findAll();

        $newFunc = new Menu();
        $createForm = $this->createForm(MenuFormType::class, $newFunc, [
            'action' => $this->generateUrl('create_menu'),
            'method' => 'POST',
        ]);

        if (!empty($paginator)) {

            foreach ($paginator as $menu) {
                $editForms[$menu->getId()] = $this->createForm(MenuFormType::class, $menu, [
                    'action' => $this->generateUrl('edit_menu', ['id' => $menu->getId()]),
                    'method' => 'POST',
                ])->createView();
            }

            foreach ($paginator as $menu) {
                $deleteForms[$menu->getId()] = $this->createFormBuilder(null, [
                    'csrf_protection' => true,
                    'csrf_field_name' => '_token',
                    'csrf_token_id'   => 'delete' . $menu->getId(),
                ])
                    ->setAction($this->generateUrl('delete_menu', ['id' => $menu->getId()]))
                    ->setMethod('POST')
                    ->getForm()
                    ->createView();
            }
        }

        return $this->render('pages/config/menu.html.twig', [
            'menus' => $paginator,
            'totalCount' => $totalCount,
            'totalPages' => $totalPages,
            'offset' => $offset,
            'page' => $page,
            'itemsPerPage' => $itemPerPage,
            'profiles' => $profile,
            'func' => $functionality,
            'params' => $params,
            'createForm' => $createForm->createView(),
            'editForms' => $editForms ?? [],
            'deleteForms' => $deleteForms ?? [],
        ]);
    }

    #[Route(path: '/create/menu', name: 'create_menu', methods: ['POST'])]
    public function createFunctionality(
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $menu = new Menu();
        $form = $this->createForm(MenuFormType::class, $menu);
        $form->handleRequest($request);

        $referer = $request->headers->get('referer');
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($menu);
            $em->flush();
        }

        return $this->redirect($referer);
    }

    #[Route(path: '/config/menu/{id}/edit', name: 'edit_menu')]
    public function editFunctionality(
        Menu $menu,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $form = $this->createForm(MenuFormType::class, $menu, [
            'action' => $this->generateUrl('edit_menu', ['id' => $menu->getId()]),
            'method' => 'POST',
        ]);

        $form->handleRequest($request);
        $referer = $request->headers->get('referer');

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
        }

        return $this->redirect($referer);
    }

    #[Route(path: '/config/menu/{id}/delete', name: 'delete_menu', methods: ['POST'])]
    public function deleteFunctionality(
        Menu $menu,
        Request $request,
        EntityManagerInterface $em
    ): Response {

        if ($this->isCsrfTokenValid('delete' . $menu->getId(), $request->request->get('form')['_token'])) {
            $em->remove($menu);
            $em->flush();
        }

        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }
}
