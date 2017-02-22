<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    const START_PAGE = 1;
    const PER_PAGE = 10;

    protected function makePaginationResponse($modelRepository, $dataHolderName, $owner_id = null)
    {
        $requestInput = app()->make(\Illuminate\Http\Request::class)->input();

        $currentPage = (int) array_get($requestInput, 'page.number', self::START_PAGE);
        $perPage = (int) array_get($requestInput, 'page.size', self::PER_PAGE);

        if ($currentPage < self::START_PAGE) {
            $currentPage = self::START_PAGE;
        }

        if ($perPage < 1) {
            $perPage = self::PER_PAGE;
        }
        if (isset($owner_id)) {
            $paginatedResults = $modelRepository->paginate($currentPage, $perPage, $owner_id);
        } else {
            $paginatedResults = $modelRepository->paginate($currentPage, $perPage);
        }

        return success_response(200, [$dataHolderName => $paginatedResults]);
    }
}
