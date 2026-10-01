<?php

namespace App\Services;

use RuntimeException;

class ArnaruAiException extends RuntimeException
{
    public function __construct(public readonly int $statusCode, string $message)
    {
        parent::__construct($message);
    }
}
