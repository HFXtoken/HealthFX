<?php

namespace Modules\Package\Repositories\Eloquent;

use Modules\Package\Events\PackageWasCreated;
use Modules\Package\Events\PackageWasDeleted;
use Modules\Package\Events\PackageWasUpdated;
use Modules\Package\Repositories\PackageRepository;
use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;
use Modules\Configuration\Repositories\LocationRepository;

class EloquentPackageRepository extends EloquentBaseRepository implements PackageRepository
{
    public function create($data)
    {
        $package = $this->model->create($data);

        if(isset($data['price']) && array_filter($data['price'])) {
            if($data['price']['discount_type'] !== '1') {
                $data['price']['discounted_price'] = $data['price']['user_price'];
            }
            $package->price()->create($data['price']);
        }

        $categoryInputArray = array_get($data, 'category_id');

        $package->categories()->sync($categoryInputArray);


        $subCategoryInputArray = array_get($data, 'sub_category_id');

        $package->subcategories()->sync($subCategoryInputArray);


        $specialityInputArray = array_get($data, 'speciality_id');

        $package->specialities()->sync($specialityInputArray);


        $partnerDealInputArray = array_get($data, 'deal_id');

        $package->partnerdeals()->sync($partnerDealInputArray);


        $branchInputArray = array_get($data, 'branch_id');

        $package->branches()->sync($branchInputArray);


        $articleInputArray = array_get($data, 'article_id');

        $package->articles()->sync($articleInputArray);


        if(isset($data['display']) && array_filter($data['display'])) {
            $package->displayLocations()->createMany($data['display']);
        }

        $package->setTags(array_get($data, 'tags', []));

        event(new PackageWasCreated($package, $data));

        return $package;
    }

    public function update($package, $data)
    {
        $package->update($data);

        if(isset($data['price']) && array_filter($data['price'])) {
            if($data['price']['discount_type'] !== '1') {
                $data['price']['provider_discount'] = null;
                $data['price']['discounted_price'] = $data['price']['user_price'];
            }
            $package->price()->update($data['price']);
        } else {
            $package->price()->delete();
        }

        $categoryInputArray = array_get($data, 'category_id');

        $package->categories()->sync($categoryInputArray);


        $subCategoryInputArray = array_get($data, 'sub_category_id');

        $package->subcategories()->sync($subCategoryInputArray);


        $specialityInputArray = array_get($data, 'speciality_id');

        $package->specialities()->sync($specialityInputArray);


        $partnerDealInputArray = array_get($data, 'deal_id');

        $package->partnerdeals()->sync($partnerDealInputArray);


        $branchInputArray = array_get($data, 'branch_id');

        $package->branches()->sync($branchInputArray);


        $articleInputArray = array_get($data, 'article_id');

        $package->articles()->sync($articleInputArray);
        

        if(isset($data['display']) && array_filter($data['display'])) {
            $packageDisplays = $package->displayLocations()->pluck('location')->all();

            $data_keys = array_keys($data['display']);
            // Prepare the specialities to be added and removed
            $displaysToAdd = array_diff($data_keys, $packageDisplays);
            $displaysToDel = array_diff($packageDisplays, $data_keys);
            $displaysToUpd = array_intersect($data_keys, $packageDisplays);

            // Detach the displays
            if (count($displaysToDel) > 0) {
                $package->displayLocations()->whereIn('location', $displaysToDel)->delete();
            }

            //Update existing displays
            if(count($displaysToUpd) > 0){
                foreach ($displaysToUpd as $display) {
                    $package->displayLocations()->where('location', $display)->update($data['display'][$display]);
                }
            }

            // Add new displays
            if (count($displaysToAdd) > 0) {
                $package->displayLocations()->createMany(array_intersect_key($data['display'], array_flip($displaysToAdd)));
            }
        } else {
            $package->displayLocations()->delete();
        }

        $package->setTags(array_get($data, 'tags', []));

        event(new PackageWasUpdated($package, $data));

        return $package;
    }

    public function destroy($package)
    {
        $package->price()->delete();

        $package->partnerdeals()->detach();

        $package->categories()->detach();

        $package->subcategories()->detach();

        $package->specialities()->detach();

        $package->branches()->detach();

        $package->articles()->detach();

        $package->displayLocations()->delete();

        $package->untag();

        event(new PackageWasDeleted($package));

        return $package->delete();
    }

