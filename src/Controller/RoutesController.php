<?php

namespace App\Controller;

use App\Services\TrengoServices;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Json;

class RoutesController extends AbstractController
{

    private TrengoServices $trengoServices;

    public function __construct(TrengoServices $trengoServices)
    {
        $this->trengoServices = $trengoServices;
    }

    #[Route('/', name: 'index')]
    public function fetchData(Request $request)
    {
        $ticketId = 834510454; // Reemplaza con un ID válido
        // Obtener parámetros desde la URL
        $page = $request->query->get('page', 1); // Valor por defecto: 1
        $labels = 1645368; // Array de etiquetas
        $sort = $request->query->get('sort', '-date'); // Valor por defecto: -date

        // Construir los parámetros
        $queryParams = [
            'page' => $page,
            'labels' => $labels,
            'sort' => $sort,
        ];

        $data = $this->trengoServices->request('/tickets', $queryParams);
        // $data = $this->trengoServices->request('/tickets/' . $ticketId . '/messages');

        // echo '<pre>';
        dump($data);
        // echo '</pre>';

        return $this->render('index.html.twig', [
            'tickets' => $data
        ]);
    }

    #[Route('/tickets', name: 'tickets', methods: ['GET'])]
    public function fetchTickets(Request $request): JsonResponse
    {

        // Obtener parámetros desde la URL
        $page = $request->query->get('page', 1); // Valor por defecto: 1
        $labels = $request->query->all('labels', 1645368); // Array de etiquetas
        $sort = $request->query->get('sort', '-date'); // Valor por defecto: -date

        // Construir los parámetros
        $queryParams = [
            'page' => $page,
            'labels' => $labels,
            'sort' => $sort,
        ];

        $data = $this->trengoServices->request('/tickets', $queryParams);

        return new JsonResponse($data);
    }

    #[Route('tickets/{ticket_id}/messages', name: 'ticket', methods: ['GET'])]
    public function fetchTicket(Request $request, int $ticket_id): JsonResponse
    {
        $data = $this->trengoServices->request('/tickets/' . $ticket_id . '/messages');

        dump($data);

        return new JsonResponse($data);
    }

    #[Route('tickets/{ticket_id}/messages/{message_id}', name: 'messages', methods: ['GET'])]
    public function fetchMessages(Request $request, int $ticket_id, int $message_id): JsonResponse
    {
        $data = $this->trengoServices->request('/tickets/' . $ticket_id . '/messages/' . $message_id);

        return new JsonResponse($data);
    }
}
