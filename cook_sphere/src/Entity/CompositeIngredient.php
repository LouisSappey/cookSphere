<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class CompositeIngredient extends Ingredient
{
    #[ORM\ManyToMany(targetEntity: Ingredient::class)]
    private Collection $subIngredients;

    public function __construct()
    {
        $this->subIngredients = new ArrayCollection();
    }

    /**
     * @return Collection<int, Ingredient>
     */
    public function getSubIngredients(): Collection
    {
        return $this->subIngredients;
    }

    public function addSubIngredient(Ingredient $ingredient): static
    {
        if (!$this->subIngredients->contains($ingredient)) {
            $this->subIngredients->add($ingredient);
        }
        return $this;
    }

    public function removeSubIngredient(Ingredient $ingredient): static
    {
        $this->subIngredients->removeElement($ingredient);
        return $this;
    }
} 