<?php

namespace App\Services\SmsProviders;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AqilasProvider implements SmsProviderInterface
{
    protected string $token;
    protected string $defaultFrom;
    protected string $baseUrl = 'https://api.aqilas.com/v1';

    public function __construct()
    {
        $this->token = config('services.aqilas.token', '');
        $this->defaultFrom = config('services.aqilas.default_from', 'GAMMA');
    }

    /**
     * Send SMS message via Aqilas.
     */
    public function sendSms(array $params): array
    {
        try {
            $response = Http::withToken($this->token)
                ->post("{$this->baseUrl}/sms/send", [
                    'to' => $params['to'],
                    'from' => $params['from'] ?? $this->defaultFrom,
                    'message' => $params['message'],
                ]);

            if ($response->successful()) {
                $data = $response->json();

                Log::info('Aqilas SMS sent successfully', [
                    'to' => $params['to'],
                    'message_id' => $data['message_id'] ?? null,
                ]);

                return [
                    'success' => true,
                    'message_id' => $data['message_id'] ?? null,
                    'status' => $data['status'] ?? 'sent',
                ];
            }

            throw new \Exception('Aqilas API error: ' . $response->body());
        } catch (\Exception $e) {
            Log::error('Aqilas SMS failed', [
                'to' => $params['to'],
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Get SMS delivery status from Aqilas.
     */
    public function getStatus(string $messageId): array
    {
        try {
            $response = Http::withToken($this->token)
                ->get("{$this->baseUrl}/sms/status/{$messageId}");

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'status' => 'unknown',
                'error' => 'Failed to retrieve status',
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get Aqilas message status', [
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
     * Get account balance from Aqilas.
     */
    public function getBalance(): float
    {
        try {
            $response = Http::withToken($this->token)
                ->get("{$this->baseUrl}/account/balance");

            if ($response->successful()) {
                $data = $response->json();
                return (float) ($data['balance'] ?? 0.0);
            }

            return 0.0;
        } catch (\Exception $e) {
            Log::error('Failed to get Aqilas balance', [
                'error' => $e->getMessage(),
            ]);

            return 0.0;
        }
    }
}
