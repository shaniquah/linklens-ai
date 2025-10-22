<?php

namespace App\Livewire;

use App\Jobs\GenerateAutomatedPost;
use App\Jobs\ProcessConnectionRequests;
use App\Models\AutomatedPost;
use App\Models\ConnectionFilter;
use App\Models\LinkedinProfile;
use Illuminate\Support\Facades\Http;
use Livewire\Component;

class LinkedinDashboard extends Component
{
    public $profile;
    public $filters;
    public $recentPosts;
    public $newFilterName = '';
    public $filterIndustry = '';
    public $filterLocation = '';
    public $filterJobTitle = '';
    public $filterCompanySize = '';
    public $filterKeywords = [];
    public $showOtherKeyword = false;
    public $customKeyword = '';
    
    // Dropdown options
    public $industries = [];
    public $locations = [];
    public $jobTitles = [];
    public $availableKeywords = [];
    
    // Modal state
    public $showPostModal = false;
    
    // Post generation settings
    public $postType = 'medium';
    public $postFrequency = 'daily';
    public $requireApproval = true;
    public $speakerVoice = 'professional';
    public $postThemes = [];
    public $tone = 'informative';
    public $diction = 'business';

    public function mount()
    {
        $this->profile = auth()->user()->linkedinProfile()->first();
        $this->loadData();
        $this->loadLinkedInOptions();
    }
    
    public function updatedProfile()
    {
        // This ensures the UI stays in sync when profile is updated
        $this->profile->refresh();
    }
    
    public function loadLinkedInOptions()
    {
        if ($this->profile) {
            $this->industries = $this->fetchIndustries();
            $this->locations = $this->fetchLocations();
            $this->jobTitles = $this->fetchJobTitles();
        } else {
            // Fallback static data
            $this->industries = ['Technology', 'Healthcare', 'Finance', 'Education', 'Manufacturing'];
            $this->locations = ['San Francisco, CA', 'New York, NY', 'Los Angeles, CA', 'Remote'];
            $this->jobTitles = ['Software Engineer', 'Product Manager', 'Data Scientist', 'Designer'];
        }
        
        $this->availableKeywords = [
            'AI', 'Machine Learning', 'Cloud', 'AWS', 'React', 'Python', 'JavaScript',
            'Leadership', 'Strategy', 'Innovation', 'Startup', 'Enterprise',
            'Digital Transformation', 'Agile', 'DevOps', 'Analytics', 'SaaS'
        ];
    }
    
    private function fetchIndustries()
    {
        try {
            $response = Http::withToken($this->profile->access_token)
                ->get('https://api.linkedin.com/v2/industries');
            
            if ($response->successful()) {
                return collect($response->json()['elements'] ?? [])
                    ->pluck('localizedName')
                    ->toArray();
            }
        } catch (\Exception $e) {}
        
        return ['Technology', 'Healthcare', 'Finance', 'Education', 'Manufacturing', 'Retail', 'Consulting'];
    }
    
    private function fetchLocations()
    {
        try {
            $response = Http::withToken($this->profile->access_token)
                ->get('https://api.linkedin.com/v2/locations', [
                    'q' => 'typeahead',
                    'text' => 'United States'
                ]);
            
            if ($response->successful()) {
                return collect($response->json()['elements'] ?? [])
                    ->pluck('displayName')
                    ->toArray();
            }
        } catch (\Exception $e) {}
        
        return ['San Francisco, CA', 'New York, NY', 'Los Angeles, CA', 'Chicago, IL', 'Remote'];
    }
    
    private function fetchJobTitles()
    {
        try {
            $response = Http::withToken($this->profile->access_token)
                ->get('https://api.linkedin.com/v2/titles', [
                    'q' => 'typeahead',
                    'text' => 'Software'
                ]);
            
            if ($response->successful()) {
                return collect($response->json()['elements'] ?? [])
                    ->pluck('localizedName')
                    ->toArray();
            }
        } catch (\Exception $e) {}
        
        return ['Software Engineer', 'Product Manager', 'Data Scientist', 'Designer', 'Marketing Manager'];
    }

    public function loadData()
    {
        $this->filters = ConnectionFilter::where('user_id', auth()->id())->get();
        $this->recentPosts = AutomatedPost::where('user_id', auth()->id())
            ->latest()
            ->take(5)
            ->get();
    }

    public function toggleAutoAccept()
    {
        if (!$this->profile) {
            return;
        }
        
        $this->profile->update([
            'auto_accept_connections' => !$this->profile->auto_accept_connections
        ]);
        $this->profile->refresh();
        
        $status = $this->profile->auto_accept_connections ? 'enabled' : 'disabled';
        auth()->user()->logActivity('setting_changed', "Auto-accept connections {$status}", [
            'setting' => 'auto_accept_connections',
            'value' => $this->profile->auto_accept_connections
        ]);
        session()->flash('message', "Auto-accept connections {$status}.");
    }

    public function togglePostAutomation()
    {
        if (!$this->profile) {
            return;
        }
        
        $this->profile->update([
            'post_automation_enabled' => !$this->profile->post_automation_enabled
        ]);
        $this->profile->refresh();
        
        $status = $this->profile->post_automation_enabled ? 'enabled' : 'disabled';
        auth()->user()->logActivity('setting_changed', "Post automation {$status}", [
            'setting' => 'post_automation_enabled',
            'value' => $this->profile->post_automation_enabled
        ]);
        session()->flash('message', "Post automation {$status}.");
    }

