<?php

namespace App\Entity;

use App\Repository\RecipeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RecipeRepository::class)]
class Recipe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $instructions = null;

    #[ORM\Column(type: Types::INTEGER, options: ['unsigned' => true, 'default' => 1])]
    private ?int $baseServings = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\ManyToOne(inversedBy: 'recipes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $chef = null;

    /**
     * @var Collection<int, Ingredient>
     */
    #[ORM\OneToMany(targetEntity: Ingredient::class, mappedBy: 'recipe', cascade: ['persist', 'remove'])]
    private Collection $ingredients;

    #[ORM\ManyToOne(inversedBy: 'recipes', cascade: ['persist'])]
    private ?Region $region = null;

    /**
     * @var Collection<int, RecipeView>
     */
    #[ORM\OneToMany(targetEntity: RecipeView::class, mappedBy: 'recipe', cascade: ['persist', 'remove'])]
    private Collection $recipeViews;

    

    /**
     * @var Collection<int, Review>
     */
    #[ORM\OneToMany(targetEntity: Review::class, mappedBy: 'recipe', cascade:['persist', 'remove'])]
    private Collection $reviews;

    #[ORM\Column(length: 255)]
    private ?string $meal_type = null;

    #[ORM\Column(type: 'datetime_immutable', nullable:true)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?bool $isVeg = null;



    public function __construct()
    {
        $this->ingredients = new ArrayCollection();
        $this->recipeViews = new ArrayCollection();
        
        $this->reviews = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getInstructions(): ?string
    {
        return $this->instructions;
    }

    public function setInstructions(string $instructions): static
    {
        $this->instructions = $instructions;

        return $this;
    }

    public function getBaseServings(): ?int
    {
        return $this->baseServings;
    }

    public function setBaseServings(int $baseServings): static
    {
        $this->baseServings = $baseServings;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getChef(): ?User
    {
        return $this->chef;
    }

    public function setChef(?User $chef): static
    {
        $this->chef = $chef;

        return $this;
    }

    /**
     * @return Collection<int, Ingredient>
     */
    public function getIngredients(): Collection
    {
        return $this->ingredients;
    }

    public function addIngredient(Ingredient $ingredient): static
    {
        if (!$this->ingredients->contains($ingredient)) {
            $this->ingredients->add($ingredient);
            $ingredient->setRecipe($this);
        }

        return $this;
    }

    public function removeIngredient(Ingredient $ingredient): static
    {
        if ($this->ingredients->removeElement($ingredient)) {
            // set the owning side to null (unless already changed)
            if ($ingredient->getRecipe() === $this) {
                $ingredient->setRecipe(null);
            }
        }

        return $this;
    }

    public function getRegion(): ?Region
    {
        return $this->region;
    }

    public function setRegion(?Region $region): static
    {
        $this->region = $region;

        return $this;
    }

    /**
     * @return Collection<int, RecipeView>
     */
    public function getRecipeViews(): Collection
    {
        return $this->recipeViews;
    }

    public function addRecipeView(RecipeView $recipeView): static
    {
        if (!$this->recipeViews->contains($recipeView)) {
            $this->recipeViews->add($recipeView);
            $recipeView->setRecipe($this);
        }

        return $this;
    }

    public function removeRecipeView(RecipeView $recipeView): static
    {
        if ($this->recipeViews->removeElement($recipeView)) {
            // set the owning side to null (unless already changed)
            if ($recipeView->getRecipe() === $this) {
                $recipeView->setRecipe(null);
            }
        }

        return $this;
    }

    
  

    /**
     * @return Collection<int, Review>
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): static
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews->add($review);
            $review->setRecipe($this);
        }

        return $this;
    }

    public function removeReview(Review $review): static
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getRecipe() === $this) {
                $review->setRecipe(null);
            }
        }

        return $this;
    }

    public function getMealType(): ?string
    {
        return $this->meal_type;
    }

    public function setMealType(string $meal_type): static
    {
        $this->meal_type = $meal_type;

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

    public function isVeg(): ?bool
    {
        return $this->isVeg;
    }

    public function setIsVeg(bool $isVeg): static
    {
        $this->isVeg = $isVeg;

        return $this;
    }

    
}
