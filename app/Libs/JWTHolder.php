<?php

namespace App\Libs;

use Lcobucci\JWT\Configuration;
use App\Repositories\UserRepository;
use Carbon\Carbon;

class JWTHolder
{
    protected $config;
    protected $users;

    public function __construct(Configuration $config, UserRepository $users)
    {
        $this->config = $config;
        $this->users = $users;
    }

    public function tokenFromCredentials($credentials)
    {
        $user = $this->users->findByUsername($credentials['username']);

        if (empty($user)) {
            response_with_errors(400, 'Bad credentials')->throwResponse();
        }

        $user->checkPassword($credentials['password']);

        return $this->getToken($user);
    }

    public function authFromToken($token)
    {
        $data = app()->make(\Lcobucci\JWT\ValidationData::class);

        try {
            $parsedToken = $this->config->getParser()->parse($token);

            if ($parsedToken->validate($data) === false) {
                throw new \Exception();
            }

            $user = $this->users->findById($parsedToken->getClaim('uid'));

            if (empty($user)) {
                throw new \Exception();
            }
        } catch (\Exception $e) {
            response_with_errors(403, 'Bad token')->throwResponse();
        }

        return $user;
    }

    protected function getToken($user)
    {
        $hostUrl = env('APP_HOST');

        $token = $this->config->createBuilder()
            ->issuedBy($hostUrl)
            ->canOnlyBeUsedBy($hostUrl)
            ->issuedAt(Carbon::now()->timestamp)
            ->expiresAt(Carbon::now()->addMinutes(env('JWT_TIMING', 3600))->timestamp)
            ->with('uid', $user->id)
            ->getToken();

        return (string) $token;
    }
}
