<?php

namespace Modules\Reports\Services;

use Modules\Reports\Entities\WebhookDelivery;
use Illuminate\Support\Facades\Http;

class WebhookService
{
    public function dispatch(string $url, array $payload): WebhookDelivery
    {
        return WebhookDelivery::create([
            'url' => $url, // Assuming schema has url
            'payload' => $payload,
            'status' => 'pending',
        ]);
    }

    public function send(WebhookDelivery $delivery): void
    {
        $signature = hash_hmac('sha256', json_encode($delivery->payload), config('app.key')); // Or specific secret

        try {
            $response = Http::withHeaders([
                'X-Signature' => $signature,
            ])->post($delivery->url, $delivery->payload); // Assuming URL is stored in model

            $delivery->update([
                'response_status' => $response->status(),
                'response_body' => $response->json(),
                'is_successful' => $response->successful(),
                'attempted_at' => now(),
            ]);
        } catch (\Exception $e) {
            $delivery->update([
                'is_successful' => false,
                'error' => $e->getMessage(),
                'attempted_at' => now(),
            ]);
            
            // Retry logic here or in a job
        }
    }
}
