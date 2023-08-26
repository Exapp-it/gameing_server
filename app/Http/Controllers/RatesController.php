<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Jobs\RatesUpdateJob;

class RatesController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        RatesUpdateJob::dispatch();
    }
}
