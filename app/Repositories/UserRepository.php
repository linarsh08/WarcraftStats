<?php

namespace App\Repositories;

use App\Models\User;
use DB;

class UserRepository
{
    protected $creatingQuery = '
insert into users (username, password_digest, created_at, updated_at)
values (:username, :password_digest, :created_at, :updated_at)';
    protected $updatingQuery = '
update users set password_digest = :password_digest, updated_at = :updated_at where id = :id';
    protected $paginationQuery = 'select * from users order by id desc limit :limit offset :offset';

    public function create($attributes)
    {
        $tempUser = new User($attributes);
        $tempUser->validate();
        $tempUser->cleanup();
        DB::insert($this->creatingQuery, $tempUser->getAttributes());

        $tempUser->id = DB::select('select max(id) from users')[0]->max;

        return $tempUser;
    }

    public function paginate($currentPage, $perPage)
    {
        $shiftInfo = ['limit' => $perPage, 'offset' => (($currentPage - 1) * $perPage)];

        return User::hydrateRaw($this->paginationQuery, $shiftInfo)->all();
    }

    public function findByUsername($username)
    {
        $username = mb_strtolower($username);

        return User::hydrateRaw('select * from users where username = ?', [$username])->first();
    }

    public function findById($id)
    {
        return User::hydrateRaw('select * from users where id = ?', [(int) $id])->first();
    }

    public function findByIdOrAbort($id)
    {
        $user = $this->findById($id);

        if (empty($user)) {
            response_with_errors(404, 'User not found')->throwResponse();
        }

        return $user;
    }

    public function updateById($id, $password)
    {
        $user = $this->findByIdOrAbort($id);

        $user->password = $password;
        $user->validate(['username']);
        $user->cleanup();

        DB::update($this->updatingQuery, $user->getAttributes(['id', 'password_digest', 'updated_at']));

        return $user;
    }

    public function deleteById($id)
    {
        $user = $this->findByIdOrAbort($id);

        app()->make(BuildOrderRepository::class)->deleteByUserId($user->id);

        DB::delete('delete from users where id = ?', [$user->id]);

        return $user;
    }
}
