<?php

namespace App\Http\Middleware;

use Closure;

class EnsureClientAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
      $access = app('App\Http\Controllers\UserController')->checkAccessControl();
      if(!$access)
      {
        return redirect('no_access');
      }
      else
      {
        return $next($request);
      }
    }
}
