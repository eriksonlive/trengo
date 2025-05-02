<?php

namespace App\Entity;

use App\Repository\FunctionalityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FunctionalityRepository::class)]
class Functionality
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $router = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $varGet = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $varPost = null;

    #[ORM\OneToMany(targetEntity: Menu::class, mappedBy: 'functionality')]
    private Collection $menus;

    public function __construct()
    {
        $this->menus = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getRouter(): ?string
    {
        return $this->router;
    }

    public function setRouter(?string $router): static
    {
        $this->router = $router;

        return $this;
    }

    public function getVarGet(): ?string
    {
        return $this->varGet;
    }

    public function setVarGet(?string $varGet): static
    {
        $this->varGet = $varGet;

        return $this;
    }

    public function getVarPost(): ?string
    {
        return $this->varPost;
    }

    public function setVarPost(?string $varPost): static
    {
        $this->varPost = $varPost;

        return $this;
    }

    /**
     * @return Collection<int, Menu>
     */
    public function getMenus(): Collection
    {
        return $this->menus;
    }

    public function addMenu(Menu $menu): static
    {
        if (!$this->menus->contains($menu)) {
            $this->menus->add($menu);
            $menu->setFunctionality($this);
        }

        return $this;
    }

    public function removeMenu(Menu $menu): static
    {
        if ($this->menus->removeElement($menu)) {
            // set the owning side to null (unless already changed)
            if ($menu->getFunctionality() === $this) {
                $menu->setFunctionality(null);
            }
        }

        return $this;
    }
}
