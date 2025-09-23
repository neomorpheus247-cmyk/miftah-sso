<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class ProcessDelayedLogout implements ShouldQueue
{
    use Queueable;

    public $tries = 3;
    public $maxExceptions = 3;
    public $backoff = 60;
    public $timeout = 30;
    public $deleteWhenMissingModels = true;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected int $userId,
        protected ?string $tokenId = null
    ) {
        $this->onQueue(config('queue.delayed_logout_queue', 'delayed-logout'));
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Invalidate specific JWT token if provided
        if ($this->tokenId) {
            JWTAuth::setToken($this->tokenId)->invalidate();
            return;
        }
        // Otherwise, you may want to invalidate all tokens for the user (custom logic required)
    }
}
