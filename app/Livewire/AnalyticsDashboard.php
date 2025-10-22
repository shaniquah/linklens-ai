<?php

namespace App\Livewire;

use App\Models\LinkedinProfile;
use Illuminate\Support\Facades\Http;
use Livewire\Component;

class AnalyticsDashboard extends Component
{
    public $timeRange = '30';
    public $profileAnalytics = [];
    public $postAnalytics = [];
    public $audienceAnalytics = [];
    public $loading = false;
    public $error = null;

    public function mount()
    {
        $this->loadLinkedInAnalytics();
    }

    public function updatedTimeRange()
    {
        $this->loadLinkedInAnalytics();
    }

    public function loadLinkedInAnalytics()
    {
        $this->loading = true;
        $this->error = null;
        
        $profile = auth()->user()->linkedinProfile;
        
        if (!$profile || !$profile->access_token) {
            $this->error = 'LinkedIn profile not connected';
            $this->loading = false;
            return;
        }
        
        try {
            $this->fetchProfileAnalytics($profile);
            $this->fetchPostAnalytics($profile);
            $this->fetchAudienceAnalytics($profile);
        } catch (\Exception $e) {
            $this->error = 'Failed to fetch LinkedIn analytics';
        }
        
        $this->loading = false;
    }
    
    private function fetchProfileAnalytics($profile)
    {
        $response = Http::withToken($profile->access_token)
            ->get('https://api.linkedin.com/v2/networkSizes/urn:li:person:' . $profile->linkedin_id);
            
        if ($response->successful()) {
            $this->profileAnalytics = $response->json();
        }
    }
    
    private function fetchPostAnalytics($profile)
    {
        $response = Http::withToken($profile->access_token)
            ->get('https://api.linkedin.com/v2/shares', [
                'q' => 'owners',
                'owners' => 'urn:li:person:' . $profile->linkedin_id,
                'sharesPerOwner' => 50
            ]);
            
        if ($response->successful()) {
            $this->postAnalytics = $response->json();
        }
    }
    
    private function fetchAudienceAnalytics($profile)
    {
        $response = Http::withToken($profile->access_token)
            ->get('https://api.linkedin.com/v2/people/(id:' . $profile->linkedin_id . ')');
            
        if ($response->successful()) {
            $this->audienceAnalytics = $response->json();
        }
    }

    public function render()
    {
        return view('livewire.analytics-dashboard')
            ->layout('analytics');
    }
}