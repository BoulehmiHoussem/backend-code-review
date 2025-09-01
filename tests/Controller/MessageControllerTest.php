<?php

declare(strict_types=1);

namespace Controller;

use App\Message\SendMessage;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Messenger\Test\InteractsWithMessenger;
use App\Entity\Message;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use App\Enum\MessageStatusEnum;

class MessageControllerTest extends WebTestCase
{
    use InteractsWithMessenger;

    private EntityManagerInterface $entityManager;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $em = static::getContainer()->get(EntityManagerInterface::class);
        assert($em instanceof EntityManagerInterface);
        $this->entityManager = $em;
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // Purge the database after each test
        $purger = new ORMPurger($this->entityManager);
        $purger->purge();

        $this->entityManager->clear();
    }

    /**
     * Data provider for test_list
     * @return array<string, array{setupMessages: array<array<string, string>>, queryParams: array<string, string>, expectedCount: int}>
     */
    public function listMessagesProvider(): array
    {
        return [
            'empty messages' => [
                'setupMessages' => [],
                'queryParams' => [],
                'expectedCount' => 0,
            ],
            'single message' => [
                'setupMessages' => [
                    ['text' => 'Hello', 'status' => 'sent']
                ],
                'queryParams' => [],
                'expectedCount' => 1,
            ],
            'multiple messages' => [
                'setupMessages' => [
                    ['text' => 'Message 1', 'status' => 'sent'],
                    ['text' => 'Message 2', 'status' => 'pending']
                ],
                'queryParams' => [],
                'expectedCount' => 2,
            ],
            'filter by status sent' => [
                'setupMessages' => [
                    ['text' => 'Message 1', 'status' => 'sent'],
                    ['text' => 'Message 2', 'status' => 'pending']
                ],
                'queryParams' => ['status' => 'sent'],
                'expectedCount' => 1,
            ],
            'invalid query params ignored' => [
                'setupMessages' => [
                    ['text' => 'Hello', 'status' => 'sent']
                ],
                'queryParams' => ['foo' => 'bar', 'limit' => 'abc'],
                'expectedCount' => 1,
            ],
        ];
    }

    /**
     * @dataProvider listMessagesProvider
     * @param array<array<string, string>> $setupMessages
     * @param array<string, string> $queryParams
     * @param int $expectedCount
     */
    function test_list(array $setupMessages, $queryParams, int $expectedCount): void
    {
        // GIVEN messages in database

        foreach ($setupMessages as $msgData) {
            $msg = new Message();
            $msg->setText($msgData['text']);
            $msg->setStatus(MessageStatusEnum::from($msgData['status']));
            $this->entityManager->persist($msg);
        }
        $this->entityManager->flush();

        // WHEN requesting /messages
        $this->client->request('GET', '/messages', $queryParams);

        // THEN response contains expected count and keys
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $responseContent = $this->client->getResponse()->getContent() ?: '{}';

        $data = json_decode($responseContent, true);
        $this->assertIsArray($data);
        $this->assertCount($expectedCount, $data['messages']);

        foreach ($data['messages'] as $msg) {
            $this->assertArrayHasKey('uuid', $msg);
            $this->assertArrayHasKey('text', $msg);
            $this->assertArrayHasKey('status', $msg);
        }
    }
    
    /**
     * data provider
     * @return array<array<string,string|int>>
     */
    public function listSendMessagesProvider(): array
    {
        return [
            'valid message' => [
                'message' => "Hello World",
                'status' => 201,
                'dispatched' => 1,
            ],
            'empty message' => [
                'message' => "",
                'status' => 400,
                'dispatched' => 0,
            ],
        ];
    }

    /**
     * @dataProvider listSendMessagesProvider
     * @param string $message
     * @param int $status
     * @param int $dispatched
     */
    function test_that_it_sends_a_message(string $message, int $status, $dispatched): void
    {
        // WHEN requesting /messages/send
        $encoded_message = json_encode(['text' => $message]) ?: "{}";
        $this->client->request(
            'POST',
            '/messages/send',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $encoded_message
        );

        $this->assertResponseStatusCodeSame($status);
        // This is using https://packagist.org/packages/zenstruck/messenger-test
        $this->transport('async')
            ->queue()
            ->assertContains(SendMessage::class, $dispatched);
    }
}
