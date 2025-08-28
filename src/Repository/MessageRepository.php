<?php

namespace App\Repository;

use App\Entity\Message;
use App\Enum\MessageStatusEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Message>
 *
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }
    
    /**
     * @return array<int, Message>
     */
    public function findByStatus(?string $status): array
    {
        if ($status !== null) {
            //Using sprintf() with $status directly is dangerous.
            //If $status comes from user input, it can allow SQL injection, even in DQL.
            //Solution: Use parameter binding to safely include user input in queries.
            $messages = $this->findBy(['status' => $status]);
        } else {
            $messages = $this->findAll();
        }
        return $messages;
    }

    /**
     * Creates and persists a new Message entity.
     *
     * @param string $text The text of the message.
     * @return Message The persisted message entity.
     */
    public function createMessage(?string $text): Message
    {
        $message = new Message();
        $message->setText($text);   
        $message->setUuid(\Symfony\Component\Uid\Uuid::v6()->toRfc4122());
        $message->setStatus(MessageStatusEnum::PENDING->value);
        $message->setCreatedAt(new \DateTime());
        $this->getEntityManager()->persist($message);
        $this->getEntityManager()->flush();
        return $message;
    }


    /**
     * Sets the status of a message to 'sent'.
     *
     * @param string $uuid The message entity to update.
     * @return void
     */
    public function SetStatusToSent(string $uuid) : void{
        $message = $this->findOneBy(['uuid' => $uuid]);
        if (!$message) throw new \InvalidArgumentException("Message with UUID $uuid not found.");
        $message->setStatus(MessageStatusEnum::SENT->value);
        $this->getEntityManager()->persist($message);
        $this->getEntityManager()->flush();
    }

    /**
     * Sets the status of a message to 'failed'.
     *
     * @param string $uuid The message entity to update.
     * @return void
     */
    public function SetStatusToFailed(string $uuid) :void{
        $message = $this->findOneBy(['uuid' => $uuid]);
        if (!$message) throw new \InvalidArgumentException("Message with UUID $uuid not found.");
        $message->setStatus(MessageStatusEnum::FAILED->value);
        $this->getEntityManager()->persist($message);
        $this->getEntityManager()->flush();
    }
}
