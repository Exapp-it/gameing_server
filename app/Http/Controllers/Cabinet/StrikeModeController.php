<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Strike;
use App\Http\Resources\StrikeResource;

class StrikeModeController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $user = $request->user();
        $strikes = Strike
                    ::where('user_id', '=', $user->id)
                    ->orderBy('date', 'desc')
                    ->get();

        $amount = 0;
        $prev = date_create(date("y-m-d"));

        foreach ($strikes as $strike) {
            $curr = date_create($strike['date']);

            $diff = intval(date_diff($curr, $prev)->format("%a"));
            if ($diff > 1) {
                break;
            }

            $amount++;
            $prev = $curr;
        }
        

        $max_key = count($strikes);
        for ($idx = $amount; $idx < $max_key; $idx++) {
            $strikes[$idx]->delete();
            unset($strikes[$idx]);
        }

        return StrikeResource::collection($strikes);
    }
}
