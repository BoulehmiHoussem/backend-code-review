<?php

declare(strict_types=1);

namespace App\MessageHandler;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use App\Message\SendMessage;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;

#[AsMessageHandler]
/**
 * TODO: Cover with a test
 */
class SendMessageHandler
{

    public function __construct(
        private MailerInterface $mailer,
    ) {}
    public function __invoke(SendMessage $textMessage): void
    {
        // Here you would implement the logic to actually send the text message and not persisting the data.
        // For example, integrating with an SMS gateway or email service.
        // This is a placeholder implementation.
        // those functions should be treated in background jobs
        // to avoid slowing down the response time of the API.
        // there are no specs for this part but i will simulate sending an email
        // this part is just for demonstration purposes
        // I used Mailtrap for testing emails
        try {
            $email = (new Email())
                ->from('noreply@example.com')
                ->to('admin@mail.com')
                ->subject('New message')
                ->text('You have a new message: ' . $textMessage->message->getText());
            $this->mailer->send($email);
        } catch (\Throwable $th) {
            throw new \ErrorException("Mailer failed");
        }
    }
}
