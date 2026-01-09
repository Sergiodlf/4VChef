<?php

namespace App\Entity;

use App\Repository\RecipeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RecipeRepository::class)]
class Recipe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    private ?string $title = null;

    #[ORM\Column]
    private ?int $diners = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isDeleted = false;

    #[ORM\ManyToOne(inversedBy: 'recipes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?RecipeType $type = null;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;


    /**
     * @var Collection<int, Ingredient>
     */
    #[ORM\OneToMany(
        targetEntity: Ingredient::class,
        mappedBy: 'recipe',
        cascade: ['persist', 'remove'],
        orphanRemoval: true
    )]
    private Collection $ingredients;


    /**
     * @var Collection<int, Step>
     */
    #[ORM\OneToMany(
        targetEntity: Step::class,
        mappedBy: 'recipe',
        cascade: ['persist', 'remove'],
        orphanRemoval: true
    )]
    #[ORM\OrderBy(['stepOrder' => 'ASC'])]
    private Collection $steps;


    /**
     * @var Collection<int, RecipeNutrient>
     */
    #[ORM\OneToMany(
        targetEntity: RecipeNutrient::class,
        mappedBy: 'recipe',
        cascade: ['persist', 'remove'],
        orphanRemoval: true
    )]
    private Collection $nutritionalValues;


    /**
     * @var Collection<int, Rating>
     */
    #[ORM\OneToMany(
        targetEntity: Rating::class,
        mappedBy: 'recipe',
        cascade: ['persist', 'remove'],
        orphanRemoval: true
    )]
    private Collection $ratings;


    public function __construct()
    {
        $this->ingredients = new ArrayCollection();
        $this->steps = new ArrayCollection();
        $this->nutritionalValues = new ArrayCollection();
        $this->ratings = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
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

    public function getDiners(): ?int
    {
        return $this->diners;
    }

    public function setDiners(int $diners): static
    {
        $this->diners = $diners;

        return $this;
    }

    public function getType(): ?RecipeType
    {
        return $this->type;
    }

    public function setType(?RecipeType $type): static
    {
        $this->type = $type;

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

    /**
     * @return Collection<int, Step>
     */
    public function getSteps(): Collection
    {
        return $this->steps;
    }

    public function addStep(Step $step): static
    {
        if (!$this->steps->contains($step)) {
            $this->steps->add($step);
            $step->setRecipe($this);
        }

        return $this;
    }

    public function removeStep(Step $step): static
    {
        if ($this->steps->removeElement($step)) {
            // set the owning side to null (unless already changed)
            if ($step->getRecipe() === $this) {
                $step->setRecipe(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, RecipeNutrient>
     */
    public function getNutritionalValues(): Collection
    {
        return $this->nutritionalValues;
    }

    public function addNutritionalValue(RecipeNutrient $nutritionalValue): static
    {
        if (!$this->nutritionalValues->contains($nutritionalValue)) {
            $this->nutritionalValues->add($nutritionalValue);
            $nutritionalValue->setRecipe($this);
        }

        return $this;
    }

    public function removeNutritionalValue(RecipeNutrient $nutritionalValue): static
    {
        if ($this->nutritionalValues->removeElement($nutritionalValue)) {
            // set the owning side to null (unless already changed)
            if ($nutritionalValue->getRecipe() === $this) {
                $nutritionalValue->setRecipe(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Rating>
     */
    public function getRatings(): Collection
    {
        return $this->ratings;
    }

    public function addRating(Rating $rating): static
    {
        if (!$this->ratings->contains($rating)) {
            $this->ratings->add($rating);
            $rating->setRecipe($this);
        }

        return $this;
    }

    public function removeRating(Rating $rating): static
    {
        if ($this->ratings->removeElement($rating)) {
            // set the owning side to null (unless already changed)
            if ($rating->getRecipe() === $this) {
                $rating->setRecipe(null);
            }
        }

        return $this;
    }

    public function isDeleted(): bool
    {
        return $this->isDeleted;
    }

    public function setIsDeleted(bool $isDeleted): self
    {
        $this->isDeleted = $isDeleted;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
