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
    public $newFilterCriteria = [];
    
    // Modal state
    public $showPostModal = false;
    
    // Post generation settings
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
        $this->profile->update([
            'auto_accept_connections' => !$this->profile->auto_accept_connections
        ]);
        $this->profile->refresh();
    }

    public function togglePostAutomation()
    {
        $this->profile->update([
            'post_automation_enabled' => !$this->profile->post_automation_enabled
        ]);
        $this->profile->refresh();
    }

    public function processConnections()
    {
        ProcessConnectionRequests::dispatch($this->profile);
        session()->flash('message', 'Processing connection requests...');
    }

    public function generatePost()
    {
        $this->showPostModal = true;
    }
    
    public function savePostSettings()
    {
        $this->validate([
            'postFrequency' => 'required|in:daily,weekly,bi-weekly',
            'speakerVoice' => 'required|in:professional,casual,authoritative,friendly',
            'tone' => 'required|in:informative,inspirational,educational,promotional',
            'diction' => 'required|in:business,technical,conversational,academic',
        ]);
        
        // Save settings and generate post
        GenerateAutomatedPost::dispatch($this->profile, [
            'frequency' => $this->postFrequency,
            'approval_required' => $this->requireApproval,
            'voice' => $this->speakerVoice,
            'themes' => $this->postThemes,
            'tone' => $this->tone,
            'diction' => $this->diction,
        ]);
        
        $this->showPostModal = false;
        session()->flash('message', 'Post generation configured and started!');
    }
    
    public function closeModal()
    {
        $this->showPostModal = false;
    }

    public function createFilter()
    {
        ConnectionFilter::create([
            'user_id' => auth()->id(),
            'name' => $this->newFilterName,
            'criteria' => $this->newFilterCriteria,
        ]);

        $this->reset(['newFilterName', 'newFilterCriteria']);
        $this->loadData();
    }

    public function deleteFilter($filterId)
    {
        ConnectionFilter::where('id', $filterId)
            ->where('user_id', auth()->id())
            ->delete();
        $this->loadData();
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