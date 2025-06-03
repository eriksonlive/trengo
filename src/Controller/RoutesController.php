<?php

namespace App\Controller;

use App\Entity\TicketMessages;
use App\Repository\TicketMessagesAttachmentsRepository;
use App\Repository\TicketMessagesRepository;
use App\Repository\TicketsRepository;
use App\Services\MessagesTicketsServices;
use App\Services\MessageViewServices;
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
    private MessagesTicketsServices $messagesTikets;
    private MessageViewServices $messageView;

    public function __construct(TrengoServices $trengoServices, TicketsSyncService $ticketSync, MessagesTicketsServices $messagesTikets, MessageViewServices $messageView)
    {
        $this->messagesTikets = $messagesTikets;
        $this->trengoServices = $trengoServices;
        $this->ticketSync = $ticketSync;
        $this->messageView = $messageView;
    }

    #[Route('/tickets', name: 'tickets')]
    public function index(
        Request $request,
        TicketsRepository $ticketsRepo
    ) {
        $itemsPerPage = 10;
        $page = max(1, $request->query->getInt('page', 1));

        // 1) Cuántos necesito para esta página
        $needed = $itemsPerPage * $page + 1;

        // 2) Cargo solo hasta esa cantidad
        $this->ticketSync->ensureLoaded($needed);

        // 3) Actualizo **solo** la página actual
        $this->ticketSync->syncPageUpdates($page);

        $params = array_merge($request->query->all(), ['label' => 1782501]);

        // 4) Finalmente leo de la BD local
        $offset = ($page - 1) * $itemsPerPage;
        $paginator = $ticketsRepo->getTicketsPaginator($offset, $itemsPerPage, $params);

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
    public function fetchTickets(Request $request, TicketMessagesRepository $tmr)
    {
        $ticket_id = $request->attributes->get('id');

        $this->messagesTikets->ensureLoaded($ticket_id);

        $messageRepo = $tmr->findBy(['ticket' => $ticket_id], ['id' => 'ASC']);

        return $this->render('/pages/trengo/allmessages.html.twig', [
            'messages' => $messageRepo,
        ]);
    }

    #[Route('/message/{message_id}', name: 'message')]
    public function fetchMessage(Request $request, TicketMessagesRepository $ticketMessages)
    {
        $message_id = $request->attributes->get('message_id');

        $data = $ticketMessages->find($message_id);

        $grouped = [
            'images' => [],
            'videos' => [],
            'audio' => [],
            'documents' => [],
            'others' => [],
        ];

        dump($data);

        foreach ($data->getTicketMessagesAttachments() as $att) {
            $ext = strtolower($att->getExtencion());
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                $grouped['images'][] = $att;
            } elseif (in_array($ext, ['mp4', 'webm', 'wmp'])) {
                $grouped['videos'][] = $att;
            } elseif (in_array($ext, ['mp3', 'wav', 'aac', 'ogg'])) {
                $grouped['audio'][] = $att;
            } elseif (in_array($ext, ['pdf', 'docx', 'xml', 'xlsx'])) {
                $grouped['documents'][] = $att;
            } else {
                $grouped['others'][] = $att;
            }
        }

        dump($grouped);

        return $this->render('/pages/trengo/message.html.twig', [
            'message' => $data ? $data : [],
            'attachments' => $grouped
        ]);
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