    public function findByName($keyword)
    {
        return $this->model->whereTranslationILike('title', '%'.$keyword.'%')->get();
    }

    // Fetch distinct package labels and which are not null
    public function fetchPkgLabels()
    {
        return $this->model->listsTranslations('item_label')->select('item_label as ilabel', 'item_label')->whereNotNull('item_label')->distinct()->get();
    }


    // API layer for Frontend

    // Fetch packages for package home page featured packages section
//    public function fetchLgHorzFeaturedPackageForIndexPage()
//    {
//        $lgHorzPkg = $this->model->with('branches.facility.location')
//            ->join('package_display', 'package_display.package_id', '=', 'package_packages.id')
//            ->where('package_display.location', 'featured')
//            ->where('package_display.position', 1)
//            ->where('package_packages.status_id', 1)
//            ->select('package_packages.*')
//            ->orderByDesc('priority')
//            ->take(1)->get();
//
//        $lgHorzLatestPkg = $this->model->with('branches.facility.location')->doesntHave('displayLocations')
//            ->where('status_id', 1)->orderByDesc('priority')->take(1)->get();
//
//        return $lgHorzPkg->union($lgHorzLatestPkg)->first();
//    }

//    public function fetchVertFeaturedPackageForIndexPage()
//    {
//        $vertPkg = $this->model->with('branches.facility.location')
//            ->join('package_display', 'package_display.package_id', '=', 'package_packages.id')
//            ->where('package_display.location', 'featured')
//            ->where('package_display.position', 2)
//            ->where('package_packages.status_id', 1)
//            ->select('package_packages.*')
//            ->orderByDesc('priority')
//            ->take(1)->get();
//
////        $vertLatestPkg = $this->model->with('branches.facility.location')
////            ->leftJoin('package_display', 'package_display.package_id', '=', 'package_packages.id')
////            ->where('package_packages.status_id', 1)
////            ->where('package_display.location', '!=', 'featured')
////            ->where('package_display.location', '!=', 'home')
////            ->where('package_display.location', '!=', 'travelbanner')
////            ->select('package_packages.*')
////            ->orderByDesc('priority')
////            ->skip(1)->take(1)->get();
//
//        $vertLatestPkg = $this->model->with('branches.facility.location')->doesntHave('displayLocations')
//            ->where('status_id', 1)->orderByDesc('priority')->skip(1)->take(1)->get();
//
//
//        return $vertPkg->union($vertLatestPkg)->first();
//    }

//    public function fetchMdHorzFeaturedPackageForIndexPage()
//    {
//        $mdHorzPkg = $this->model->with('branches.facility.location')
//            ->join('package_display', 'package_display.package_id', '=', 'package_packages.id')
//            ->where('package_display.location', 'featured')
//            ->where('package_display.position', 3)
//            ->where('package_packages.status_id', 1)
//            ->select('package_packages.*')
//            ->orderByDesc('priority')
//            ->take(1)->get();
//
//        $mdHorzLatestPkg = $this->model->with('branches.facility.location')->doesntHave('displayLocations')
//            ->where('status_id', 1)->orderByDesc('priority')->skip(2)->take(1)->get();
//
//        return $mdHorzPkg->union($mdHorzLatestPkg)->first();
//    }

