<?php
declare(strict_types=1);
namespace App\Dto\Request\Resolver;

use App\Entity\Message;
use Symfony\Component\Form\DataTransformerInterface;
use App\Dto\Response\MessagesResponseDto;

class MessageTransformer implements DataTransformerInterface
{

    /** Transforms an array of Message entities into a MessagesResponseDto
     * @param mixed $messages The input messages, expected to be an array of Message entities
     * @return MessagesResponseDto The transformed MessagesResponseDto
     */
    public function transform(mixed $messages): MessagesResponseDto
    {

        // Ensure $messages is an array
        $messages = is_array($messages) ? $messages : [];

        // Map each Message entity to an associative array
        $messages = array_map(fn(Message $message) => [
            'uuid' => $message->getUuid(),
            'text' => $message->getText(),
            'status' => $message->getStatus(),
        ], $messages);

        // Return the MessagesResponseDto with the transformed messages
        return new MessagesResponseDto(messages: $messages);
    }

    public function reverseTransform(mixed $value): mixed
    {
        // Not needed for this use case
        return true;
    }
}
