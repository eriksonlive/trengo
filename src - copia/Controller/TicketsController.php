<?php

namespace App\Controller;

use App\Entity\Tickets;
use App\Services\TrengoServices;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class TicketsController extends AbstractController
{
    #[Route('/ticks/example', name: 'ticks')]
    public function index(
        Request $request,
        EntityManagerInterface $entityManager,
        TrengoServices $trengoServices
    ): Response {
        $labels = 1645368;
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

        return $this->render('index.html.twig', [
            'message' => 'Tickets actualizados correctamente',
        ]);
    }

    #[Route('/webhook/trengo', name: 'webhook_trengo', methods: ['POST', 'GET'])]
    public function webhookTrengo(Request $request, LoggerInterface $logger): Response
    {
        $content = $request->getContent();
        $data = json_decode($content, true);

        file_put_contents(__DIR__ . '/../../var/log/trengo_webhook.log', print_r($data, true), FILE_APPEND);
        // $logger->info('Webhook Trengo recibido', $data);
        // Aquí puedes manejar la lógica del webhook de Trengo
        // Por ejemplo, guardar los datos en la base de datos o procesarlos de alguna manera

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