    public function fetchFeaturedPackagesForIndexPage()
    {
        $featuredFirstPkg = $this->model->with('branches.facility.location')
            ->join('package_display', 'package_display.package_id', '=', 'package_packages.id')
            ->where('package_display.location', 'featured')
            ->where('package_display.position', 1)
            ->where('package_packages.status_id', 1)
            ->select('package_packages.*')
            ->orderByDesc('priority')
            ->take(1)->get();

        $featuredSecondPkg = $this->model->with('branches.facility.location')
            ->join('package_display', 'package_display.package_id', '=', 'package_packages.id')
            ->where('package_display.location', 'featured')
            ->where('package_display.position', 2)
            ->where('package_packages.status_id', 1)
            ->select('package_packages.*')
            ->orderByDesc('priority')
            ->take(1)->get();

        $featuredThirdPkg = $this->model->with('branches.facility.location')
            ->join('package_display', 'package_display.package_id', '=', 'package_packages.id')
            ->where('package_display.location', 'featured')
            ->where('package_display.position', 3)
            ->where('package_packages.status_id', 1)
            ->select('package_packages.*')
            ->orderByDesc('priority')
            ->take(1)->get();

        $featuredFourthPkg = $this->model->with('branches.facility.location')
            ->join('package_display', 'package_display.package_id', '=', 'package_packages.id')
            ->where('package_display.location', 'featured')
            ->where('package_display.position', 4)
            ->where('package_packages.status_id', 1)
            ->select('package_packages.*')
            ->orderByDesc('priority')
            ->take(1)->get();

        $featuredFifthPkg = $this->model->with('branches.facility.location')
            ->join('package_display', 'package_display.package_id', '=', 'package_packages.id')
            ->where('package_display.location', 'featured')
            ->where('package_display.position', 5)
            ->where('package_packages.status_id', 1)
            ->select('package_packages.*')
            ->orderByDesc('priority')
            ->take(1)->get();

        $featuredSixthPkg = $this->model->with('branches.facility.location')
            ->join('package_display', 'package_display.package_id', '=', 'package_packages.id')
            ->where('package_display.location', 'featured')
            ->where('package_display.position', 6)
            ->where('package_packages.status_id', 1)
            ->select('package_packages.*')
            ->orderByDesc('priority')
            ->take(1)->get();

//        $featuredLatestPkgs = $this->model->with('branches.facility.location')
//            ->leftJoin('package_display', 'package_display.package_id', '=', 'package_packages.id')
//            ->where('package_packages.status_id', 1)
//            ->where('package_display.location', '!=', 'featured')
//            ->where('package_display.location', '!=', 'home')
//            ->where('package_display.location', '!=', 'travelbanner')
//            ->select('package_packages.*')
//            ->orderByDesc('priority')
//            ->skip(3)->take(2)->get();

//        $featuredLatestPkgs = $this->model->with('branches.facility.location')->doesntHave('displayLocations')
//            ->where('status_id', 1)->orderByDesc('priority')->skip(3)->take(3)->get();

        $featuredLatestPkgs = $this->model->with('branches.facility.location')->doesntHave('displayLocations')
            ->where('status_id', 1)->orderByDesc('priority')->take(6)->get();

        return $featuredFirstPkg->concat($featuredSecondPkg)->concat($featuredThirdPkg)->concat($featuredFourthPkg)->concat($featuredFifthPkg)->concat($featuredSixthPkg)->union($featuredLatestPkgs);
    }

    // Fetch packages for package home page medical packages section
    public function fetchMedicalPackagesForIndexPage()
    {
        return $this->model->with('branches.facility.location')->whereHas('categories.packageCategories', function($q) {
            $q->where('slug', 'medical')->where('type', 'packages')->where('is_active', true);
        })->where('status_id', '=', 1)->take(6)->orderByDesc('priority')->get();
    }

    // Fetch packages for package home page health-screening packages section
    public function fetchHealthPackagesForIndexPage()
    {
        return $this->model->with('branches.facility.location')->whereHas('categories.packageCategories', function($q) {
            $q->where('slug', 'health-screening')->where('type', 'packages')->where('is_active', true);
        })->where('status_id', '=', 1)->take(6)->orderByDesc('priority')->get();
    }

    // Fetch packages for package home page more packages section
    public function fetchRegularPackagesForIndexPage()
    {
        return $this->model->with('branches.facility.location')->where('status_id', '=', 1)->take(6)->inRandomOrder()->get();
    }

    // Fetch all packages for package list page based on package type
    public function fetchAll($type)
    {
        if($type === 'medical') {

            $packages = $this->model->with('branches.facility.location')->whereHas('categories.packageCategories', function($q) {
                $q->where('slug', 'medical')->where('type', 'packages')->where('is_active', true);
            })->where('status_id', '=', 1)->orderByDesc('priority')->paginate(8);

        } else if($type === 'health-screening') {

            $packages = $this->model->with('branches.facility.location')->whereHas('categories.packageCategories', function($q) {
                $q->where('slug', 'health-screening')->where('type', 'packages')->where('is_active', true);
            })->where('status_id', '=', 1)->orderByDesc('priority')->paginate(8);

        } else {

            $packages = $this->model->with('branches.facility.location')->where('status_id', '=', 1)->orderByDesc('priority')->paginate(8);
        }

        return $packages;
    }

    // Fetch package details for detail page by slug
    public function findBySlug($slug)
    {
        return $this->model->with('branches.facility.location')->where('slug', $slug)->first();
    }

    // Fetch popular packages
    public function fetchByViews()
    {
        return $this->model->with('branches.facility.location')->where('status_id', 1)->take(5)->latest()->get();
    }

