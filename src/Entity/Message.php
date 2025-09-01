<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Enum\MessageStatusEnum;
use Symfony\Component\Uid\Uuid;
use App\Entity\Trait\Timestampable;

#[ORM\HasLifecycleCallbacks] // Enable lifecycle callbacks
#[ORM\Entity(repositoryClass: MessageRepository::class)]

class Message
{
    // trait that has createdAt attributes and functions
    use Timestampable;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::GUID, unique: true)]
    private readonly string $uuid;


    // text shouldn't be null
    #[ORM\Column(Types::TEXT, nullable: false)]
    private string $text;

    /**Currently nullable: #[ORM\Column(length: 255, nullable: true)] private ?string $status = null;
     * But in your repository, createMessage() always sets PENDING.
     * Make it non-nullable with default value
     * Since this status will be managed automatically we will need to validate it in the request ParamsDto only
     * Optional: enforce default value at DB level too using options: ["default" => MessageStatusEnum::PENDING])
     * */
    #[ORM\Column(enumType: MessageStatusEnum::class, options: ["default" => MessageStatusEnum::PENDING])]
    private MessageStatusEnum $status = MessageStatusEnum::PENDING;


    /** 
     * Initialize Uuid, Status 
     * Created at we will set it using ORM lifecyclehook (PrePersist) 
     */
    public function __construct()
    {
        $this->uuid = Uuid::v6()->toRfc4122();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    // UUID should not change after creation (remove the setter)


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

    /**
     * @internal setStatus is intended for fixtures/tests only
     */
    public function setStatus(MessageStatusEnum $status): static
    {
        $this->status = $status;

        return $this;
    }
}
