<?php

namespace App\Service;

use App\Repository\MessageRepository;
use App\Dto\Request\Impl\MessageListRequestDto;
use App\Dto\Response\MessagesResponseDto;
use App\Dto\Response\Transformer\MessageTransformer;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Message\SendMessage;

class MessageService
{
    public function __construct(
        private MessageRepository $repo,
        private MessageTransformer $transformer,
        private MessageBusInterface $bus,        
    ) {}


    /**
     * Lists messages based on the provided parameters.
     * @param MessageListRequestDto $params The parameters for listing messages.
     * @return MessagesResponseDto The response DTO containing the list of messages.
     */
    public function listMessages(MessageListRequestDto $params): MessagesResponseDto
    {
        // Fetch messages based on the status filter from the DTO.
        $messages = $this->repo->findByStatus($params->status);

        // Transform the messages to the response DTO format.
        return $this->transformer->transform($messages);
    }

    public function sendMessage(string $text): void
    {
        //$this->repo->createMessage($text);
        $this->bus->dispatch(new SendMessage($text));
    }
}