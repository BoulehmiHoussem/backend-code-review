<?php

namespace App\Service;

use App\Repository\MessageRepository;
use App\Dto\Request\Impl\MessageListRequestDto;
use App\Dto\Response\MessagesResponseDto;
use App\Dto\Response\Transformer\MessageTransformer;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Message\SendMessage;
use App\Dto\Request\Impl\SendMessageRequestDto;
use App\Entity\Message;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class MessageService
{
    public function __construct(
        private MessageRepository $repo,
        private MessageTransformer $transformer,
        private MessageBusInterface $bus,
        private ValidatorInterface $validator, // Inject the validator service
    ) {}


    /**
     * Lists messages based on the provided parameters.
     * @param MessageListRequestDto $params The parameters for listing messages.
     * @return MessagesResponseDto The response DTO containing the list of messages.
     */
    public function listMessages(MessageListRequestDto $params): MessagesResponseDto
    {
        
        // Fetch messages based on the status filter from the DTO.
        $messages = $this->repo->findByStatus($params->status->value ?? null);


        // Transform the messages to the response DTO format.
        return $this->transformer->transform($messages);
    }

    /**
     * Sends a message by dispatching it to the message bus.
     * @param SendMessageRequestDto $dto The DTO containing the message payload to be sent.
     */
    public function sendMessage(SendMessageRequestDto $dto): Message
    {
        // Validate the DTO
        $errors = $this->validator->validate($dto);
        if (count($errors) > 0 && $errors[0] != null) {
            // Handle validation errors (you can throw an exception or return an error response)
            throw new \InvalidArgumentException((string) $errors[0]->getMessage());
        }
        $message = $this->repo->createMessage($dto->getText());

        // Dispatch the message to the message bus for asynchronous processing.
        $this->bus->dispatch(new SendMessage($message));

        // Return the created message entity
        return $message;
    }

    /**
     * Sets the status of a message to 'sent'.
     * @param string $uuid The UUID of the message entity to update.
     */
    public function setStatusToSent(string $uuid): void
    {
        $this->repo->setStatusToSent($uuid);
    }

    /**
     * Sets the status of a message to 'failed'.
     * @param string $uuid The message entity to update.
     */
    public function setStatusToFailed(string $uuid): void
    {
        $this->repo->setStatusToFailed($uuid);
    }
}
