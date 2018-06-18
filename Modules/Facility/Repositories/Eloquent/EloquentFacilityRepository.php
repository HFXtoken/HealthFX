<?php

namespace Modules\Facility\Repositories\Eloquent;

use App\Http\Helpers\ApplicationHelper;
use Modules\Article\Entities\Article;
use Modules\Configuration\Repositories\LocationRepository;
use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;
use Modules\Doctor\Entities\Doctor;
use Modules\Facility\Events\FacilityWasCreated;
use Modules\Facility\Events\FacilityWasDeleted;
use Modules\Facility\Events\FacilityWasUpdated;
use Modules\Facility\Repositories\FacilityRepository;
use Modules\Package\Entities\Package;

class EloquentFacilityRepository extends EloquentBaseRepository implements FacilityRepository
{
    public function create($data)
    {
        $facility = $this->model->create($data);
        $facility->setTags(array_get($data, 'tags', []));
        event(new FacilityWasCreated($facility, $data));

        return $facility;
    }

    public function update($facility, $data)
    {
        $facility->update($data);
        $facility->setTags(array_get($data, 'tags', []));
        event(new FacilityWasUpdated($facility, $data));

        return $facility;
    }

    public function destroy($facility)
    {
        $facility->untag();
        event(new FacilityWasDeleted($facility));
        return $facility->delete();
    }

    // Fetch all facilities for facility list page
    public function fetchAll()
    {
        $facilities = $this->model->with('branches.availableDays', 'branches.doctors', 'primaryBranch', 'otherBranches')
                        ->where('status_id', '=', 1)->paginate(5);
        foreach ($facilities as $key => $facility) {
            $linked_doctors = self::linkedDoctors($facility->id);
            $facility->linked_doctors = Doctor::whereIn('id', array_pluck($linked_doctors, 'id'))->limit(3)->get();

            $linked_packages = self::linkedPackages($facility->id);
            $facility->linked_packages = Package::whereIn('id', array_pluck($linked_packages, 'id'))->limit(2)->get();

            $facility->primaryBranch[0]->branch_timings = ApplicationHelper::consolidateTimings($facility->primaryBranch[0]->timings);
        }
        
        $results = array(
            'search_result' => $facilities
        );

        return $results;
    }

    // Fetch facility details for detail page by slug
    public function findBySlug($slug)
    {
        $facilityDetails = $this->model->with('branches.availableDays', 'branches.doctors', 'departments')
            ->where('slug', $slug)->get();

        foreach ($facilityDetails as $key => $facilityDetail) {
            $linked_doctors = self::linkedDoctors($facilityDetail->id);
            $facilityDetail->linked_doctors = Doctor::whereIn('id', array_pluck($linked_doctors, 'id'))->get();


            $linked_packages = self::linkedPackages($facilityDetail->id);
            $facilityDetail->linked_packages = Package::whereIn('id', array_pluck($linked_packages, 'id'))->get();


            $linked_articles = self::linkedArticles($facilityDetail->id);
            $facilityDetail->linked_articles = Article::whereIn('id', array_pluck($linked_articles, 'id'))->get();

            $facilityDetail->linked_branches = self::linkedBranches($facilityDetail->id);

            foreach ($facilityDetail->linked_branches->branches as $key => $branch) {
                $facilityDetail->linked_branches->branches[$key]->branch_timings = ApplicationHelper::consolidateTimings($branch->timings);
            }
        }

        return $facilityDetails;
    }

    public function fetchFacilitiesForHomePage()
    {
        return $this->model->where('status_id', '=', 1)->latest()->take(8)->get();
    }

    // Fetch countries of published facilities
    public function fetchDestinations()
    {
        $facilities = $this->model->where('status_id', 1)->get();
        return array_pluck($facilities, 'location.country','location.country');
    }

    // Fetch cities of published facilities
    public function fetchLocations()
    {
        $facilities = $this->model->where('status_id', 1)->get();
        return array_pluck($facilities, 'location.city_name', 'location.slug');
    }

    // Fetch specialities of published facilities
    public function fetchSpecialities()
    {
        $facilities = $this->model->with('branches.specialities')->where('status_id', 1)->get();
        return array_pluck(array_collapse(array_pluck(array_collapse(array_pluck($facilities, 'branches')), 'specialities')), 'name', 'slug');
    }

