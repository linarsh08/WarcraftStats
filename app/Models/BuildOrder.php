<?php

namespace App\Models;

class BuildOrder extends Model
{
    protected $fillable = ['title', 'description', 'playing_race', 'enemy_races'];
    protected $hidden = ['user_id'];
    protected $appends = ['parsed_description'];

    protected $rules = [
        'title' => 'required|string|min:4|max:20',
        'description' => 'required|string|min:50',
        'playing_race' => 'required|string|true_race',
        'enemy_races' => 'required|json|true_races',
        'user_id' => 'required|integer|exists:users,id',
    ];

    public function getParsedDescriptionAttribute()
    {
        $parser = app()->make(\cebe\markdown\GithubMarkdown::class);

        return $parser->parse($this->attributes['description']);
    }

    public function setEnemyRacesAttribute($value)
    {
        $this->attributes['enemy_races'] = json_encode($value);
    }

    public function cleanup()
    {
        $this->updateTimestamps();
    }

    public function isOwner($userId)
    {
        return $this->user_id === intval($userId);
    }
}
