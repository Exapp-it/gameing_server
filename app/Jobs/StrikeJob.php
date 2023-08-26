<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\Strike;

class StrikeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $args;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($args)
    {
        $this->args = $args;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $user = $this->args['user'];
        $today = date("Y-m-d");
        $strike = Strike
                    ::where('user_id', '=', $user->id)
                    ->whereDate('date', '=', $today)
                    ->first();
        if (!$strike) {
            $strike = Strike::create([
                'user_id' => $user->id,
                'date'    => $today
            ]);
            $strike->save();
        }
    }
}
