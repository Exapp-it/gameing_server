<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Carbon\Carbon;
use App\Models\User;

class MetricsController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $start_day = Carbon::now()->startOfDay();
        $end_day = Carbon::now()->endOfDay();

        $reg_today = User::whereBetween(
            'created_at', 
            [
                $start_day,
                $end_day
            ]
        )->count();

        return [
            "reg_today" => $reg_today,
            "dep_amount" => 87,
            "dep_sum" => 5712,
            "first_dep_amount" => 45,
            "first_dep_sum" => 2395,
            "first_dep_avg" => 256,
            "repeat_dep_amount" => 7,
            "repeat_dep_sum" => 872,
            "repeat_dep_avg" => 121,
            "users_win" => 3916,
            "users_lose" => 1211,
            "diff_win_lose" => 3916 - 1211
        ];
    }
}
