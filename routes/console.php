<?php

use Illuminate\Foundation\Inspiring;
use App\Http\Controllers\CatchController;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command(
    'order:process  {output=catch} {type=csv}', 
    function ($output, $type) {
        $catch = new CatchController;
        $catch->processOrder($output, $type);
    }
)->describe('Process Order');