<?php

namespace App\Services;

use App\Entity\TicketMessagesAttachments;
use App\Repository\TicketMessagesAttachmentsRepository;
use App\Repository\TicketMessagesRepository;
use Doctrine\ORM\EntityManagerInterface;

class MessageViewServices
{
    public TrengoServices $trengo;
    public TicketMessagesRepository $ticketMessagesRepo;
    public TicketMessagesAttachmentsRepository $tmaRepo;
    public EntityManagerInterface $em;

    public function __construct(TrengoServices $trengo, TicketMessagesRepository $ticketMessagesRepo, TicketMessagesAttachmentsRepository $tmaRepo, EntityManagerInterface $em)
    {
        $this->trengo = $trengo;
        $this->tmaRepo = $tmaRepo;
        $this->ticketMessagesRepo = $ticketMessagesRepo;
        $this->em = $em;
    }

    public function ensureLoaded(int $ticketId, int $messageId)
    {
        $resp = $this->trengo->request('/tickets/' . $ticketId . '/messages/' . $messageId) ?? [];

        $data = $resp ?? [];

        $ticketMessage = $this->ticketMessagesRepo->find($data['id']);

        dump($data);

        $messageViewId = $data['id'];
        $message = $this->tmaRepo->find($messageViewId);
        $isNew = $message === null;
        if ($isNew) {
            $messageAttachment = new TicketMessagesAttachments();
            $messageAttachment->setId($messageViewId);
            $messageAttachment->setMessage($ticketMessage);
            $messageAttachment->setClientName($data['contact']['name']);
            $messageAttachment->setFileName($data['attachments'][0]['file_name'] ?? null);
            $messageAttachment->setFullUrl($data['attachments'][0]['full_url'] ?? null);
            $messageAttachment->setIsImage($data['attachments'][0]['is_image'] ?? null);
            $messageAttachment->setExtencion($data['attachments'][0]['extension'] ?? null);

            // 👇 Esta línea es clave para mantener la relación en ambos lados
            // $ticketMessage->setTicketMessagesAttachments($messageAttachment);

            $this->em->persist($messageAttachment);
        }

        $this->em->flush();

        return $resp;
    }
}
