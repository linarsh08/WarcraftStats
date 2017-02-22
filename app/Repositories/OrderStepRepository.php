<?php

namespace App\Repositories;

class OrderStepRepository
{
    private $creationQuery = '
insert into
build_steps (description, timing, build_order_id, created_at, updated_at)
values (:description, :timing, :build_order_id, :created_at, :updated_at)';
    private $updatingQuery = '';
}
