<?php

namespace App\Entity;

use App\Repository\MenuRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MenuRepository::class)]
class Menu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'menus')]
    private ?Profile $profile = null;

    #[ORM\ManyToOne(inversedBy: 'menus')]
    private ?Functionality $functionality = null;

    #[ORM\ManyToOne(targetEntity: Menu::class, inversedBy: 'children')]
    #[ORM\JoinColumn(name: "id_menu_parent", referencedColumnName: "id", onDelete: "SET NULL")]
    private ?Menu $IdMenuParent = null;

    #[ORM\OneToMany(mappedBy: 'IdMenuParent', targetEntity: Menu::class)]
    private Collection $children;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $orden = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $menu = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $language = null;

    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProfile(): ?profile
    {
        return $this->profile;
    }

    public function setProfile(?profile $profile): static
    {
        $this->profile = $profile;

        return $this;
    }

    public function getFunctionality(): ?functionality
    {
        return $this->functionality;
    }

    public function setFunctionality(?functionality $functionality): static
    {
        $this->functionality = $functionality;

        return $this;
    }

    public function getIdMenuParent(): ?Menu
    {
        return $this->IdMenuParent;
    }

    public function setIdMenuParent(?Menu $IdMenuParent): ?static
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

    public function getChildren(): Collection
    {
        return $this->children;
    }
}
