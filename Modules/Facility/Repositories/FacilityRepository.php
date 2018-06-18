<?php

namespace Modules\Facility\Repositories;

use Modules\Core\Repositories\BaseRepository;

interface FacilityRepository extends BaseRepository
{
    public function fetchAll ();

    public function fetchFacilitiesForHomePage ();

    public function fetchLocations ();

    public function fetchSpecialities ();

    public function fetchByFilters ($speciality = null, $destination = null, $keyword = null, $country = null);

    public function fetchFacilityDoctors ($id);

    public function fetchFacilitySpecialities ($id);

    public function fetchFacilityBranches ($id);
}
