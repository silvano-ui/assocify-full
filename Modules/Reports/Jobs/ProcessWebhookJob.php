<?php

namespace Modules\Reports\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Reports\Services\WebhookService;
use Modules\Reports\Entities\WebhookDelivery;

class ProcessWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $deliveryId;

    public function __construct($deliveryId)
    {
        $this->deliveryId = $deliveryId;
    }

    public function handle(WebhookService $webhookService)
    {
        $delivery = WebhookDelivery::find($this->deliveryId);
        
        if ($delivery && $delivery->status !== 'success' && $delivery->attempts < config('reports.webhooks.max_attempts', 3)) {
            $webhookService->send($delivery);
        }
    }
}
