<?php

namespace App\Controller;

use App\Repository\TicketsRepository;
use App\Services\TicketsSyncService;
use App\Services\TrengoServices;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RoutesController extends AbstractController
{

    private TrengoServices $trengoServices;
    private TicketsSyncService $ticketSync;

    public function __construct(TrengoServices $trengoServices, TicketsSyncService $ticketSync)
    {
        $this->trengoServices = $trengoServices;
        $this->ticketSync = $ticketSync;
    }

    #[Route('/tickets', name: 'tickets')]
    public function index(
        Request $request,
        TicketsRepository $ticketsRepo
    ) {
        $itemsPerPage = 10;
        $page = max(1, $request->query->getInt('page', 1));

        // 1) Cuántos necesito para esta página
        $needed = $itemsPerPage * $page;

        // 2) Cargo solo hasta esa cantidad
        $this->ticketSync->ensureLoaded($needed);

        // 3) Actualizo **solo** la página actual
        $this->ticketSync->syncPageUpdates($page);

        // 4) Finalmente leo de la BD local
        $offset = ($page - 1) * $itemsPerPage;
        $paginator = $ticketsRepo->getTicketsPaginator($offset, $itemsPerPage, $request->query->all());

        $totalCount = count($paginator);
        $totalPages = (int) ceil($totalCount / $itemsPerPage);

        return $this->render('pages/trengo/tickets.html.twig', [
            'tickets' => $paginator,
            'totalCount' => $totalCount,
            'totalPages' => $totalPages,
            'offset' => $offset,
            'page' => $page,
            'itemsPerPage' => $itemsPerPage,
            'filters' => [
                'status' => $request->query->get('status'),
                'date_start' => $request->query->get('date_start'),
                'date_end' => $request->query->get('date_end'),
                'search' => $request->query->get('search'),
            ],
        ]);
    }

    #[Route('/allmessage/{id}', name: 'all_messages')]
    public function fetchTickets(Request $request)
    {
        $ticket_id = $request->attributes->get('id');

        $data = $this->trengoServices->request('/tickets/' . $ticket_id . '/messages');

        // dump($data['data']);

        return $this->render('/pages/trengo/allmessages.html.twig', [
            'messages' => $data ? $data['data'] : [],
        ]);
    }

    #[Route('/message/{ticket_id}/{message_id}', name: 'message')]
    public function fetchMessage(Request $request)
    {
        $ticket_id = $request->attributes->get('ticket_id'); // Obtener el ID del ticket desde la URL
        $message_id = $request->attributes->get('message_id'); // Obtener el ID del ticket desde la URL

        $data = $this->trengoServices->request('/tickets/' . $ticket_id . '/messages/' . $message_id);

        // dump($data);

        return $this->render('/pages/trengo/message.html.twig', [
            'message' => $data ? $data : [],
        ]);
    }

    #[Route('tickets/{ticket_id}/messages', name: 'ticket', methods: ['GET'])]
    public function fetchTicket(Request $request, int $ticket_id): JsonResponse
    {
        $data = $this->trengoServices->request('/tickets/' . $ticket_id . '/messages');

        // dump($data);

        return new JsonResponse($data);
    }

    #[Route('tickets/{ticket_id}/messages/{message_id}', name: 'messages', methods: ['GET'])]
    public function fetchMessages(Request $request, int $ticket_id, int $message_id): JsonResponse
    {
        $data = $this->trengoServices->request('/tickets/' . $ticket_id . '/messages/' . $message_id);

        return new JsonResponse($data);
    }
}
