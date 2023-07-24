<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;

class ApiDataLogger
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
        $dataLog = [
            'ip' => $request->ip(),
            'method' => $request->method(),
            'route' => $request->path(),
        ];

        /** @todo solucao provisoria para o caso de acessos indesejaveis */
        // $lock = [
        //     '127.0.0.1',
        // ];

        // if (in_array($dataLog['ip'], $lock)) {
        //     return;
        // }

        Log::info(json_encode($dataLog));
        return $next($request);
    }
}
