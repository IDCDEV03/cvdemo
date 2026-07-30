<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response;

class PreventBackHistory
{
  public function handle(Request $request, Closure $next)
{
    $response = $next($request);

    return $response->header('Cache-Control', 'no-store, no-cache, must-revalidate')
                    ->header('Pragma', 'no-cache')
                    ->header('Expires', '0');
}
}
