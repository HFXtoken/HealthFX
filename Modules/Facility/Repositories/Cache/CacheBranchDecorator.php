<?php

namespace Modules\Facility\Repositories\Cache;

use Modules\Facility\Repositories\BranchRepository;
use Modules\Core\Repositories\Cache\BaseCacheDecorator;

class CacheBranchDecorator extends BaseCacheDecorator implements BranchRepository
{
    public function __construct(BranchRepository $branch)
    {
        parent::__construct();
        $this->entityName = 'facility.branches';
        $this->repository = $branch;
    }
}
