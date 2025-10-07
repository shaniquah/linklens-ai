<?php

namespace App\Jobs;

use App\Models\ConnectionFilter;
use App\Models\LinkedinProfile;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class ProcessConnectionRequests implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private LinkedinProfile $profile
    ) {}

    public function handle(): void
    {
        if (!$this->profile->auto_accept_connections) {
            return;
        }

        $filters = ConnectionFilter::where('user_id', $this->profile->user_id)
            ->where('is_active', true)
            ->get();

        $requests = $this->getConnectionRequests();

        foreach ($requests as $request) {
            if ($this->shouldAcceptConnection($request, $filters)) {
                $this->acceptConnection($request['id']);
            }
        }
    }

    private function getConnectionRequests(): array
    {
        $response = Http::withToken($this->profile->access_token)
            ->get('https://api.linkedin.com/v2/people/~/mailbox', [
                'type' => 'INVITATION_REQUEST'
            ]);

        return $response->json()['values'] ?? [];
    }

    private function shouldAcceptConnection(array $request, $filters): bool
    {
        if ($filters->isEmpty()) {
            return true;
        }

        foreach ($filters as $filter) {
            if ($filter->matchesProfile($request['from'] ?? [])) {
                return true;
            }
        }

        return false;
    }

    private function acceptConnection(string $invitationId): void
    {
        Http::withToken($this->profile->access_token)
            ->post("https://api.linkedin.com/v2/people/~/mailbox/{$invitationId}", [
                'action' => 'accept'
            ]);
    }
}