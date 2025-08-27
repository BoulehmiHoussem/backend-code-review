<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Event\WorkerMessageHandledEvent;
use Psr\Log\LoggerInterface;
use App\Service\MessageService;
use App\Message\SendMessage;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;

class EmailMessageSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private LoggerInterface $logger,
        private MessageService $messageService
    ) {}

    /**
     * Handles the event when a message is successfully processed by the worker.
     *
     * @param WorkerMessageHandledEvent $event The event containing message details
     */
    public function onWorkerMessageHandledEvent(WorkerMessageHandledEvent $event): void
    {

        $envelopeMessage = $event->getEnvelope()->getMessage();
        
        // Ensure the message is of type SendMessage
        if (!$envelopeMessage instanceof SendMessage) {
            return;
        }
        // Update the message status to 'sent'
        $this->messageService->setStatusToSent($envelopeMessage->message->getUuid());

        // Log the successful processing
        $this->logger->info('Message with has been sent and status updated to SENT.');
    }

    public function onWorkerMessageFailedEvent(WorkerMessageFailedEvent $event): void
    {
        $envelopeMessage = $event->getEnvelope()->getMessage();
        if (!$envelopeMessage instanceof SendMessage) {
            return;
        }
        // Update the message status to 'failed'
        $this->messageService->setStatusToFailed($envelopeMessage->message->getUuid());
        $this->logger->error('Failed to send message, Status updated to FAILED.');
    }

    public static function getSubscribedEvents(): array
    {
        return [
            WorkerMessageHandledEvent::class => 'onWorkerMessageHandledEvent',
            WorkerMessageFailedEvent::class => 'onWorkerMessageFailedEvent',
        ];
    }
}
