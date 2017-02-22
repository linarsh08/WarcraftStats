<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Repositories\UserRepository;
use App\Libs\JWTHolder;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    protected $users;

    public function __construct(UserRepository $users, JWTHolder $tokenizer)
    {
        $this->users = $users;
    }

    public function index()
    {
        return $this->makePaginationResponse($this->users, 'users');
    }

    public function show($id)
    {
        $user = $this->users->findByIdOrAbort($id);

        return success_response(200, ['user' => $user]);
    }

    public function create()
    {
        $user = $this->users->create($this->userParams());

        return success_response(200, ['user' => $user]);
    }

    public function login()
    {
        $tokenizer = app()->make(JWTHolder::class);
        $token = $tokenizer->tokenFromCredentials($this->userParams());

        return success_response(202, ['token' => $token]);
    }

    public function update(Request $request, $id)
    {
        current_user()->canManipulateUser($id);
        $user = $this->users->updateById($id, $request->input('password'));

        return success_response(200, ['user' => $user]);
    }

    public function delete($id)
    {
        current_user()->canManipulateUser($id);

        $user = $this->users->deleteById($id);

        return success_response(200, ['user' => $user]);
    }

    protected function userParams()
    {
        $request = app()->make(Request::class);

        return $request->only('username', 'password');
    }
}