    public function fetchByFilters($speciality = null, $destination = null, $location = null, $keyword = null)
    {
        $Builder = $this->model->with('branches.specialities', 'branches.availableDays', 'branches.doctors','primaryBranch', 'otherBranches');
        if($speciality != null) {
            $Builder->whereHas('branches.specialities', function($q) use($speciality) {
                $q->where('slug', $speciality);
            });
        }
        if($destination != null) {
//            if( $country != null && $country === 'true') {
//                $location_repo = app()->make(LocationRepository::class);
//                $location_entity = $location_repo->findBySlug($destination);
//                $Builder->whereHas('location', function($q) use($location_entity) {
//                    $q->whereTranslation('country', $location_entity->country);
//                });
//            } else {
//                $Builder->whereHas('location', function($q) use($destination) {
//                    $q->where('slug', $destination);
//                });
//                $Builder->whereHas('location', function($q) use($destination) {
//                    $q->whereTranslation('country', $destination);
//                });
//            }

            $Builder->whereHas('location', function($q) use($destination) {
                $q->whereTranslation('country', $destination);
            });

            if($location != null) {
                $Builder->whereHas('location', function($q) use($location) {
                    $q->where('slug', $location);
                });
            }
        }
        if($keyword != null) {
            $Builder->where(function($query) use ($keyword){
                $query
                    ->whereTranslationILike('name', '%'.$keyword.'%');
            });
        }
        $Builder->where('status_id', 1);

        $facilities = $Builder->latest()->paginate(5);

        foreach ($facilities as $key => $facility) {
            $linked_doctors = self::linkedDoctors($facility->id);

            $Builder = Doctor::whereIn('id', array_pluck($linked_doctors, 'id'));
            if($speciality != null) {
                $Builder->whereHas('primarySpeciality', function($q) use($speciality) {
                    $q->where('slug', $speciality);
                });
            }
            $facility->linked_doctors = $Builder->limit(3)->get();

            $linked_packages = self::linkedPackages($facility->id);

            $facility->linked_packages = Package::whereIn('id', array_pluck($linked_packages, 'id'))->limit(2)->get();

            $facility->primaryBranch[0]->branch_timings = ApplicationHelper::consolidateTimings($facility->primaryBranch[0]->timings);
        }

        $destinations = array_pluck($facilities, 'location.country', 'location.country');

        $locations = array_pluck($facilities, 'location.city_name', 'location.slug');

        $specialities = array_pluck(array_collapse(array_pluck(array_collapse(array_pluck($facilities, 'branches')), 'specialities')), 'name', 'slug');

        $results = array(
            'search_result' => $facilities,
            'search_destinations' => $destinations,
            'search_locations' => $locations,
            'search_specialities' => $specialities,
            'keyword' => $keyword
        );

        return $results;
    }

    public function fetchFacilityDoctors ($id)
    {
        $linked_doctors = Doctor::whereIn('id', array_pluck(self::linkedDoctors($id), 'id'))->get()->toArray();
        $dd_array = [];
        foreach ($linked_doctors as $doctor) {
            $dd_array[$doctor['id']] = $doctor['first_name'].' '.$doctor['last_name'];
        }
        return $dd_array;
    }

    public function fetchFacilitySpecialities ($id)
    {
        $facilities = $this->model->with('branches.specialities')->where('status_id', 1)->where('id', $id)->get()->toArray();
        return array_pluck(array_collapse(array_pluck(array_collapse(array_pluck($facilities, 'branches')), 'specialities')), 'name', 'id');
    }

    public function fetchFacilityBranches ($id)
    {
        $linked_branches = self::linkedBranches($id);
        foreach ($linked_branches->branches as $key => $branch) {
            $linked_branches->branches[$key]->branch_timings = ApplicationHelper::consolidateTimings($branch->timings);
        }
        return $linked_branches;
    }

    private function linkedDoctors($facility_id)
    {
        return $this->model->join('facility_branches', 'facility_branches.facility_id', '=', 'facility_facilities.id')
            ->join('facility_branch_doctors', 'facility_branches.id', '=', 'facility_branch_doctors.branch_id')
            ->join('doctor_doctors', 'doctor_doctors.id', '=', 'facility_branch_doctors.doctor_id')
            ->where('facility_facilities.id', $facility_id)
            ->select('doctor_doctors.id')->orderBy('doctor_doctors.id')->distinct()->get();
    }

    private function linkedPackages($facility_id)
    {
        return $this->model->join('facility_branches', 'facility_branches.facility_id', '=', 'facility_facilities.id')
            ->join('package_branches', 'facility_branches.id', '=', 'package_branches.branch_id')
            ->join('package_packages', 'package_packages.id', '=', 'package_branches.package_id')
            ->where('facility_facilities.id', $facility_id)
            ->select('package_packages.id')->orderBy('package_packages.id')->distinct()->get();
    }

    private function linkedArticles($facility_id)
    {
        return $this->model->join('facility_branches', 'facility_facilities.id', '=', 'facility_branches.facility_id')
            ->join('article_branches', 'facility_branches.id', '=', 'article_branches.branch_id')
            ->join('article_articles', 'article_branches.article_id', '=', 'article_articles.id')
            ->where('facility_facilities.id', $facility_id)
            ->select('article_articles.id')->orderBy('article_articles.id')->distinct()->get();
    }

    private function linkedBranches($facility_id)
    {
        return $this->model->with('branches.timings')->has('branches')
            ->where('status_id', '=', 1)->where('id', '=', $facility_id)->first();
    }
}
