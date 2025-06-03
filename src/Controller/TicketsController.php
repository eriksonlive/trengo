<?php

namespace App\Controller;

use App\Entity\Tickets;
use App\Services\TicketsSyncService;
use App\Services\TrengoServices;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Json;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class TicketsController extends AbstractController
{
    private LoggerInterface $trengoLogger;

    public function __construct(LoggerInterface $trengoLogger)
    {
        $this->trengoLogger = $trengoLogger;
    }

    #[Route('/ticket', name: 'ticket')]
    public function getAllTickets(
        Request $request,
        EntityManagerInterface $entityManager,
        TrengoServices $trengoServices
    ): JsonResponse {
        $labels = 1779076; // rqpera 1645368, Pruebas 1779076
        $sort = '-date';
        $page = 1;

        $repo = $entityManager->getRepository(Tickets::class);
        $existingTickets = $repo->findAll();
        $existingById = [];
        foreach ($existingTickets as $ticket) {
            $existingById[$ticket->getId()] = $ticket;
        }

        // Traer todos los tickets por páginas
        do {
            $queryParams = [
                'page' => $page,
                'labels' => $labels,
                'sort' => $sort,
            ];

            $response = $trengoServices->request('/tickets', $queryParams);
            $ticketsData = $response['data'] ?? [];

            foreach ($ticketsData as $ticketData) {
                $ticketId = $ticketData['id'];
                $status = $ticketData['status'];

                // Verifica si ya existe en la base y si el estado cambió
                $shouldInsert = false;
                if (!isset($existingById[$ticketId])) {
                    $shouldInsert = true;
                    $ticket = new Tickets();
                    $ticket->setId($ticketId);
                } else {
                    $ticket = $existingById[$ticketId];
                    if ($ticket->getStatus() !== $status) {
                        $shouldInsert = true;
                    }
                }

                if ($shouldInsert) {
                    $clientes = array_filter($ticketData['labels'], fn($label) => preg_match('/^cliente/i', $label['slug']));
                    $category = array_filter($ticketData['labels'], fn($label) => preg_match('/^categoria/i', $label['slug']));
                    $priority = array_filter($ticketData['labels'], fn($label) => preg_match('/^prioridad/i', $label['slug']));
                    $state = array_filter($ticketData['labels'], fn($label) => preg_match('/^estado/i', $label['slug']));

                    $ticket->setLabelName(implode(', ', array_map(fn($label) => $label['slug'], $clientes)) ?: 'Sin cliente');
                    $ticket->setStatus($status);
                    $ticket->setTypeContact($ticketData['contact']['email'] ?? $ticketData['contact']['phone'] ?? '');
                    $ticket->setSubject($ticketData['subject'] ?? '');
                    $ticket->setCategory(implode(', ', array_map(fn($label) => $label['slug'], $category)) ?: 'Sin categoría');
                    $ticket->setPriority(implode(', ', array_map(fn($label) => $label['slug'], $priority)) ?: 'Sin prioridad');
                    $ticket->setDetenidoEn(implode(', ', array_map(fn($label) => $label['slug'], $state)));
                    $ticket->setCreatedAt(new \DateTimeImmutable($ticketData['created_at']));
                    $ticket->setUpdatedAt(new \DateTimeImmutable($ticketData['updated_at']));
                    $ticket->setClosedAt(new \DateTimeImmutable($ticketData['closed_at']));
                    $ticket->setIdLabel((int) implode(', ', array_map(fn($label) => $label['id'], $clientes)));
                    $entityManager->persist($ticket);
                }
            }

            $page++; // Pasamos a la siguiente página
        } while (!empty($response['links']['next'])); // Mientras haya más páginas

        $entityManager->flush();

        return JsonResponse::fromJsonString(
            json_encode([
                'status' => 'success',
                'message' => 'Tickets procesados correctamente',
            ])
        );
    }

    #[Route('/webhook/trengo', name: 'webhook_trengo', methods: ['POST'])]
    public function webhookTrengo(Request $request, TicketsSyncService $ticketSync): Response
    {
        $content = $request->getContent();
        parse_str($content, $data);

        $signature = $request->headers->get('X-Trengo-Signature');
        $secret = $_ENV['TRENGO_WEBHOOK_SECRET'] ?? null;

        if ($secret && $signature) {
            $expected = hash_hmac('sha256', $content, $secret);

            if (!hash_equals($expected, $signature)) {
                $this->trengoLogger->warning('Firma de Trengo inválida', [
                    'expected' => $expected,
                    'received' => $signature,
                ]);

                return new Response('Firma inválida', Response::HTTP_FORBIDDEN);
            }
        }

        // Guardar en log para debug
        file_put_contents(__DIR__ . '/../../var/log/trengo_webhook.log', print_r($data, true), FILE_APPEND);
        $this->trengoLogger->info('Webhook de Trengo recibido', [
            'data' => $data,
        ]);

        $ticketSync->syncTicketUpdates($data);

        // TODO: guardar en base de datos
        return new Response('Webhook recibido', Response::HTTP_OK);
    }

    private function extractPageNumber(string $url): ?int
    {
        $parsed = parse_url($url);
        if (!isset($parsed['query'])) {
            return null;
        }

        parse_str($parsed['query'], $queryParams);
        return $queryParams['page'] ?? null;
    }

    private function getAllTrengoTickets(TrengoServices $trengoServices, array $baseParams): array
    {
        $allTickets = [];
        $page = 1;

        do {
            $queryParams = array_merge($baseParams, ['page' => $page]);
            $response = $trengoServices->request('/tickets', $queryParams);
            $data = $response['data'] ?? [];
            $allTickets = array_merge($allTickets, $data);

            $next = $response['links']['next'] ?? null;
            $page++;
        } while ($next); // Si existe 'next', seguir paginando

        return $allTickets;
    }
}
