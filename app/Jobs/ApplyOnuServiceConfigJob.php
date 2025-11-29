<?php

namespace App\Jobs;

use App\Models\OnuService;
use App\Services\OnuConfigurationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ApplyOnuServiceConfigJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected int $serviceId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $serviceId)
    {
        $this->serviceId = $serviceId;
    }

    /**
     * Execute the job.
     */
    public function handle(OnuConfigurationService $configurationService): void
    {
        $service = OnuService::with(['onu.olt'])->find($this->serviceId);

        if (!$service) {
            Log::warning('ApplyOnuServiceConfigJob: service not found', ['service_id' => $this->serviceId]);
            return;
        }

        $result = $configurationService->applyConfiguration($service);

        if (!$result['success']) {
            Log::warning('ApplyOnuServiceConfigJob failed', [
                'service_id' => $service->id,
                'message' => $result['message'] ?? 'Unknown error',
            ]);
        } else {
            Log::info('ApplyOnuServiceConfigJob success', [
                'service_id' => $service->id,
                'message' => $result['message'] ?? 'Configuration applied',
            ]);
        }
    }
}

