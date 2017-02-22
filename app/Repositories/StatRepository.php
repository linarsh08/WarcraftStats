<?php

namespace App\Repositories;

use App\Models\Stat;
use DB;

class StatRepository
{
    protected $creatingQuery = '
insert into 
stats (played_games, wins, most_selectable_race, user_id, created_at, updated_at)
values (:played_games, :wins, :most_selectable_race, :user_id, :created_at, :updated_at)';
    protected $updatingQuery = '
update stats
set played_games = :played_games, wins = :wins, most_selectable_race = :most_selectable_race,
updated_at = :updated_at
where id = :id
';
    protected $paginationQuery = '
select * from stats order by updated_at desc limit :limit offset :offset';
    protected $paginationQueryByUser = '
select * from stats where user_id = :user_id order by updated_at desc limit :limit offset :offset';

    protected $users;

    public function __construct(UserRepository $users)
    {
        $this->users = $users;
    }

    public function create($attributes)
    {
        $tempStat = new Stat($attributes);
        $tempStat->validate();
        $tempStat->cleanup();
        DB::insert($this->creatingQuery, $tempStat->getAttributes());

        $tempStat->id = DB::select('select max(id) from users')[0]->max;

        return $tempStat;
    }

    public function findById($id)
    {
        return stat
::hydrateRaw('select * from stats where id = ?', [intval($id)])->first();
    }

    public function findByIdOrAbort($id)
    {
        $stat = $this->findById($id);

        if (empty($stat)) {
            response_with_errors(404, 'Build order not found')->throwResponse();
        }

        return $stat;
    }

    public function updateById($id, $attributes = [])
    {
        $updatedAttributes = ['id', 'title', 'description', 'playing_race', 'enemy_races', 'updated_at'];
        $stat
 = $this->findByIdOrAbort($id);
        if (!$stat
->isOwner(current_user()->id)) {
            response_with_errors(403, "It isn't your build order")->throwResponse();
        }

        $attributes = array_where($attributes, function ($value, $key) {
            return isset($value);
        });

        $stat
->fill($attributes);
        $stat
->validate();
        $stat
->cleanup();

        DB::update($this->updatingQuery, $stat
->getAttributes($updatedAttributes));

        return $stat
;
    }

    public function deleteById($id)
    {
        $stat
 = $this->findByIdOrAbort($id);
        if (!$stat
->isOwner(current_user()->id)) {
            response_with_errors(403, "It isn't your build order")->throwResponse();
        }
        DB::delete('delete from build_orders where id = ?', [$stat
->id]);

        return $stat
;
    }

    public function deleteByUserId($id)
    {
        $stat
s = DB::select('select id from build_orders where user_id = ?', [intval($id)]);

        foreach ($stat
s as $stat
) {
            DB::delete('delete from build_orders where id = ?', [$stat
->id]);
        }
    }

    public function paginate($currentPage, $perPage, $userId = null)
    {
        $queryInfo = ['limit' => $perPage, 'offset' => (($currentPage - 1) * $perPage)];

        if (isset($userId)) {
            $user = $this->users->findByIdOrAbort($userId);
            $queryInfo['user_id'] = $user->id;

            return stat
::hydrateRaw($this->paginationQueryByUser, $queryInfo)->all();
        }

        return stat
::hydrateRaw($this->paginationQuery, $queryInfo)->all();
    }
}
