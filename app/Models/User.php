<?php

namespace App\Models;

class User extends Model
{
    protected $fillable = ['username', 'password'];
    protected $hidden = ['password_digest'];

    protected $rules = [
        'username' => 'required|string|min:4|max:20|unique:users',
        'password' => 'required|string|min:4',
    ];

    public function setUsernameAttribute($value)
    {
        $this->attributes['username'] = mb_strtolower($value);
    }

    public function cleanup()
    {
        $this->hashPassword();
        $this->updateTimestamps();
        unset($this->attributes['password']);
    }

    public function checkPassword($password)
    {
        if (!password_verify($password, $this->password_digest)) {
            response_with_errors(400, 'Bad credentials')->throwResponse();
        }
    }

    public function canManipulateUser($editedUserId)
    {
        if ($this->id !== intval($editedUserId)) {
            response_with_errors(403, "You can't edit this user")->throwResponse();
        }
    }

    protected function hashPassword()
    {
        $this->password_digest = password_hash($this->password, PASSWORD_DEFAULT);
    }
}
