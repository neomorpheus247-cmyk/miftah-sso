<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class ProcessDelayedLogout implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected int $userId,
        protected ?string $tokenId = null
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Invalidate specific token if provided
        if ($this->tokenId) {
            PersonalAccessToken::findToken($this->tokenId)?->delete();
            return;
        }

        // Otherwise, invalidate all tokens for the user
        PersonalAccessToken::where('tokenable_id', $this->userId)
            ->where('tokenable_type', 'App\\Models\\User')
            ->delete();
    }
}
