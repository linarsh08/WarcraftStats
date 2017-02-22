<?php

namespace App\Repositories;

use App\Models\BuildOrder;
use DB;

class BuildOrderRepository
{
    protected $creatingQuery = '
insert into 
build_orders (title, description, playing_race, enemy_races, user_id, created_at, updated_at)
values (:title, :description, :playing_race, :enemy_races, :user_id, :created_at, :updated_at)';
    protected $updatingQuery = '
update build_orders
set title = :title, description = :description, playing_race = :playing_race, enemy_races = :enemy_races,
updated_at = :updated_at
where id = :id
';
    protected $paginationQuery = '
select * from build_orders order by updated_at desc limit :limit offset :offset';
    protected $paginationQueryByUser = '
select * from build_orders where user_id = :user_id order by updated_at desc limit :limit offset :offset';

    protected $users;

    public function __construct(UserRepository $users)
    {
        $this->users = $users;
    }

    public function create($attributes)
    {
        $tempBuild = new BuildOrder($attributes);
        $tempBuild->user_id = current_user()->id;
        $tempBuild->validate();
        $tempBuild->cleanup();
        DB::insert($this->creatingQuery, $tempBuild->getAttributes());

        $tempBuild->id = DB::select('select max(id) from build_orders')[0]->max;

        return $tempBuild;
    }

    public function findById($id)
    {
        return BuildOrder::hydrateRaw('select * from build_orders where id = ?', [intval($id)])->first();
    }

    public function findByIdOrAbort($id)
    {
        $buildOrder = $this->findById($id);

        if (empty($buildOrder)) {
            response_with_errors(404, 'Build order not found')->throwResponse();
        }

        return $buildOrder;
    }

    public function updateById($id, $attributes = [])
    {
        $updatedAttributes = ['id', 'title', 'description', 'playing_race', 'enemy_races', 'updated_at'];
        $buildOrder = $this->findByIdOrAbort($id);
        if (!$buildOrder->isOwner(current_user()->id)) {
            response_with_errors(403, "It isn't your build order")->throwResponse();
        }

        $attributes = array_where($attributes, function ($value, $key) {
            return isset($value);
        });

        $buildOrder->fill($attributes);
        $buildOrder->validate();
        $buildOrder->cleanup();

        DB::update($this->updatingQuery, $buildOrder->getAttributes($updatedAttributes));

        return $buildOrder;
    }

    public function deleteById($id)
    {
        $buildOrder = $this->findByIdOrAbort($id);
        if (!$buildOrder->isOwner(current_user()->id)) {
            response_with_errors(403, "It isn't your build order")->throwResponse();
        }
        DB::delete('delete from build_orders where id = ?', [$buildOrder->id]);

        return $buildOrder;
    }

    public function deleteByUserId($id)
    {
        $buildOrders = DB::select('select id from build_orders where user_id = ?', [intval($id)]);

        foreach ($buildOrders as $buildOrder) {
            DB::delete('delete from build_orders where id = ?', [$buildOrder->id]);
        }
    }

    public function paginate($currentPage, $perPage, $userId = null)
    {
        $queryInfo = ['limit' => $perPage, 'offset' => (($currentPage - 1) * $perPage)];

        if (isset($userId)) {
            $user = $this->users->findByIdOrAbort($userId);
            $queryInfo['user_id'] = $user->id;

            return BuildOrder::hydrateRaw($this->paginationQueryByUser, $queryInfo)->all();
        }

        return BuildOrder::hydrateRaw($this->paginationQuery, $queryInfo)->all();
    }
}
