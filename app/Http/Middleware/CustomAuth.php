<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Closure;

class CustomAuth
{

	 /**  CustomAuth.php
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		if ($request->session()->has('username') && $request->session()->has('password')) {
			return $next($request);
		}
		return redirect('/')->withError(['Please login with your credential']);
	}
}
