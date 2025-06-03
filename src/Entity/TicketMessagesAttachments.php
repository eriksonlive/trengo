<?php

namespace App\Entity;

use App\Repository\TicketMessagesAttachmentsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TicketMessagesAttachmentsRepository::class)]
class TicketMessagesAttachments
{
    #[ORM\Id]
    #[ORM\GeneratedValue()]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'ticketMessagesAttachments', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'message_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?TicketMessages $message = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $clientName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $FileName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fullUrl = null;

    #[ORM\Column(nullable: true,)]
    private ?bool $isImage = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $extencion = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getMessage(): ?TicketMessages
    {
        return $this->message;
    }

    public function setMessage(?TicketMessages $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getClientName(): ?string
    {
        return $this->clientName;
    }

    public function setClientName(?string $clientName): static
    {
        $this->clientName = $clientName;

        return $this;
    }

    public function getFileName(): ?string
    {
        return $this->FileName;
    }

    public function setFileName(?string $FileName): static
    {
        $this->FileName = $FileName;

        return $this;
    }

    public function getFullUrl(): ?string
    {
        return $this->fullUrl;
    }

    public function setFullUrl(?string $fullUrl): static
    {
        $this->fullUrl = $fullUrl;

        return $this;
    }

    public function isIsImage(): ?bool
    {
        return $this->isImage;
    }

    public function setIsImage(?bool $isImage): static
    {
        $this->isImage = $isImage;

        return $this;
    }

    public function getExtencion(): ?string
    {
        return $this->extencion;
    }

    public function setExtencion(?string $extencion): static
    {
        $this->extencion = $extencion;

        return $this;
    }
}
