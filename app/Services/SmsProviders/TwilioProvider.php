<?php

namespace App\Services\SmsProviders;

use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;

class TwilioProvider implements SmsProviderInterface
{
    protected Client $client;
    protected string $fromNumber;

    public function __construct()
    {
        $accountSid = config('services.twilio.account_sid');
        $authToken = config('services.twilio.auth_token');
        $this->fromNumber = config('services.twilio.from_number');

        $this->client = new Client($accountSid, $authToken);
    }

    /**
     * Send SMS message via Twilio.
     */
    public function sendSms(array $params): array
    {
        try {
            $message = $this->client->messages->create(
                $params['to'],
                [
                    'from' => $params['from'] ?? $this->fromNumber,
                    'body' => $params['message'],
                ]
            );

            Log::info('Twilio SMS sent successfully', [
                'to' => $params['to'],
                'sid' => $message->sid,
            ]);

            return [
                'success' => true,
                'message_id' => $message->sid,
                'status' => $message->status,
            ];
        } catch (\Exception $e) {
            Log::error('Twilio SMS failed', [
                'to' => $params['to'],
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Get SMS delivery status from Twilio.
     */
    public function getStatus(string $messageId): array
    {
        try {
            $message = $this->client->messages($messageId)->fetch();

            return [
                'status' => $message->status,
                'error_code' => $message->errorCode,
                'error_message' => $message->errorMessage,
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get Twilio message status', [
                'message_id' => $messageId,
                'error' => $e->getMessage(),
            ]);

            return [
                'status' => 'unknown',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get account balance from Twilio.
     */
    public function getBalance(): float
    {
        try {
            $balance = $this->client->balance->fetch();
            return (float) $balance->balance;
        } catch (\Exception $e) {
            Log::error('Failed to get Twilio balance', [
                'error' => $e->getMessage(),
            ]);

            return 0.0;
        }
    }
}
