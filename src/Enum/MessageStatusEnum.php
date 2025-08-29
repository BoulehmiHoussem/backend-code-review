<?php

namespace App\Enum;

/**
 * Enum representing the possible statuses of a message
 * - PENDING: The message is created in database but not yet handled (Sent by email for example)
 * - SENT: The message has been successfully sent (Sent by email for example)
 * - FAILED: The message failed to send (SMTP Failure for example)
 */
enum MessageStatusEnum: string
{
    case PENDING = 'pending';
    case SENT = 'sent';
    case FAILED = 'failed';
}
