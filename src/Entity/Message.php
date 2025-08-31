<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Enum\MessageStatusEnum;
use DateTimeImmutable;
use Symfony\Component\Uid\Uuid;

#[ORM\HasLifecycleCallbacks] // Enable lifecycle callbacks
#[ORM\Entity(repositoryClass: MessageRepository::class)]

class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::GUID, unique: true)]
    private string $uuid;
    

    // text shouldn't be null
    #[ORM\Column(length: 255, nullable: false)]
    private string $text;

    /**Currently nullable: #[ORM\Column(length: 255, nullable: true)] private ?string $status = null;
     * But in your repository, createMessage() always sets PENDING.
     * Make it non-nullable with default value
     * Since this status will be managed automatically we will need to validate it in the request ParamsDto only
     * */   
    #[ORM\Column(enumType: MessageStatusEnum::class)]
    private MessageStatusEnum $status = MessageStatusEnum::PENDING;
    
    /** Currently type-hinted as DateTime, but better to use immutable DateTimeImmutable 
     * With DateTime, this mutates the original timestamp stored in the entity, possibly causing bugs when persisting or comparing dates.
    */
    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    /** 
     * Initialize Uuid, Status 
     * Created at we will set it using ORM lifecyclehook (PrePersist) 
     */
    public function __construct()
    {
        $this->uuid = Uuid::v6()->toRfc4122();
        $this->status = MessageStatusEnum::PENDING;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): static
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): static
    {
        $this->text = $text;

        return $this;
    }

    public function getStatus(): MessageStatusEnum
    {
        return $this->status;
    }

    public function setStatus(MessageStatusEnum $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        
        return $this;
    }

    /**
     * IF we want to add other entities that uses tomestampable we can create a trait for this part
     */
    #[ORM\PrePersist]
    public function initializeCreatedAt(): void
    {
        $this->createdAt = new \DateTimeImmutable();
    }

}
