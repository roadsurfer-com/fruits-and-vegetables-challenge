<?php

declare(strict_types=1);

namespace App\Entity;

interface Entity
{
    public function setId(int $id): static;

    public function getId(): ?int;

    public function convertToKilogram(): static;
}
