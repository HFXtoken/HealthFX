<?php
/**
 * Created by PhpStorm.
 * User: Naveen
 * Date: 27/10/17
 * Time: 4:02 PM
 */

use Illuminate\Routing\Router;
/** @var Router $router */

$router->group(['prefix' => '/facility'], function(Router $router) {

    $router->get('all', [
        'as' => 'frontend.facility.facility.list-template',
        'uses' => 'Frontend\FacilityController@getListPageTemplate'
    ]);

    $router->get('detail/{slug}', [
        'as' => 'frontend.facility.facility.detail-template',
        'uses' => 'Frontend\FacilityController@getDetailPageTemplate'
    ]);

    $router->get('detail/{slug}/{departmentSlug}', [
        'as' => 'frontend.facility.facility.department-template',
        'uses' => 'Frontend\FacilityController@getDepartmentPageTemplate'
    ]);
});


// API Layer
$router->group(['prefix' => '/api'], function(Router $router) {

    // route used for facility list page
    $router->get('/facility/all', [
        'as' => 'api.frontend.facility.list',
        'uses' => 'Frontend\FacilityController@getAll'
    ]);

    // route used for facility detail page
    $router->get('/facility/detail/{slug}', [
        'as' => 'api.frontend.facility.detail',
        'uses' => 'Frontend\FacilityController@getDetail'
    ]);

    // route used for home page facility list
    $router->get('/facility/featured-facility', [
        'as' => 'api.frontend.facility.featured',
        'uses' => 'Frontend\FacilityController@getFacilitiesForHomePage'
    ]);

    // route used for fetching all destinations mapped to published facility
    $router->get('/facility/search/destinations', [
        'as' => 'api.frontend.facility.search-destinations',
        'uses' => 'Frontend\FacilityController@getSearchDestinations'
    ]);

    // route used for fetching all locations mapped to published facility
    $router->get('/facility/search/locations', [
        'as' => 'api.frontend.facility.search-locations',
        'uses' => 'Frontend\FacilityController@getSearchLocations'
    ]);

    // route used for fetching all specialities mapped to published facility
    $router->get('/facility/search/specialities', [
        'as' => 'api.frontend.facility.search-specialities',
        'uses' => 'Frontend\FacilityController@getSearchSpecialities'
    ]);

    // route used for submitting facility enquiry
    $router->post('/facility/submit-query', [
        'as' => 'api.frontend.facility.submit-query',
        'uses' => 'Frontend\FacilityController@mailFacilityEnquiryForm'
    ]);

    // route used for facility department detail
    $router->get('/facility/department/{slug}', [
        'as' => 'api.frontend.facility.department.detail',
        'uses' => 'Frontend\FacilityController@getDepartmentDetail'
    ]);
});