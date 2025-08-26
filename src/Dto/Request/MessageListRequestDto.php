<?php

declare(strict_types=1);

namespace App\Dto\Request;

/**
 * DTO for listing messages with optional filters
 */
class MessageListRequestDto
{
    /** The status filter for listing messages */
    public function __construct(
        public readonly ?string $status = null
    ) {
    }
}