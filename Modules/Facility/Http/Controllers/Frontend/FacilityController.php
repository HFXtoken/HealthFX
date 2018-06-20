<?php
/**
 * Created by PhpStorm.
 * User: Naveen
 * Date: 13/11/17
 * Time: 10:50 AM
 */

namespace Modules\Facility\Http\Controllers\Frontend;


use Illuminate\Http\Request;
use Modules\Core\Http\Controllers\BasePublicController;
use Modules\Facility\Repositories\DepartmentRepository;
use Modules\Facility\Repositories\FacilityRepository;
use Illuminate\Support\Facades\Mail;

class FacilityController extends BasePublicController
{
    private $facility;
    /**
     * @var DepartmentRepository
     */
    private $department;

    /**
     * FacilityController constructor.
     * @param FacilityRepository $facility
     * @param DepartmentRepository $department
     */
    public function __construct(FacilityRepository $facility, DepartmentRepository $department)
    {
        $this->facility = $facility;
        $this->department = $department;
    }

    // Return facility list page template to the list view
    public function getListPageTemplate()
    {
        return view('facility::frontend.facilities.list');
    }


    // Return facility detail page template to the detail view
    public function getDetailPageTemplate()
    {
        return view('facility::frontend.facilities.detail');
    }

    public function getDepartmentPageTemplate()
    {
        return view('facility::frontend.facilities.department');
    }


    // Fetch list of all facilities
    public function getAll(Request $request)
    {
        if(empty($_GET)) {

            $facilities = $this->facility->fetchAll();

            foreach ($facilities['search_result'] as $key => $facility)
            {
                $facilities['search_result'][$key]->image = $facility->facility_image->path_string;

                foreach ($facility->linked_packages as $key => $linked_package)
                {
                    $facility->linked_packages[$key]->image = $linked_package->package_image->path_string;
                }

                foreach ($facility->linked_doctors as $key => $linked_doctor)
                {
                    $facility->linked_doctors[$key]->image = $linked_doctor->profile_image->path_string;
                }
            }

            return $facilities;
        } else {
            return $this->getSearchResult($request);
        }
        
    }

    // Fetch details of a particular facility by its slug
    public function getDetail(Request $request)
    {
        $facilityDetails = $this->facility->findBySlug($request->query('slug'));

        foreach ($facilityDetails as $key => $facilityDetail)
        {
            $facilityDetails[$key]->image = $facilityDetail->facility_image->path_string;

            $facilityDetails[$key]->gallery = $facilityDetail->gallery_images;

            foreach ($facilityDetail->linked_packages as $key => $linked_package)
            {
                $facilityDetail->linked_packages[$key]->image = $linked_package->package_image->path_string;
            }

            foreach ($facilityDetail->linked_doctors as $key => $linked_doctor)
            {
                $facilityDetail->linked_doctors[$key]->image = $linked_doctor->profile_image->path_string;
            }

            foreach ($facilityDetail->linked_articles as $key => $linked_article)
            {
                $facilityDetail->linked_articles[$key]->image = $linked_article->article_image->path_string;
            }

            $showFacilityBranchesAccordion = array_fill(0, sizeof($facilityDetail->linked_branches->branches), false);

            $facilityDetail->linked_branches->showFacilityBranchesAccordion = $showFacilityBranchesAccordion;
        }

        return $facilityDetails;
    }

    // Fetch facilities for site home page
    public function getFacilitiesForHomePage()
    {
        $featuredFacilities = $this->facility->fetchFacilitiesForHomePage();

        foreach ($featuredFacilities as $key => $featuredFacility)
        {
            $featuredFacilities[$key]->image = $featuredFacility->facility_image->path_string;
        }

        return $featuredFacilities;
    }

    // Fetch countries of published facilities
    public function getSearchDestinations()
    {
        return $this->facility->fetchDestinations();
    }

    // Fetch cities of published facilities
    public function getSearchLocations()
    {
        return $this->facility->fetchLocations();
    }

    // Fetch specialities of published facilities
    public function getSearchSpecialities()
    {
        return $this->facility->fetchSpecialities();
    }

    public function getSearchResult(Request $request)
    {
        $results = $this->facility->fetchByFilters($request->query('speciality'), $request->query('destination'), $request->query('location'), $request->query('keyword'));
        foreach ($results['search_result'] as $key => $facility)
        {
            $results['search_result'][$key]->image = $facility->facility_image->path_string;

             foreach ($facility->linked_packages as $key => $linked_package)
             {
                 $facility->linked_packages[$key]->image = $linked_package->package_image->path_string;
             }

             foreach ($facility->linked_doctors as $key => $linked_doctor)
             {
                 $facility->linked_doctors[$key]->image = $linked_doctor->profile_image->path_string;
             }
        }

        return $results;
    }

    // Send email to admin with the details filled in facility enquiry form
    public function mailFacilityEnquiryForm(Request $request)
    {
        $data = $request->all();

        if(count($data) > 0)
        {
            // Send mail to user with form data
            Mail::send('profile::email.user.enquiry', ['data' => $data], function ($message) use ($data) {
                $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
                $message->to($data['user_email'], $data['user_first_name'].' '.$data['user_last_name'])->subject(trans('frontend.email.provider.enquiry'));
            });

            // Send mail to admin with form data
            Mail::send('profile::email.admin.enquiry', ['data' => $data], function ($message) use ($data) {
                $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
                $message->to(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'))->subject(trans('frontend.email.provider.enquiry'));
            });
        }
        return $data;
    }

    public function getDepartmentDetail(Request $request)
    {
        $departmentDetails = $this->department->findBySlug($request->query('slug'));

        $departmentDetails->facility->linked_branches = $this->facility->fetchFacilityBranches($departmentDetails->facility->id);
        $showFacilityBranchesAccordion = array_fill(0, sizeof($departmentDetails->facility->linked_branches->branches), false);
        $departmentDetails->facility->linked_branches->showFacilityBranchesAccordion = $showFacilityBranchesAccordion;

        foreach ($departmentDetails->doctors as $key => $doctor)
        {
            $departmentDetails->doctors[$key]->image = $doctor->profile_image->path_string;
        }
        $departmentDetails->image = ($departmentDetails->department_image != null) ? $departmentDetails->department_image->path_string : $departmentDetails->facility->facility_image->path_string;

        return $departmentDetails;
    }
}