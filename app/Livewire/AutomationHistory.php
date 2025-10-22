<?php

namespace App\Livewire;

use App\Models\AutomatedPost;
use App\Models\ConnectionFilter;
use Livewire\Component;

class AutomationHistory extends Component
{
    public $recentPosts;
    public $activeFilters;
    public $totalPosts;
    public $totalFilters;

    public function mount()
    {
        $userId = auth()->id();
        
        $this->recentPosts = AutomatedPost::where('user_id', $userId)
            ->latest()
            ->take(3)
            ->get();
            
        $this->activeFilters = ConnectionFilter::where('user_id', $userId)
            ->where('is_active', true)
            ->take(3)
            ->get();
            
        $this->totalPosts = AutomatedPost::where('user_id', $userId)->count();
        $this->totalFilters = ConnectionFilter::where('user_id', $userId)->count();
    }

    public function render()
    {
        return view('livewire.automation-history');
    }
}