<?php

namespace App\Service;

use App\Repository\MessageRepository;
use App\Dto\Request\MessageListRequestDto;
use App\Dto\Response\MessagesResponseDto;
use App\Dto\Request\Resolver\MessageTransformer;

class MessageService
{
    public function __construct(
        private MessageRepository $repo,
        private MessageTransformer $transformer
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
}