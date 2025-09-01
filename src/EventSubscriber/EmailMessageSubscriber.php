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

        if(!$envelopeMessage->message->getUuid()) {
            $this->logger->error('Message UUID is missing, cannot update status.');
            return;
        }

        // Update the message status to 'sent'
        try {
            $this->messageService->setStatusToSent($envelopeMessage->message->getUuid());
        } catch (\Throwable $th) {
            $this->logger->error('An error occurred', ['exception' => $th]);
            return;
        }

        // Log the successful processing
        $this->logger->info('Message with has been sent and status updated to SENT.');
    }

    public function onWorkerMessageFailedEvent(WorkerMessageFailedEvent $event): void
    {
        $envelopeMessage = $event->getEnvelope()->getMessage();
        if (!$envelopeMessage instanceof SendMessage) {
            return;
        }

         if(!$envelopeMessage->message->getUuid()) {
            $this->logger->error('Message UUID is missing, cannot update status.');
            return;
        }
        
        // Update the message status to 'failed'
        try {
            $this->messageService->setStatusToFailed($envelopeMessage->message->getUuid());
        } catch (\Throwable $th){
            $this->logger->error('An error occurred', ['exception' => $th]);
            return;
        }
        $this->logger->error('Failed to send message (Check MAILER_DSN .env), Status updated to FAILED.');
    }

    public static function getSubscribedEvents(): array
    {
        return [
            WorkerMessageHandledEvent::class => 'onWorkerMessageHandledEvent',
            WorkerMessageFailedEvent::class => 'onWorkerMessageFailedEvent',
        ];
    }
}
