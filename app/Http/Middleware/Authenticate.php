<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Support\Facades\Log;

class Authenticate
{
    /**
     * The authentication guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param \Illuminate\Contracts\Auth\Factory $auth
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string|null $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        // check access token
        if ($this->auth->guard($guard)->guest()) {
            $errorMessage = [
                'message' => 'unauthorized',
                'url' => $request->fullUrl(),
                'header' => $request->header(),
                'request' => $request->toArray(),
            ];

            Log::emergency($errorMessage);

            return response()->json(['error' => 'acesso negado'], 401);
        }

        return $next($request);
    }
}
