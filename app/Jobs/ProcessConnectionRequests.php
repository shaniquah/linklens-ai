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
            return true; // Accept all if no filters
        }

        $profile = $this->getProfileDetails($request['from']['id'] ?? '');
        
        if (!$profile) {
            return false; // Reject if can't get profile data
        }

        foreach ($filters as $filter) {
            if ($this->matchesFilterCriteria($profile, $filter->criteria)) {
                return true; // Accept if matches any filter
            }
        }

        return false; // Reject if no filters match
    }
    
    private function getProfileDetails(string $profileId): ?array
    {
        if (empty($profileId)) {
            return null;
        }
        
        try {
            $response = Http::withToken($this->profile->access_token)
                ->get("https://api.linkedin.com/v2/people/(id:{$profileId})", [
                    'projection' => '(id,firstName,lastName,headline,industry,location,positions)'
                ]);
                
            return $response->successful() ? $response->json() : null;
        } catch (\Exception $e) {
            return null;
        }
    }
    
    private function matchesFilterCriteria(array $profile, array $criteria): bool
    {
        foreach ($criteria as $key => $value) {
            if (!$this->checkCriterion($profile, $key, $value)) {
                return false;
            }
        }
        return true;
    }
    
    private function checkCriterion(array $profile, string $key, $value): bool
    {
        return match($key) {
            'industry' => $this->matchesIndustry($profile, $value),
            'location' => $this->matchesLocation($profile, $value),
            'job_title' => $this->matchesJobTitle($profile, $value),
            'company_size' => $this->matchesCompanySize($profile, $value),
            'keywords' => $this->matchesKeywords($profile, $value),
            default => true
        };
    }
    
    private function matchesIndustry(array $profile, $targetIndustry): bool
    {
        $industry = $profile['industry']['name'] ?? '';
        return stripos($industry, $targetIndustry) !== false;
    }
    
    private function matchesLocation(array $profile, $targetLocation): bool
    {
        $location = $profile['location']['name'] ?? '';
        return stripos($location, $targetLocation) !== false;
    }
    
    private function matchesJobTitle(array $profile, $targetTitle): bool
    {
        $headline = $profile['headline'] ?? '';
        $positions = $profile['positions']['elements'] ?? [];
        
        // Check headline
        if (stripos($headline, $targetTitle) !== false) {
            return true;
        }
        
        // Check current position
        foreach ($positions as $position) {
            $title = $position['title'] ?? '';
            if (stripos($title, $targetTitle) !== false) {
                return true;
            }
        }
        
        return false;
    }
    
    private function matchesCompanySize(array $profile, int $minSize): bool
    {
        $positions = $profile['positions']['elements'] ?? [];
        
        foreach ($positions as $position) {
            $companySize = $position['company']['staffCount'] ?? 0;
            if ($companySize >= $minSize) {
                return true;
            }
        }
        
        return false;
    }
    
    private function matchesKeywords(array $profile, array $keywords): bool
    {
        $searchText = strtolower(implode(' ', [
            $profile['headline'] ?? '',
            $profile['firstName'] ?? '',
            $profile['lastName'] ?? '',
        ]));
        
        foreach ($keywords as $keyword) {
            if (stripos($searchText, $keyword) !== false) {
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