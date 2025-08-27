<?php
declare(strict_types=1);

namespace App\Message;
use App\Entity\Message;

class SendMessage
{
    /**
     * @param Message $message The message entity containing the text to be sent
     */
    public function __construct(
        public Message $message,
    )
    {
    }
}