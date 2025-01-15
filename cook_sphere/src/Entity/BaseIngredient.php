<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class BaseIngredient extends Ingredient
{
    #[ORM\Column(length: 50)]
    private ?string $measurementUnit = null;

    #[ORM\Column]
    private ?float $calories = null;

    public function getMeasurementUnit(): ?string
    {
        return $this->measurementUnit;
    }

    public function setMeasurementUnit(string $measurementUnit): static
    {
        $this->measurementUnit = $measurementUnit;
        return $this;
    }

    public function getCalories(): ?float
    {
        return $this->calories;
    }

    public function setCalories(float $calories): static
    {
        $this->calories = $calories;
        return $this;
    }
} 