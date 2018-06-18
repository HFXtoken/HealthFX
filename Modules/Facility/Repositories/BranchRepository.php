<?php

namespace Modules\Facility\Repositories;

use Modules\Core\Repositories\BaseRepository;

interface BranchRepository extends BaseRepository
{
    public function findByName($keyword);
}
