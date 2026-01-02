<?php

namespace Modules\Reports\Console;

use Illuminate\Console\Command;
use Modules\Reports\Entities\WebhookDelivery;
use Modules\Reports\Jobs\ProcessWebhookJob;

class RetryWebhooksCommand extends Command
{
    protected $signature = 'reports:webhooks';
    protected $description = 'Retry failed webhook deliveries';

    public function handle()
    {
        $this->info('Retrying failed webhooks...');
        
        $deliveries = WebhookDelivery::where('status', 'failed')
            ->where('attempts', '<', config('reports.webhooks.max_attempts', 3))
            ->get();

        foreach ($deliveries as $delivery) {
            ProcessWebhookJob::dispatch($delivery->id);
        }
        
        $this->info('Done.');
    }
}
