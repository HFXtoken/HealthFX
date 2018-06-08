<?php
/**
 * Created by PhpStorm.
 * User: Naveen
 * Date: 27/10/17
 * Time: 4:06 PM
 */

use Illuminate\Routing\Router;
/** @var Router $router */

$router->group(['prefix' => '/package'], function(Router $router) {

    $router->get('', [
        'as' => 'frontend.package.package.index-template',
        'uses' => 'Frontend\PackageController@getHomePageTemplate'
    ]);

//    $router->get('travel/all', [
//        'as' => 'frontend.package.package.travel.list',
//        'uses' => 'Frontend\PackageController@getAllTravelPackages'
//    ]);

    $router->get('all/{listType}', [
        'as' => 'frontend.package.package.list-template',
        'uses' => 'Frontend\PackageController@getListPageTemplate'
    ]);

    $router->get('detail/{slug}', [
        'as' => 'frontend.package.package.detail-template',
        'uses' => 'Frontend\PackageController@getDetailPageTemplate'
    ]);

    $router->get('mastercard/{type}', [
        'as' => 'frontend.package.package.mastercard-template',
        'uses' => 'Frontend\PackageController@getMastercardPageTemplate'
    ]);
});


// API layer
$router->group(['prefix' => '/api'], function(Router $router) {

    // routes used for package landing page
    $router->get('/packages/home/horz-lg-featured', [
        'as' => 'api.frontend.package.home.horz-lg-featured',
        'uses' => 'Frontend\PackageController@getLgHorzFeaturedPackageForIndexPage'
    ]);

    $router->get('/packages/home/vert-featured', [
        'as' => 'api.frontend.package.home.vert-featured',
        'uses' => 'Frontend\PackageController@getVertFeaturedPackageForIndexPage'
    ]);

    $router->get('/packages/home/horz-md-featured', [
        'as' => 'api.frontend.package.home.horz-md-featured',
        'uses' => 'Frontend\PackageController@getMdHorzFeaturedPackageForIndexPage'
    ]);

    $router->get('/packages/home/featured', [
        'as' => 'api.frontend.package.home.featured',
        'uses' => 'Frontend\PackageController@getFeaturedPackagesForIndexPage'
    ]);

    $router->get('/packages/home/medical', [
        'as' => 'api.frontend.package.home.medical',
        'uses' => 'Frontend\PackageController@getMedicalPackagesForIndexPage'
    ]);

    $router->get('/packages/home/health-screening', [
        'as' => 'api.frontend.package.home.health-screening',
        'uses' => 'Frontend\PackageController@getHealthPackagesForIndexPage'
    ]);

    $router->get('/packages/home/regular', [
        'as' => 'api.frontend.package.home.regular',
        'uses' => 'Frontend\PackageController@getRegularPackagesForIndexPage'
    ]);

    // route used for package list page
    $router->get('/packages/all/{listType}', [
        'as' => 'frontend.package.package.list',
        'uses' => 'Frontend\PackageController@getSearchResult'
    ]);

    // route used for package detail page
    $router->get('/packages/detail/{slug}', [
        'as' => 'api.frontend.package.detail',
        'uses' => 'Frontend\PackageController@getDetail'
    ]);

    // route used for fetching popular packages
    $router->get('/packages/popular-packages', [
        'as' => 'api.frontend.package.popular-packages',
        'uses' => 'Frontend\PackageController@getPopularPackages'
    ]);

    // route used for fetching all locations mapped to published packages
    $router->get('/packages/locations', [
        'as' => 'api.frontend.package.locations',
        'uses' => 'Frontend\PackageController@getLocations'
    ]);

    // route used for fetching all countries mapped to published packages
    $router->get('/packages/search/destinations', [
        'as' => 'api.frontend.package.search-destinations',
        'uses' => 'Frontend\PackageController@getSearchDestinations'
    ]);

    // route used for fetching all locations mapped to published packages
    $router->get('/packages/search/locations', [
        'as' => 'api.frontend.package.search-locations',
        'uses' => 'Frontend\PackageController@getSearchLocations'
    ]);

    // route used for fetching all categories mapped to published packages
    $router->get('/packages/search/categories', [
        'as' => 'api.frontend.package.search-categories',
        'uses' => 'Frontend\PackageController@getSearchCategories'
    ]);

    // route used for fetching all locations mapped to published packages
    $router->get('/packages/search/result', [
        'as' => 'api.frontend.package.search-result',
        'uses' => 'Frontend\PackageController@getSearchResult'
    ]);

    // route used for fetching packages for site home page
    $router->get('/packages/featured-packages', [
        'as' => 'api.frontend.package.featured-packages',
        'uses' => 'Frontend\PackageController@getFeaturedPackagesForHomePage'
    ]);

    // route used for fetching featured travel package for site home page
    $router->get('/packages/travel-package', [
        'as' => 'api.frontend.package.travel-package',
        'uses' => 'Frontend\PackageController@getFeaturedTravelPackageForHomePage'
    ]);
});