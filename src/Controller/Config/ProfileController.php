<?php

namespace App\Controller\Config;

use App\Entity\Profile;
use App\Form\CreateProfileType;
use App\Repository\ProfileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    #[Route(path: '/config/profile', name: 'config_profile')]
    public function index(Request $request, ProfileRepository $profileRepo): Response
    {
        $params = array_filter(
            $request->attributes->all(),
            fn($k) => 0 !== strpos($k, '_'),
            \ARRAY_FILTER_USE_KEY
        );

        $itemPerPage = 5;
        $page = max(1, $request->attributes->get('page_profile', 1));
        $offset = ($page - 1) * $itemPerPage;
        $paginator = $profileRepo->getProfilePaginator($offset, $itemPerPage, $request->query->all());
        $totalCount = count($paginator);
        $totalPages = (int) ceil($totalCount / $itemPerPage);

        $newFunc = new Profile();
        $createForm = $this->createForm(CreateProfileType::class, $newFunc, [
            'action' => $this->generateUrl('create_profile'),
            'method' => 'POST',
        ]);

        if(!empty($paginator)) {

            foreach ($paginator as $func) {
                $editForms[$func->getId()] = $this->createForm(CreateProfileType::class, $func, [
                    'action' => $this->generateUrl('edit_profile', ['id' => $func->getId()]),
                    'method' => 'POST',
                ])->createView();
            }
    
            foreach ($paginator as $func) {
                $deleteForms[$func->getId()] = $this->createFormBuilder(null, [
                    'csrf_protection' => true,
                    'csrf_field_name' => '_token',
                    'csrf_token_id'   => 'delete' . $func->getId(),
                ])
                    ->setAction($this->generateUrl('delete_profile', ['id' => $func->getId()]))
                    ->setMethod('POST')
                    ->getForm()
                    ->createView();
            }
        }

        return $this->render('pages/config/profile.html.twig', [
            'profiles' => $paginator,
            'totalCount' => $totalCount,
            'totalPages' => $totalPages,
            'offset' => $offset,
            'page' => $page,
            'itemsPerPage' => $itemPerPage,
            'params' => $params,
            'createForm' => $createForm->createView(),
            'editForms' => $editForms ?? [],
            'deleteForms' => $deleteForms ?? [],
        ]);
    }

    #[Route(path: '/create/profile', name: 'create_profile', methods: ['POST'])]
    public function createFunctionality(
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $profile = new Profile();
        $form = $this->createForm(CreateProfileType::class, $profile);
        $form->handleRequest($request);

        $referer = $request->headers->get('referer');
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($profile);
            $em->flush();
        }

        return $this->redirect($referer);
    }

    #[Route(path: '/config/profile/{id}/edit', name: 'edit_profile')]
    public function editFunctionality(
        Profile $profile,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $form = $this->createForm(CreateProfileType::class, $profile, [
            'action' => $this->generateUrl('edit_profile', ['id' => $profile->getId()]),
            'method' => 'POST',
        ]);

        $form->handleRequest($request);
        $referer = $request->headers->get('referer');

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
        }

        return $this->redirect($referer);
    }

    #[Route(path: '/config/profile/{id}/delete', name: 'delete_profile', methods: ['POST'])]
    public function deleteFunctionality(
        Profile $profile,
        Request $request,
        EntityManagerInterface $em
    ): Response {

        if ($this->isCsrfTokenValid('delete' . $profile->getId(), $request->request->get('form')['_token'])) {
            $em->remove($profile);
            $em->flush();
        }

        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }
}
