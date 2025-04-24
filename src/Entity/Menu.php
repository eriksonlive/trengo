<?php

namespace App\Entity;

use App\Repository\MenuRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MenuRepository::class)]
class Menu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'menus')]
    private ?Profile $idProfile = null;

    #[ORM\ManyToOne(inversedBy: 'menus')]
    private ?Functionality $idFunctionality = null;

    #[ORM\Column(nullable: true)]
    private ?int $IdMenuParent = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $orden = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $menu = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $language = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdProfile(): ?profile
    {
        return $this->idProfile;
    }

    public function setIdProfile(?profile $idProfile): static
    {
        $this->idProfile = $idProfile;

        return $this;
    }

    public function getIdFunctionality(): ?functionality
    {
        return $this->idFunctionality;
    }

    public function setIdFunctionality(?functionality $idFunctionality): static
    {
        $this->idFunctionality = $idFunctionality;

        return $this;
    }

    public function getIdMenuParent(): ?int
    {
        return $this->IdMenuParent;
    }

    public function setIdMenuParent(?int $IdMenuParent): static
    {
        $this->IdMenuParent = $IdMenuParent;

        return $this;
    }

    public function getOrden(): ?string
    {
        return $this->orden;
    }

    public function setOrden(?string $orden): static
    {
        $this->orden = $orden;

        return $this;
    }

    public function getMenu(): ?string
    {
        return $this->menu;
    }

    public function setMenu(?string $menu): static
    {
        $this->menu = $menu;

        return $this;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setLanguage(?string $language): static
    {
        $this->language = $language;

        return $this;
    }
}
