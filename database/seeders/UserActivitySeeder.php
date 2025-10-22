<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserActivity;
use Illuminate\Database\Seeder;

class UserActivitySeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        
        foreach ($users as $user) {
            // Add some sample activities for existing users
            UserActivity::create([
                'user_id' => $user->id,
                'action' => 'login',
                'description' => 'Logged into the platform',
                'created_at' => now()->subDays(2),
            ]);
            
            UserActivity::create([
                'user_id' => $user->id,
                'action' => 'page_visit',
                'description' => 'Visited main dashboard',
                'metadata' => ['route' => 'dashboard'],
                'created_at' => now()->subDays(1),
            ]);
            
            UserActivity::create([
                'user_id' => $user->id,
                'action' => 'page_visit',
                'description' => 'Accessed LinkedIn automation',
                'metadata' => ['route' => 'linkedin.dashboard'],
                'created_at' => now()->subHours(6),
            ]);
        }
    }
}