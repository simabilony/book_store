<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SQLInjection
{
    /**
     * Handle an incoming request.
     *
     * param Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)
     *
     * $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $suspiciousPatterns = [
            '/(\%27)|(\')|(\-\-)|(\%23)|(#)/i',
            '/((\%3D)|(=)) [^\n]*((\%27)|(\')|(\-\-)|(\%3B)|(;))/1',
            '/\b(select|update|insert|delete|drop|alter|create|truncate)\b/i'
        ];

        $input = $request->all();
        foreach ($input as $key => $value) {
            foreach ($suspiciousPatterns as $pattern) {
                if (preg_match($pattern, $value)) {
                    Log::warning('SQL Injection attempt detected.', [
                        'ip' => $request->ip(),

                        'url' => $request->fullUrl(),

                        'input' => $input,

                    ]);

                    return response('Suspicious activity detected.', 403);
                }
            }
        }
        return $next($request);
    }
}
