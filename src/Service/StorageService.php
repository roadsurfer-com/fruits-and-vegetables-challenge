<?php

declare(strict_types=1);

namespace App\Service;

final class StorageService
{
    private string $request = '';

    public function __construct(
        string $request
    ) {
        $this->request = $request;
    }

    public function getRequest(): string
    {
        return $this->request;
    }
}
