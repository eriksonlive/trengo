<?php

namespace App\Services;

use App\Entity\Tickets;
use App\Repository\TicketsRepository;
use App\Services\TrengoServices;
use Doctrine\ORM\EntityManagerInterface;

class TicketsSyncService
{
    private EntityManagerInterface $em;
    private TicketsRepository $ticketsRepo;
    private TrengoServices $trengo;
    private int $apiPageSize = 25; // Tamaño de lote

    public function __construct(EntityManagerInterface $entityManager, TicketsRepository $ticketsRepo, TrengoServices $trengoServices)
    {
        $this->em = $entityManager;
        $this->ticketsRepo = $ticketsRepo;
        $this->trengo = $trengoServices;
    }

    public function ensureLoaded(int $required): void
    {
        // $labels = 1259169; // omnisalud
        $labels = 1782501; // pruebas
        // $labels = 1645368; // reqpera
        $sort = '-date';

        // 1. Siempre cargar la página 1 primero
        $resp = $this->trengo->request('/tickets', [
            'page'   => 1,
            'labels' => $labels,
            'sort'   => $sort,
        ]);
        $data = $resp['data'] ?? [];
        foreach ($data as $ticketData) {
            $ticketId = $ticketData['id'];
            if (!$this->ticketsRepo->find($ticketId)) {
                $ticket = new Tickets();
                $ticket->setId($ticketId);

                $clientes = array_filter($ticketData['labels'], fn($label) => preg_match('/^cliente/i', $label['slug']));
                $category = array_filter($ticketData['labels'], fn($label) => preg_match('/^categoria/i', $label['slug']));
                $priority = array_filter($ticketData['labels'], fn($label) => preg_match('/^prioridad/i', $label['slug']));
                $state    = array_filter($ticketData['labels'], fn($label) => preg_match('/^estado/i', $label['slug']));

                $ticket->setLabelName(implode(', ', array_map(fn($label) => $label['slug'], $clientes)) ?: 'Sin cliente');
                $ticket->setStatus($ticketData['status']);
                $ticket->setTypeContact($ticketData['contact']['email'] ?? $ticketData['contact']['phone'] ?? '');
                $ticket->setSubject($ticketData['subject'] ?? '');
                $ticket->setCategory(implode(', ', array_map(fn($label) => $label['slug'], $category)) ?: 'Sin categoría');
                $ticket->setPriority(implode(', ', array_map(fn($label) => $label['slug'], $priority)) ?: 'Sin prioridad');
                $ticket->setDetenidoEn(implode(', ', array_map(fn($label) => $label['slug'], $state)));
                $ticket->setCreatedAt(new \DateTimeImmutable($ticketData['created_at']));
                $ticket->setUpdatedAt(new \DateTimeImmutable($ticketData['updated_at']));
                $ticket->setClosedAt(new \DateTimeImmutable($ticketData['closed_at']));
                $ticket->setIdLabel((int) implode(', ', array_map(fn($label) => $label['id'], $clientes)));
                $this->em->persist($ticket);
            }
        }
        $this->em->flush();

        // Cuántos tickets hay por ahora
        $currentCount = $this->ticketsRepo->count([]);

        // Empieza desde la página que sigue a las ya cargadas
        $page = (int) floor($currentCount / $this->apiPageSize);

        $existingById = $this->ticketsRepo->getStatesMap();

        while ($currentCount < $required) {
            $resp = $this->trengo->request('/tickets', [
                'page'   => $page,
                'labels' => $labels,
                'sort'   => $sort,
            ]);
            $data = $resp['data'] ?? [];
            if (empty($data)) {
                // no hay más
                break;
            }
            foreach ($data as $ticketData) {
                $ticketId = $ticketData['id'];
                $status = $ticketData['status'];

                $ticket = $this->ticketsRepo->find($ticketId);
                $isNew  = $ticket === null;
                if ($isNew) {
                    $ticket = new Tickets();
                    $ticket->setId($ticketId);

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
                    $this->em->persist($ticket);
                }
            }
            $this->em->flush();
            $existingById = $this->ticketsRepo->getStatesMap();
            $currentCount = $this->ticketsRepo->count([]);
            $page++;
        }
    }

    public function syncPageUpdates(int $page): void
    {
        $labels = 1259169;
        $sort = '-date';

        // Traigo SOLO esa página
        $resp = $this->trengo->request('/tickets', [
            'page'   => $page,
            'labels' => $labels,
            'sort'   => $sort,
        ]);

        $data = $resp['data'] ?? [];
        foreach ($data as $t) {
            $ticket = $this->ticketsRepo->find($t['id']);
            if ($ticket && $ticket->getStatus() !== $t['status']) {
                $ticket->setStatus($t['status']);
                // … cualquier otro campo que quieras refrescar …
                $this->em->persist($ticket);
            }
        }

        $this->em->flush();
    }

    public function syncTicketUpdates(array $params)
    {
        if (isset($params['event_type']) && $params['event_type'] == 'TICKET_LABEL_ADDED' && strpos($params['label_name'], 'Estado')) {
            $ticket = $this->ticketsRepo->find($params['ticket_id']);

            if (!$ticket) {
                return;
            }

            $ticket->setLabelName($params['label_name']);
            $ticket->setIdLabel($params['label_id']);
            $ticket->setUpdatedAt(new \DateTimeImmutable());

            $this->em->flush();
        }
    }
}
