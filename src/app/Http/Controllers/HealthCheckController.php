<?php

namespace App\Http\Controllers;

use DB;
use Auth;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class HealthCheckController extends Controller
{
    public function __invoke()
    {
        try {
            DB::connection()->select('SELECT 1');

            return response('OK', 200);
        } catch (\Throwable $exception) {
            return response('Unhealthy', 503);
        }
    }
}
