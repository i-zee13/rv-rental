<?php

namespace App\Mcp;

use RuntimeException;

class McpAuthException extends RuntimeException
{
    public function __construct(
        string $message,
        public int $status,
        public string $outcome,
    ) {
        parent::__construct($message);
    }
}
