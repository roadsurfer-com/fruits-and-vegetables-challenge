<?php

declare(strict_types=1);

namespace App\Service;

use App\Collection\Collection;

interface TypedTransferAwareStorageService
{
    public function toCollection(): Collection;
}
