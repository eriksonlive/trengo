<?php

namespace App\Controller;

use App\Entity\Tickets;
use App\Repository\TicketsRepository;
use App\Services\TrengoServices;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RoutesController extends AbstractController
{

    private TrengoServices $trengoServices;

    public function __construct(TrengoServices $trengoServices)
    {
        $this->trengoServices = $trengoServices;
    }

    #[Route('/', name: 'index')]
    public function fetchData(Request $request, TicketsRepository $tickets)
    {

        $itemsPerPage = 10;
        $page = max(1, $request->query->getInt('page', 1));
        $offset = ($page - 1) * $itemsPerPage;

        dump($request->query->all());

        $paginator = $tickets->getTicketsPaginator($offset, $itemsPerPage, $request->query->all());

        $totalCount = count($paginator);
        $totalPages = (int) ceil($totalCount / $itemsPerPage);

        return $this->render('index.html.twig', [
            'tickets' => $paginator,
            'totalCount' => $totalCount,
            'totalPages' => $totalPages,
            'offset' => $offset,
            'page' => $page,
            'itemsPerPage' => $itemsPerPage,
            "filters" => $request->query->all(),
        ]);
    }

    #[Route('/tickets', name: 'tickets', methods: ['GET'])]
    public function fetchTickets(Request $request): JsonResponse
    {

        print_r($request->query->all());

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
