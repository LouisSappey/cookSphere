<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\InheritanceType("JOINED")]
#[ORM\DiscriminatorColumn(name: "type", type: "string")]
#[ORM\DiscriminatorMap([
    "base" => BaseIngredient::class,
    "composite" => CompositeIngredient::class
])]
abstract class Ingredient
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $nutritionalInfo = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $allergens = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getNutritionalInfo(): ?array
    {
        return $this->nutritionalInfo;
    }

    public function setNutritionalInfo(?array $nutritionalInfo): static
    {
        $this->nutritionalInfo = $nutritionalInfo;
        return $this;
    }

    public function getAllergens(): ?array
    {
        return $this->allergens;
    }

    public function setAllergens(?array $allergens): static
    {
        $this->allergens = $allergens;
        return $this;
    }
} 