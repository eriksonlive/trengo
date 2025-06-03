<?php

namespace App\Services;

use App\Entity\TicketEmailMessage;
use App\Entity\TicketMessages;
use App\Entity\TicketMessagesAttachments;
use App\Repository\TicketMessagesRepository;
use App\Repository\TicketsRepository;
use Doctrine\ORM\EntityManagerInterface;

class MessagesTicketsServices
{
    private EntityManagerInterface $em;
    private TicketMessagesRepository $ticketMessagesRepo;
    private TrengoServices $trengo;
    private TicketsRepository $ticket;
    private int $apiPageSize = 25; // Tamaño de lote

    public function __construct(EntityManagerInterface $entityManager, TicketMessagesRepository $ticketMessagesRepo, TrengoServices $trengoServices, TicketsRepository $ticket)
    {
        $this->em = $entityManager;
        $this->ticketMessagesRepo = $ticketMessagesRepo;
        $this->trengo = $trengoServices;
        $this->ticket = $ticket;
    }

    public function ensureLoaded(String $ticketId)
    {
        $resp = $this->trengo->request('/tickets/' . $ticketId . '/messages') ?? [];

        $data = $resp['data'] ?? [];

        dump($data);

        foreach ($data as $messageData) {
            $messageId = $messageData['id'];
            $ticketMessage = $this->ticketMessagesRepo->find($messageId);
            $isNew = $ticketMessage === null;
            if ($isNew) {
                $ticketMessage = new TicketMessages();
                $ticketMessage->setId($messageId);
                $ticketMessage->setTicket($this->ticket->find($messageData['ticket_id']));
                $ticketMessage->setBodyType($messageData['body_type'] ?? '');
                $ticketMessage->setMessage($messageData['message'] ?? '');
                $ticketMessage->setFileName($messageData['file_name'] ?? '');
                $ticketMessage->setFileCaption($messageData['file_caption'] ?? '');
                $ticketMessage->setContact($messageData['contact']['name']);
                $ticketMessage->setEmail($messageData['contact']['email'] ?? '');
                $ticketMessage->setPhone($messageData['contact']['phone'] ?? '');
                $ticketMessage->setIsPhone($messageData['contact']['is_phone'] ?? '');
                $ticketMessage->setAgentEmail($messageData['agent']['email'] ?? '');
                $ticketMessage->setAgentName($messageData['agent']['name'] ?? '');
                $ticketMessage->setCreatedAt(new \DateTimeImmutable($messageData['created_at']));
                $ticketMessage->setUpdatedAt(new \DateTimeImmutable($messageData['created_at']));

                if (count($messageData['attachments']) > 0) {
                    foreach ($messageData['attachments'] as $cl => $vl) {
                        $messageAttachment = new TicketMessagesAttachments();
                        $messageAttachment->setMessage($ticketMessage);
                        $messageAttachment->setFileName($vl['file_name'] ?? null);
                        $messageAttachment->setFullUrl($vl['full_url'] ?? null);
                        $messageAttachment->setIsImage($vl['is_image'] ?? null);
                        $messageAttachment->setExtencion($vl['extension'] ?? null);

                        $this->em->persist($messageAttachment);
                    }
                }

                if (count($messageData['email_message']) > 0 && !$messageData['email_message']['collapsed'] == true) {
                    $messageEmail = new TicketEmailMessage();
                    $messageEmail->setMessage($ticketMessage);
                    $messageEmail->setHtml($messageData['email_message']['html'] ?? null);
                    $messageEmail->setEmailTo($messageData['email_message']['to'] ?? null);
                    $messageEmail->setEmailcc($messageData['email_message']['cc'] ?? null);
                    $messageEmail->setEmailFrom($messageData['email_message']['from'] ?? null);
                    $messageEmail->setEmailSubject($messageData['email_message']['subject'] ?? null);
                    $messageEmail->setCreatedAt(new \DateTimeImmutable($messageData['email_message']['created_at']));
                    $messageEmail->setUpdatedAt(new \DateTimeImmutable($messageData['email_message']['updated_at']));

                    $ticketMessage->setEmailMessage($messageEmail);

                    $this->em->persist($messageEmail);
                }

                $this->em->persist($ticketMessage);
            }
        }

        $this->em->flush();

        return $data;
    }
}
