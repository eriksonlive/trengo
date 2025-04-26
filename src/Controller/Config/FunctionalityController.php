<?php

namespace App\Controller\Config;

use App\Entity\Functionality;
use App\Form\CreateFunctionalityType;
use App\Repository\FunctionalityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FunctionalityController extends AbstractController
{
    #[Route(path: '/config/functionality', name: 'config_functionality')]
    public function index(
        Request $request,
        FunctionalityRepository $funcRepo
    ): Response {
        $params = array_filter(
            $request->attributes->all(),
            fn($k) => 0 !== strpos($k, '_'),
            \ARRAY_FILTER_USE_KEY
        );

        $itemPerPage = 5;
        $page = max(1, $request->attributes->get('page_func', 1));
        $offset = ($page - 1) * $itemPerPage;
        $paginator   = $funcRepo->getFuncPaginator($offset, $itemPerPage, $request->query->all());
        $totalCount  = count($paginator);
        $totalPages  = (int) ceil($totalCount / $itemPerPage);

        // — FORMULARIO de CREACIÓN —
        $newFunc = new Functionality();
        $createForm = $this->createForm(CreateFunctionalityType::class, $newFunc, [
            'action' => $this->generateUrl('create_functionality'),
            'method' => 'POST',
        ]);

        foreach ($paginator as $func) {
            $editForms[$func->getId()] = $this->createForm(CreateFunctionalityType::class, $func, [
                'action' => $this->generateUrl('edit_functionality', ['id' => $func->getId()]),
                'method' => 'POST',
            ])->createView();
        }

        foreach ($paginator as $func) {
            $deleteForms[$func->getId()] = $this->createFormBuilder(null, [
                'csrf_protection' => true,
                'csrf_field_name' => '_token',
                'csrf_token_id'   => 'delete' . $func->getId(),
            ])
                ->setAction($this->generateUrl('delete_functionality', ['id' => $func->getId()]))
                ->setMethod('POST')
                ->getForm()
                ->createView();
        }

        return $this->render('pages/config/functionality.html.twig', [
            'functionality' => $paginator,
            'totalCount'    => $totalCount,
            'totalPages'    => $totalPages,
            'offset'        => $offset,
            'page'          => $page,
            'itemsPerPage'  => $itemPerPage,
            'params'        => $params,
            'createForm'    => $createForm->createView(),
            'editForms'     => $editForms,
            'deleteForms'   => $deleteForms,
        ]);
    }

    #[Route(path: '/create/functionality', name: 'create_functionality', methods: ['POST'])]
    public function createFunctionality(
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $func = new Functionality();
        $form = $this->createForm(CreateFunctionalityType::class, $func);
        $form->handleRequest($request);

        $referer = $request->headers->get('referer');
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($func);
            $em->flush();
        }

        return $this->redirect($referer);
    }

    #[Route(path: '/config/functionality/{id}/edit', name: 'edit_functionality')]
    public function editFunctionality(
        Functionality $func,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $form = $this->createForm(CreateFunctionalityType::class, $func, [
            'action' => $this->generateUrl('edit_functionality', ['id' => $func->getId()]),
            'method' => 'POST',
        ]);

        $form->handleRequest($request);
        $referer = $request->headers->get('referer');

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
        }

        return $this->redirect($referer);
    }

    #[Route(path: '/config/functionality/{id}/delete', name: 'delete_functionality', methods: ['POST'])]
    public function deleteFunctionality(
        Functionality $func,
        Request $request,
        EntityManagerInterface $em
    ): Response {

        if ($this->isCsrfTokenValid('delete' . $func->getId(), $request->request->get('form')['_token'])) {
            $em->remove($func);
            $em->flush();
        }

        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }
}
