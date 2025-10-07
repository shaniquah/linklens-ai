<?php

namespace App\Livewire;

use App\Jobs\GenerateAutomatedPost;
use App\Jobs\ProcessConnectionRequests;
use App\Models\AutomatedPost;
use App\Models\ConnectionFilter;
use App\Models\LinkedinProfile;
use Livewire\Component;

class LinkedinDashboard extends Component
{
    public $profile;
    public $filters;
    public $recentPosts;
    public $newFilterName = '';
    public $newFilterCriteria = [];

    public function mount()
    {
        $this->profile = auth()->user()->linkedinProfile;
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
        GenerateAutomatedPost::dispatch($this->profile);
        session()->flash('message', 'Generating new post...');
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

    public function render()
    {
        return view('livewire.linkedin-dashboard');
    }
}