<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Services\TomHorn\Client as TomHornClient;
use App\Services\Mancala\Client as MancalaClient;
use App\Services\B2bslots\Client as B2bslotsClient;

class GamesInfoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $client = app()->make(TomHornClient::class);
        $games = $client->getGameModules();
        foreach ($games as $game) {
            $game->saveToDB();
        }

        $client = app()->make(MancalaClient::class);
        $games = $client->GetAvailableGames();
        foreach ($games as $game) {
            $game->saveToDB();
        }

        $client = app()->make(B2bslotsClient::class);
        $games = $client->getGames();
        foreach ($games as $game) {
            $game->saveToDB();
        }
    }
}
