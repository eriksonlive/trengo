<?php

namespace App\Services;

use App\Entity\Tickets;
use App\Services\TrengoServices;
use Doctrine\ORM\EntityManagerInterface;

class TicketsSyncService
{
    private EntityManagerInterface $entityManager;
    private TrengoServices $trengoServices;
    private int $batchSize = 30; // Tamaño de lote

    public function __construct(EntityManagerInterface $entityManager, TrengoServices $trengoServices)
    {
        $this->entityManager = $entityManager;
        $this->trengoServices = $trengoServices;
    }

    public function syncTicketsexample(): void
    {
        $labels = 1645368;
        // $labels = 1259169;
        $sort = '-date';
        $page = 1;

        $repo = $this->entityManager->getRepository(Tickets::class);
        $existingTickets = $repo->findAll();
        $existingById = [];
        foreach ($existingTickets as $ticket) {
            $existingById[$ticket->getId()] = $ticket;
        }

        do {
            $queryParams = [
                'page' => $page,
                'labels' => $labels,
                'sort' => $sort,
            ];

            $response = $this->trengoServices->request('/tickets', $queryParams);
            $ticketsData = $response['data'] ?? [];

            foreach ($ticketsData as $ticketData) {
                $ticketId = $ticketData['id'];
                $status = $ticketData['status'];

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
                    $this->entityManager->persist($ticket);
                }
            }

            $page++;
        } while (!empty($response['links']['next']));

        $this->entityManager->flush();
    }

    public function syncTickets(): void
    {
        // Parámetros iniciales
        $labels = 1259169;
        $sort   = '-date';
        $page   = 1;

        // 1) Carga inicial de IDs y estados de tickets existentes como array simple
        $existingById = $this->loadExistingTicketStates();

        $processed = 0;

        do {
            // 2) Trae página de tickets desde Trengo
            $response     = $this->trengoServices->request('/tickets', [
                'page'   => $page,
                'labels' => $labels,
                'sort'   => $sort,
            ]);
            $ticketsData = $response['data'] ?? [];

            foreach ($ticketsData as $ticketData) {
                $ticketId = $ticketData['id'];
                $status   = $ticketData['status'];

                // 3) Decide si debemos crear o actualizar
                $ticket = $this->entityManager->find(Tickets::class, $ticketId);
                $isNew  = $ticket === null;

                if ($isNew || $ticket->getStatus() !== $status) {
                    if ($isNew) {
                        $ticket = new Tickets();
                        $ticket->setId($ticketId);
                    }

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

                    $this->entityManager->persist($ticket);
                    $processed++;
                }

                // 4) Flush + clear cada batchSize
                if ($processed > 0 && $processed % $this->batchSize === 0) {
                    $this->entityManager->flush();
                    // Clear sólo la entidad Tickets para no perder otras referencias
                    $this->entityManager->clear(Tickets::class);
                    // Reconstruye el índice de existentes
                    $existingById = $this->loadExistingTicketStates();
                }
            }

            $page++;
        } while (!empty($response['links']['next']));

        // 5) Flush final de cualquier resto
        $this->entityManager->flush();
    }

    private function loadExistingTicketStates(): array
    {
        $rows = $this->entityManager
            ->createQueryBuilder()
            ->select('t.id, t.status')
            ->from(Tickets::class, 't')
            ->getQuery()
            ->getArrayResult();

        $map = [];
        foreach ($rows as $r) {
            $map[(int)$r['id']] = $r['status'];
        }
        return $map;
    }
}
