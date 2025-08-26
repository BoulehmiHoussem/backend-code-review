<?php

namespace App\Repository;

use App\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Dto\Request\MessageListRequestDto;
use App\Dto\Request\Resolver\ParamsResolver;

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


}
