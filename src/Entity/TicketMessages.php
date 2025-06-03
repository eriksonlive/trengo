<?php

namespace App\Entity;

use App\Repository\TicketMessagesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TicketMessagesRepository::class)]
class TicketMessages
{
    #[ORM\Id]
    #[ORM\Column(type: 'bigint')]
    private ?string $id = null;

    #[ORM\ManyToOne(inversedBy: 'ticketMessages')]
    private ?Tickets $ticket = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $bodyType = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $message = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fileName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fileCaption = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $contact = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isPhone = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $agentName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $agentEmail = null;

    #[ORM\OneToMany(mappedBy: 'message', targetEntity: TicketMessagesAttachments::class, cascade: ['persist', 'remove'])]
    private Collection $ticketMessagesAttachments;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\OneToOne(mappedBy: 'message', cascade: ['persist', 'remove'])]
    private ?TicketEmailMessage $emailMessage = null;

    public function __construct()
    {
        $this->ticketMessagesAttachments = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getTicket(): ?Tickets
    {
        return $this->ticket;
    }

    public function setTicket(?Tickets $ticket): static
    {
        $this->ticket = $ticket;

        return $this;
    }

    public function getBodyType(): ?string
    {
        return $this->bodyType;
    }

    public function setBodyType(?string $bodyType): static
    {
        $this->bodyType = $bodyType;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function setFileName(?string $fileName): static
    {
        $this->fileName = $fileName;

        return $this;
    }

    public function getFileCaption(): ?string
    {
        return $this->fileCaption;
    }

    public function setFileCaption(?string $fileCaption): static
    {
        $this->fileCaption = $fileCaption;

        return $this;
    }

    public function getTicketMessagesAttachments(): Collection
    {
        return $this->ticketMessagesAttachments;
    }

    public function addTicketMessagesAttachment(TicketMessagesAttachments $attachment): static
    {
        if (!$this->ticketMessagesAttachments->contains($attachment)) {
            $this->ticketMessagesAttachments[] = $attachment;
            $attachment->setMessage($this);
        }

        return $this;
    }

    public function removeTicketMessagesAttachment(TicketMessagesAttachments $attachment): static
    {
        if ($this->ticketMessagesAttachments->removeElement($attachment)) {
            if ($attachment->getMessage() === $this) {
                $attachment->setMessage(null);
            }
        }

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getContact(): ?string
    {
        return $this->contact;
    }

    public function setContact(?string $contact): static
    {
        $this->contact = $contact;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function isIsPhone(): ?bool
    {
        return $this->isPhone;
    }

    public function setIsPhone(?bool $isPhone): static
    {
        $this->isPhone = $isPhone;

        return $this;
    }

    public function getAgentName(): ?string
    {
        return $this->agentName;
    }

    public function setAgentName(?string $agentName): static
    {
        $this->agentName = $agentName;

        return $this;
    }

    public function getAgentEmail(): ?string
    {
        return $this->agentEmail;
    }

    public function setAgentEmail(?string $agentEmail): static
    {
        $this->agentEmail = $agentEmail;

        return $this;
    }

    public function getEmailMessage(): ?TicketEmailMessage
    {
        return $this->emailMessage;
    }

    public function setEmailMessage(?TicketEmailMessage $emailMessage): static
    {
        // unset the owning side of the relation if necessary
        if ($emailMessage === null && $this->emailMessage !== null) {
            $this->emailMessage->setMessage(null);
        }

        // set the owning side of the relation if necessary
        if ($emailMessage !== null && $emailMessage->getMessage() !== $this) {
            $emailMessage->setMessage($this);
        }

        $this->emailMessage = $emailMessage;

        return $this;
    }
}
