<?php

namespace App\Console\Commands;

use App\Jobs\GenerateAutomatedPost;
use App\Jobs\ProcessConnectionRequests;
use App\Models\LinkedinProfile;
use Illuminate\Console\Command;

class ProcessLinkedinAutomation extends Command
{
    protected $signature = 'linkedin:process';
    protected $description = 'Process LinkedIn automation tasks';

    public function handle()
    {
        $profiles = LinkedinProfile::whereNotNull('access_token')->get();

        foreach ($profiles as $profile) {
            if ($profile->auto_accept_connections) {
                ProcessConnectionRequests::dispatch($profile);
            }

            if ($profile->post_automation_enabled) {
                GenerateAutomatedPost::dispatch($profile);
            }
        }

        $this->info("Processed {$profiles->count()} LinkedIn profiles");
    }
}