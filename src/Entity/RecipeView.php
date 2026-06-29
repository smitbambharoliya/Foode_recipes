<?php

namespace App\Entity;

use App\Repository\RecipeViewRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RecipeViewRepository::class)]
class RecipeView
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'recipeViews')]
    private ?Recipe $recipe = null;

    #[ORM\ManyToOne(inversedBy: 'recipeViews')]
    private ?User $user = null;

    #[ORM\Column]
    private ?\DateTime $viewedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRecipe(): ?Recipe
    {
        return $this->recipe;
    }

    public function setRecipe(?Recipe $recipe): static
    {
        $this->recipe = $recipe;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getViewedAt(): ?\DateTime
    {
        return $this->viewedAt;
    }

    public function setViewedAt(\DateTime $viewedAt): static
    {
        $this->viewedAt = $viewedAt;

        return $this;
    }
}
