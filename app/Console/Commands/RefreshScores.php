<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class RefreshScores extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:refresh-scores';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh Scores of the players';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::all();

        $users->each(function (User $user) {
            $this->info("Refreshing score of {$user->name}");
            $this->info("Old score: {$user->score} | New score: {$user->refreshScore()}");
        });
    }
}
