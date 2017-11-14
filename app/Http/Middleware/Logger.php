<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Log;

class Logger
{
    public function handle($request, Closure $next)
    {
        return $next($request);
    }

    public function terminate($request, $response)
    {
        if ($request->getMethod() == 'OPTIONS') {
            return false;
        }

        $requestData = [
            'method' => $request->getMethod(),
            'fullUrl' => $request->fullUrl(),
            'path' => $request->path(),
            'data' => $request->all(),
            'ip' => $request->ip(),
            'token' => $request->server('HTTP_AUTHORIZATION'),
            'user_agent' => $request->server('HTTP_USER_AGENT'),
        ];

        $responseData = [
            'statusCode' => $response->getStatusCode(),
            'content' => $response->getContent(),
            'headers' => $response->headers->all(),
            'date' => $response->getDate(),
        ];

        Log::create([
            'request' => $requestData,
            'response' => $responseData
        ]);
    }
}