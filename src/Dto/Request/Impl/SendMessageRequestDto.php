<?php

declare(strict_types=1);

namespace App\Dto\Request\Impl;

use App\Dto\Request\RequestDtoInterface;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * DTO for listing messages with optional filters
 */
class SendMessageRequestDto implements RequestDtoInterface
{
    /** The message */

    public function __construct(
        #[Assert\NotBlank(message: "Text is required")]
        public readonly ?string $text = null
    ) {}

    public function getText(): ?string
    {
        return $this->text;
    }
}
