<?php

namespace App\Services\SmsProviders;

interface SmsProviderInterface
{
    /**
     * Send SMS message.
     *
     * @param array $params Parameters including 'to', 'message', and optional 'from'
     * @return array Response with status and message ID
     */
    public function sendSms(array $params): array;

    /**
     * Get SMS delivery status.
     *
     * @param string $messageId The message ID
     * @return array Status information
     */
    public function getStatus(string $messageId): array;

    /**
     * Get account balance.
     *
     * @return float Account balance
     */
    public function getBalance(): float;
}
