<?php

declare(strict_types=1);

namespace App\Tests;

class WebTestCase extends \Symfony\Bundle\FrameworkBundle\Test\WebTestCase
{
    #[\PHPUnit\Framework\Attributes\After]
    public function __internalDisableErrorHandler(): void
    {
        \restore_exception_handler();
    }
}
