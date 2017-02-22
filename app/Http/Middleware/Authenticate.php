<?php

namespace App\Http\Middleware;

use Closure;

class Authenticate
{
    protected $tokenizer;

    public function __construct(\App\Libs\JWTHolder $tokenizer)
    {
        $this->tokenizer = $tokenizer;
    }

    public function handle($request, Closure $next)
    {
        $token = $request->header('Auth');
        if (empty($token)) {
            response_with_errors(401, 'Unauthorized')->throwResponse();
        }

        $user = $this->tokenizer->authFromToken($token);
        $request->attributes->add(['current_user' => $user]);

        return $next($request);
    }
}
