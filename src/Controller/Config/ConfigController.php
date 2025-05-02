<?php

namespace App\Controller\Config;

use App\Repository\ProfileRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ConfigController extends AbstractController
{
    #[Route(path: '/config', name: 'config')]
    public function index(Request $request, ProfileRepository $profile): Response
    {
        $clean = \array_filter(
            $request->query->all(),
            fn($k) => $k === '' || $k[0] !== '_',
            \ARRAY_FILTER_USE_KEY
        );

        $session = $request->getSession();

        if ($request->query->get('profile_id')) {
            $session->set('profile_id', (int)$request->query->get('profile_id'));
            return $this->redirectToRoute('config');
        }

        $storedProfileId = $session->get('profile_id');

        if ($storedProfileId === null) {
            $storedProfileId = 0;
            $session->set('profile_id', $storedProfileId);
        }

        $profileEntity = null;

        if ($storedProfileId > 0) {
            $profileEntity = $profile->find($storedProfileId);
        }

        if (!$profileEntity) {
            $firstProfile = $profile->findOneBy([], ['id' => 'ASC']);
            if ($firstProfile) {
                $session->set('profile_id', $firstProfile->getId());
            } else {
                $session->set('profile_id', 0);
            }
        }

        $profiles = $profile->findAll();

        return $this->render('pages/config/config.html.twig', [
            'params' => $clean,
            'profiles' => $profiles,
            'profile_id' => $session->get('profile_id'),
        ]);
    }
}
