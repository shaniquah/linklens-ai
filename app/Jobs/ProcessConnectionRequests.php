<?php

namespace App\Jobs;

use App\Models\LinkedinProfile;
use App\Services\SimplifiedBedrockService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProcessConnectionRequests implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private LinkedinProfile $profile
    ) {}

    public function handle(SimplifiedBedrockService $bedrockService): void
    {
        if (!$this->profile->auto_accept_connections) {
            return;
        }

        $connectionRequests = $this->fetchConnectionRequests();
        
        foreach ($connectionRequests as $request) {
            $this->processConnectionRequest($request, $bedrockService);
        }
    }

    private function fetchConnectionRequests(): array
    {
        try {
            $response = Http::withToken($this->profile->access_token)
                ->get('https://api.linkedin.com/v2/invitations', [
                    'q' => 'invitationType',
                    'invitationType' => 'CONNECTION'
                ]);

            if ($response->successful()) {
                return $response->json()['elements'] ?? [];
            }
        } catch (\Exception $e) {
            Log::error('Failed to fetch connection requests: ' . $e->getMessage());
        }

        return [];
    }

    private function processConnectionRequest(array $request, SimplifiedBedrockService $bedrockService): void
    {
        $inviterProfile = $this->getInviterProfile($request['from'] ?? '');
        
        if (!$inviterProfile) {
            return;
        }

        // Use AWS Bedrock to analyze the connection request
        $analysis = $bedrockService->analyzeConnectionRequest($inviterProfile);
        
        if ($analysis['decision'] === 'accept' && $analysis['confidence'] > 0.7) {
            $this->acceptConnectionRequest($request['id']);
            
            // Log the decision
            auth()->user()->logActivity('connection_accepted', 
                "Auto-accepted connection from {$inviterProfile['firstName']} {$inviterProfile['lastName']}", [
                'inviter_id' => $request['from'],
                'confidence' => $analysis['confidence'],
                'reason' => $analysis['reason']
            ]);
        } else {
            // Log rejection decision
            auth()->user()->logActivity('connection_rejected', 
                "Auto-rejected connection from {$inviterProfile['firstName']} {$inviterProfile['lastName']}", [
                'inviter_id' => $request['from'],
                'confidence' => $analysis['confidence'],
                'reason' => $analysis['reason']
            ]);
        }
    }

    private function getInviterProfile(string $inviterId): ?array
    {
        try {
            $response = Http::withToken($this->profile->access_token)
                ->get("https://api.linkedin.com/v2/people/(id:{$inviterId})");

            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Exception $e) {
            Log::error('Failed to fetch inviter profile: ' . $e->getMessage());
        }

        return null;
    }

    private function acceptConnectionRequest(string $invitationId): void
    {
        try {
            $response = Http::withToken($this->profile->access_token)
                ->post("https://api.linkedin.com/v2/invitations/{$invitationId}", [
                    'action' => 'accept'
                ]);

            if (!$response->successful()) {
                Log::error('Failed to accept connection request: ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('Failed to accept connection request: ' . $e->getMessage());
        }
    }
}