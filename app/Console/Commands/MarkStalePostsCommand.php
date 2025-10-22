<?php

namespace App\Console\Commands;

use App\Jobs\MarkStalePostsAsFailed;
use Illuminate\Console\Command;

class MarkStalePostsCommand extends Command
{
    protected $signature = 'posts:mark-stale-failed';
    protected $description = 'Mark posts pending for more than 30 minutes as failed';

    public function handle()
    {
        MarkStalePostsAsFailed::dispatch();
        $this->info('Stale posts marked as failed.');
    }
}