    public function processConnections()
    {
        ProcessConnectionRequests::dispatch($this->profile);
        auth()->user()->logActivity('connection_processing', 'Started processing connection requests');
        session()->flash('message', 'Processing connection requests...');
    }

    public function generatePost()
    {
        $this->showPostModal = true;
    }
    
    public function savePostSettings()
    {
        $this->validate([
            'postType' => 'required|in:short,medium,long',
            'postFrequency' => 'required|in:daily,weekly,bi-weekly',
            'speakerVoice' => 'required|in:professional,casual,authoritative,friendly',
            'tone' => 'required|in:informative,inspirational,educational,promotional',
            'diction' => 'required|in:business,technical,conversational,academic',
        ]);
        
        // Save settings and generate post
        GenerateAutomatedPost::dispatch($this->profile, [
            'type' => $this->postType,
            'frequency' => $this->postFrequency,
            'approval_required' => $this->requireApproval,
            'voice' => $this->speakerVoice,
            'themes' => $this->postThemes,
            'tone' => $this->tone,
            'diction' => $this->diction,
        ]);
        
        auth()->user()->logActivity('post_created', 'Generated new automated post', [
            'type' => $this->postType,
            'frequency' => $this->postFrequency,
            'voice' => $this->speakerVoice
        ]);
        
        $this->showPostModal = false;
        session()->flash('message', 'Post generation configured and started!');
    }
    
    public function closeModal()
    {
        $this->showPostModal = false;
    }

    public function toggleOtherKeyword()
    {
        $this->showOtherKeyword = !$this->showOtherKeyword;
        if (!$this->showOtherKeyword) {
            $this->customKeyword = '';
        }
    }
    
    public function addCustomKeyword()
    {
        if (!empty($this->customKeyword)) {
            $this->filterKeywords[] = $this->customKeyword;
            $this->customKeyword = '';
            $this->showOtherKeyword = false;
        }
    }

    public function createFilter()
    {
        if (empty($this->newFilterName)) {
            return;
        }
        
        $criteria = [];
        
        if (!empty($this->filterIndustry)) {
            $criteria['industry'] = $this->filterIndustry;
        }
        
        if (!empty($this->filterLocation)) {
            $criteria['location'] = $this->filterLocation;
        }
        
        if (!empty($this->filterJobTitle)) {
            $criteria['job_title'] = $this->filterJobTitle;
        }
        
        if (!empty($this->filterCompanySize)) {
            $criteria['company_size'] = (int) $this->filterCompanySize;
        }
        
        if (!empty($this->filterKeywords)) {
            $criteria['keywords'] = $this->filterKeywords;
        }

        ConnectionFilter::create([
            'user_id' => auth()->id(),
            'name' => $this->newFilterName,
            'criteria' => $criteria,
        ]);
        
        auth()->user()->logActivity('filter_created', "Created connection filter: {$this->newFilterName}", [
            'filter_name' => $this->newFilterName,
            'criteria' => $criteria
        ]);

        $this->reset(['newFilterName', 'filterIndustry', 'filterLocation', 'filterJobTitle', 'filterCompanySize', 'showOtherKeyword', 'customKeyword']);
        $this->filterKeywords = [];
        $this->loadData();
    }

    public function deleteFilter($filterId)
    {
        ConnectionFilter::where('id', $filterId)
            ->where('user_id', auth()->id())
            ->delete();
        $this->loadData();
    }
    
    public function toggleFilter($filterId)
    {
        $filter = ConnectionFilter::where('id', $filterId)
            ->where('user_id', auth()->id())
            ->first();
            
        if ($filter) {
            $filter->update(['is_active' => !$filter->is_active]);
            $this->loadData();
        }
    }

    public function retryPost($postId)
    {
        $post = AutomatedPost::where('id', $postId)
            ->where('user_id', auth()->id())
            ->where('status', 'failed')
            ->first();
            
        if ($post) {
            $response = Http::withToken($this->profile->access_token)
                ->post('https://api.linkedin.com/v2/ugcPosts', [
                    'author' => "urn:li:person:{$this->profile->linkedin_id}",
                    'lifecycleState' => 'PUBLISHED',
                    'specificContent' => [
                        'com.linkedin.ugc.ShareContent' => [
                            'shareCommentary' => ['text' => $post->content],
                            'shareMediaCategory' => 'NONE',
                        ],
                    ],
                    'visibility' => ['com.linkedin.ugc.MemberNetworkVisibility' => 'PUBLIC'],
                ]);

            if ($response->successful()) {
                $post->markAsPosted();
                session()->flash('message', 'Post published successfully!');
            } else {
                session()->flash('message', 'Failed to publish post. Please try again.');
            }
            
            $this->loadData();
        }
    }
    
    public function addNewPost($post)
    {
        $this->recentPosts->prepend((object) $post['post']);
        $this->recentPosts = $this->recentPosts->take(5);
    }
    
    public function render()
    {
        return view('livewire.linkedin-dashboard');
    }
}