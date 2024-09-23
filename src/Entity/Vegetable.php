<?php

namespace App\Entity;

use App\Repository\VegetableRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VegetableRepository::class)]
class Vegetable implements Entity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?float $quantity = null;

    private string $unit = 'g';

    public function getId(): ?int
    {
        return $this->id;
    }


    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
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

    public function getQuantity(): ?float
    {
        return $this->quantity;
    }

    public function setQuantity(float $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getUnit(): string
    {
        return $this->unit;
    }

    public function convertToKilogram(): static
    {
        $this->unit = 'kg';
        $this->quantity = round($this->quantity / 1000, 3);

        return $this;
    }
}
