<?php

namespace App\Dto\Response;

/**
 * DTO for the response containing a list of messages
 */
class MessagesResponseDto 
{
    /**
     * @param array<int, array<string, string|null>> $messages Array of messages
     */
    public function __construct(
        public array $messages = []
    ) {}
}
