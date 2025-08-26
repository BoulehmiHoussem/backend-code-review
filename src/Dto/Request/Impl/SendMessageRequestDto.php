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
    #[Assert\NotBlank(message: "Text is required")]
    #[Assert\Type('string', message: "Text must be a string")]
    #[Assert\Length(
        max: 1000,
        maxMessage: "Text cannot be longer than {{ limit }} characters"
    )]
    public function __construct(
        public readonly ?string $text = null
    ) {
    }
}