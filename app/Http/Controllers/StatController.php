<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\BuildOrderRepository;

class BuildOrdersController extends Controller
{
    protected $buildOrders;

    public function __construct(BuildOrderRepository $buildOrders)
    {
        $this->buildOrders = $buildOrders;
    }

    public function index()
    {
        return $this->makePaginationResponse($this->buildOrders, 'stats');
    }

    public function indexByUser($user_id)
    {
        return $this->makePaginationResponse($this->buildOrders, 'stats', $user_id);
    }

    public function show($id)
    {
        $buildOrder = $this->buildOrders->findByIdOrAbort($id);

        return success_response(200, ['stat' => $buildOrder]);
    }

    public function create()
    {
        $buildOrder = $this->buildOrders->create($this->buildOrderParams());

        return success_response(200, ['stat' => $buildOrder]);
    }

    public function update($id)
    {
        $buildOrder = $this->buildOrders->updateById($id, $this->buildOrderParams());

        return success_response(200, ['stat' => $buildOrder]);
    }

    public function delete($id)
    {
        $buildOrder = $this->buildOrders->deleteById($id);

        return success_response(200, ['stat' => $buildOrder]);
    }

    protected function buildOrderParams()
    {
        $request = app()->make(Request::class);

        return $request->only('title', 'description', 'playing_race', 'enemy_races');
    }
}
