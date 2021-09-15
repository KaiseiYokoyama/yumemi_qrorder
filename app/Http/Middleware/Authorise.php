<?php

namespace App\Http\Middleware;

use App\Models\Party;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Authorise
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $session_secret = $request->cookie('session_secret');
        $party = Party::query()
            ->where('uuid', $session_secret)
            ->first();

        if ($party == null) {
            throw new HttpException(Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
