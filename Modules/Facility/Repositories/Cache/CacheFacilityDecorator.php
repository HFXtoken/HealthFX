<?php

namespace Modules\Facility\Repositories\Cache;

use Modules\Facility\Repositories\FacilityRepository;
use Modules\Core\Repositories\Cache\BaseCacheDecorator;

class CacheFacilityDecorator extends BaseCacheDecorator implements FacilityRepository
{
    public function __construct(FacilityRepository $facility)
    {
        parent::__construct();
        $this->entityName = 'facility.facilities';
        $this->repository = $facility;
    }
}
