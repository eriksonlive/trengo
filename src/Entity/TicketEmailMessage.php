<?php

namespace App\Entity;

use App\Repository\TicketEmailMessageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TicketEmailMessageRepository::class)]
class TicketEmailMessage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'emailMessage', cascade: ['persist', 'remove'])]
    private ?TicketMessages $message = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $html = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $emailTo = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $emailFrom = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $emailcc = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $emailSubject = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getHtml(): ?string
    {
        return $this->html;
    }

    public function setHtml(?string $html): static
    {
        $this->html = $html;

        return $this;
    }

    public function getEmailTo(): ?string
    {
        return $this->emailTo;
    }

    public function setEmailTo(?string $emailTo): static
    {
        $this->emailTo = $emailTo;

        return $this;
    }

    public function getEmailFrom(): ?string
    {
        return $this->emailFrom;
    }

    public function setEmailFrom(?string $emailFrom): static
    {
        $this->emailFrom = $emailFrom;

        return $this;
    }

    public function getEmailcc(): ?string
    {
        return $this->emailcc;
    }

    public function setEmailcc(?string $emailcc): static
    {
        $this->emailcc = $emailcc;

        return $this;
    }

    public function getEmailSubject(): ?string
    {
        return $this->emailSubject;
    }

    public function setEmailSubject(?string $emailSubject): static
    {
        $this->emailSubject = $emailSubject;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
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
}