    // Fetch countries of published packages
    public function fetchDestinations()
    {
        $linkedPackages = $this->model->with('branches.facility.location')->where('status_id', 1)->get();
        return array_unique(array_pluck(array_collapse(array_pluck($linkedPackages, 'branches')), 'facility.location.country', 'facility.location.country'));
    }

    // Fetch locations of published packages
    public function fetchLocations()
    {
        $linkedPackages = $this->model->with('branches.facility.location')->where('status_id', 1)->get();
        return array_unique(array_pluck(array_collapse(array_pluck($linkedPackages, 'branches')), 'facility.location.city_name', 'facility.location.slug'));
    }

    public function fetchCategories()
    {
        $linkedPackages = $this->model->with('categories')->where('status_id', 1)->get();
        return array_unique(array_pluck(array_collapse(array_pluck($linkedPackages, 'categories')), 'name', 'slug'));
    }

    public function fetchByFilters($type = null, $destination = null, $location = null, $category = null, $keyword = null)
    {
        $Builder = $this->model->with('branches.facility.location', 'branches.timings');
        if($type != null && $type != 'regular') {
            $Builder->whereHas('categories.packageCategories', function($q) use($type) {
                $q->where('slug', $type)->where('type', 'packages')->where('is_active', true);
            });
        }
//        if($destination != null) {
//            if( $country != null && $country === 'true') {
//                $location_repo = app()->make(LocationRepository::class);
//                $location_entity = $location_repo->findBySlug($destination);
//                $Builder->whereHas('branches.facility.location', function($q) use($location_entity) {
//                    $q->whereTranslation('country', $location_entity->country);
//                });
//            } else {
//                $Builder->whereHas('branches.facility.location', function($q) use($destination) {
//                    $q->where('slug', $destination);
//                });
//            }
//        }
        if($destination != null) {
            $Builder->whereHas('branches.facility.location', function($q) use($destination) {
                $q->whereTranslation('country', $destination);
            });

            if($location != null) {
                $Builder->whereHas('branches.facility.location', function($q) use($location) {
                    $q->where('slug', $location);
                });
            }
        }
        if($category != null) {
            $Builder->whereHas('categories', function($q) use($category) {
                $q->where('slug', $category);
            });
        }
        if($keyword != null) {
            $Builder->where(function($query) use ($keyword){
                $query
                    ->whereTranslationILike('title', '%'.$keyword.'%')
                    ->orWhereTranslationILike('short_description', '%'.$keyword.'%');
            });
        }
        $Builder->where('status_id', 1)->orderByDesc('priority');

        $packages = $Builder->latest()->paginate(8);

        $destinations = array_unique(array_pluck(array_collapse(array_pluck($packages, 'branches')), 'facility.location.country', 'facility.location.country'));

        $locations = array_unique(array_pluck(array_collapse(array_pluck($packages, 'branches')), 'facility.location.city_name', 'facility.location.slug'));

        $categories = array_unique(array_pluck(array_collapse(array_pluck($packages->load('categories'), 'categories')), 'name', 'slug'));

        $results = array(
            'search_result' => $packages,
            'search_destinations' => $destinations,
            'search_locations' => $locations,
            'search_categories' => $categories,
            'keyword' => $keyword
        );

        return $results;
    }

    // Fetch featured packages for site home page
    public function fetchFeaturedPackagesForHomePage()
    {
        $siteHomePackages = $this->model->with('branches.facility.location')
            ->join('package_display', 'package_packages.id', '=', 'package_display.package_id')
            ->where('package_packages.status_id', 1)
            ->where('package_display.location', '=', 'home')
            ->whereNotNull('package_display.position')
            ->select('package_packages.*')
            ->orderBy('package_display.position', 'asc')
            ->take(9)->get();

        $latestPackages = $this->model->with('branches.facility.location')->whereDoesntHave('displayLocations', function($q) {
            $q->whereIn('location', ['home', 'travelbanner']);
        })->where('status_id', 1)->orderByDesc('priority')->take(9)->get();

        return $siteHomePackages->union($latestPackages)->take(9);
    }

    // Fetch featured travel package for site home page
    public function fetchFeaturedTravelPackageForHomePage()
    {
        return $this->model->with('branches.facility.location')->whereHas('displayLocations', function($q) {
            $q->where('location', '=', 'travelbanner');
        })->where('status_id', '=', 1)->orderByDesc('priority')->first();
    }
}
