<?php

namespace App\Livewire;

use App\Models\UserActivity;
use Livewire\Component;

class DashboardActivity extends Component
{
    public $recentActivities = [];

    public function mount()
    {
        $this->loadRecentActivities();
    }

    public function loadRecentActivities()
    {
        $this->recentActivities = UserActivity::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.dashboard-activity');
    }
}