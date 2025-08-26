<?php

declare(strict_types=1);

namespace App\Dto\Request\Impl;
use App\Dto\Request\RequestDtoInterface;

/**
 * DTO for listing messages with optional filters
 */
class MessageListRequestDto implements RequestDtoInterface
{
    /** The status filter for listing messages */
    public function __construct(
        public readonly ?string $status = null
    ) {
    }
}