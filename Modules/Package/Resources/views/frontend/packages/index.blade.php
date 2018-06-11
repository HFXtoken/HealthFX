@extends('layouts.site-inner')


@section('content-header')
    <h2 class="pkgLandMainHdr">{{ trans('frontend.package.healthcare_packages') }}</h2>
    <bread-crumbs :items="[{text: '{{trans('frontend.breadcrumbs.home')}}', href: '{{route('homepage')}}' }, {text: '{{trans('frontend.breadcrumbs.packages')}}', 'href': '{{route('frontend.package.package.index-template')}}' }, {text: '{{trans('frontend.breadcrumbs.featured')}}', href: '#'}]"></bread-crumbs>
@stop


@section('content')
    <div id="packageIndexPage">

        <!-- Featured packages section -->
        {{--<div class="pkgLandSubHdr">--}}
            {{--<h2>{{ trans('frontend.package.featured_packages') }}</h2>--}}
        {{--</div>--}}

        {{--<featured-horizontal></featured-horizontal>--}}

        {{--<div class="trvlPkgSecRow row">--}}
            {{--<featured-vertical></featured-vertical>--}}
            {{--<div class="col-md-12 col-sm-12 col-xs-12 trvlPkgSecRowRightPanel" style="padding-right: 10px;">--}}
                {{--<featured-med-horizontal></featured-med-horizontal>--}}
                {{--<featured-packages></featured-packages>--}}
            {{--</div>--}}
        {{--</div>--}}

        <!-- Featured packages section -->
        <featured-packages></featured-packages>

        <!-- Medical packages section -->
        <medical-packages></medical-packages>

        <!-- Health screening packages section -->
        <health-packages></health-packages>

        <!-- Regular packages section -->
        <regular-packages></regular-packages>
    </div>
@stop


@section('right-sidebar')
    <div id="packageListSidebar" style="margin-top: 35px;">
        <popular-packages></popular-packages>
    </div>
@stop


@section('partners')
    @include('configuration::frontend.partners.partners')
@stop