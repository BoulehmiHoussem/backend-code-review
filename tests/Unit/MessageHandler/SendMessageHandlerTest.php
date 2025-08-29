<?php

declare(strict_types=1);

namespace App\Tests\Unit\MessageHandler;

use App\MessageHandler\SendMessageHandler;
use App\Message\SendMessage;
use App\Entity\Message;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class SendMessageHandlerTest extends TestCase
{
    public function test_handler_sends_email(): void
    {
        // Arrange
        $messageText = 'Hello world';
        $messageEntity = $this->createMock(Message::class);
        $messageEntity->method('getText')->willReturn($messageText);

        /** @var Message&\PHPUnit\Framework\MockObject\MockObject $messageEntity */
        $sendMessage = new SendMessage($messageEntity);

        /** @var MailerInterface&\PHPUnit\Framework\MockObject\MockObject $mailer */
        $mailer = $this->createMock(MailerInterface::class);

        $mailer->expects($this->once())
            ->method('send')
            ->with($this->callback(function (Email $email) use ($messageText) {
                // Full assertions
                $this->assertEquals('noreply@example.com', $email->getFrom()[0]->getAddress(), 'From address should match');
                $this->assertEquals('admin@mail.com', $email->getTo()[0]->getAddress(), 'To address should match');
                $this->assertEquals('New message', $email->getSubject(), 'Subject should match');
                $this->assertEquals('You have a new message: ' . $messageText, $email->getTextBody(), 'Text body should match');

                return true; // callback must return true if assertion passed
            }));

        // Act
        $handler = new SendMessageHandler($mailer);
        $handler($sendMessage);
    }

    public function test_handler_handles_mailer_service_exceptions(): void
    {
        // Arrange
        $messageText = 'Hello world';
        $messageEntity = $this->createMock(Message::class);
        $messageEntity->method('getText')->willReturn($messageText);

        /** @var Message&\PHPUnit\Framework\MockObject\MockObject $messageEntity */
        $sendMessage = new SendMessage($messageEntity);

        /** @var MailerInterface&\PHPUnit\Framework\MockObject\MockObject $mailer */
        $mailer = $this->createMock(MailerInterface::class);

        $mailer->expects($this->once())
            ->method('send')
            ->with($this->callback(function (Email $email) use ($messageText) {
                // Full assertions
                $this->assertEquals('noreply@example.com', $email->getFrom()[0]->getAddress(), 'From address should match');
                $this->assertEquals('admin@mail.com', $email->getTo()[0]->getAddress(), 'To address should match');
                $this->assertEquals('New message', $email->getSubject(), 'Subject should match');
                $this->assertEquals('You have a new message: ' . $messageText, $email->getTextBody(), 'Text body should match');

                return true; // callback must return true if assertion passed
            }));

        // Act
        $handler = new SendMessageHandler($mailer);
        $handler($sendMessage);
    }
}
