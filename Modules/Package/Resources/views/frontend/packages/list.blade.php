@extends('layouts.site-inner')


@section('content-header')
    <h2>{{ trans('frontend.package.healthcare_packages') }}</h2>
    <bread-crumbs :items="[{text: '{{trans('frontend.breadcrumbs.home')}}', href: '{{route('homepage')}}' }, {text: '{{trans('frontend.breadcrumbs.packages')}}', 'href': '{{route('frontend.package.package.index-template')}}' }, {text: '{{trans('frontend.breadcrumbs.all')}}', href: '#'}]"></bread-crumbs>
@stop


@section('content')
    <div id="packageListContent">
        {{--<div class="stepsPkgListingInfograph">--}}
            {{--<h3>{{ trans('frontend.package.steps_to_buy_pkg') }}</h3>--}}
            {{--<img src="/themes/globalhealth/images/4stepsImg.png" alt="">--}}
            {{--<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam vel tincidunt nisi, sit amet egestas urna. Vivamus sagittis, ex in egestas eleifend, nisi nulla posuere eros, sed dictum nulla quam sit amet justo. Suspendisse potenti. Aenean placerat nec purus ac aliquam. </p>--}}
            {{--<div class="row" style="text-align: center; margin-left: -15px !important;">--}}
                {{--<div class="col-md-3 col-sm-3 col-xs-12" style="padding-left: 0px;">--}}
                    {{--{{ trans('frontend.package.pkg_buy_step1') }}--}}
                {{--</div>--}}
                {{--<div class="col-md-3 col-sm-3 col-xs-12">--}}
                    {{--{{ trans('frontend.package.pkg_buy_step2') }}--}}
                {{--</div>--}}
                {{--<div class="col-md-3 col-sm-3 col-xs-12" style="padding-left: 20px;">--}}
                    {{--{{ trans('frontend.package.pkg_buy_step3') }}--}}
                {{--</div>--}}
                {{--<div class="col-md-3 col-sm-3 col-xs-12" style="padding-left: 20px;">--}}
                    {{--{{ trans('frontend.package.pkg_buy_step4') }}--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}

        <filter-shortcuts></filter-shortcuts>

        <package-list></package-list>
    </div>
@stop

@section('right-sidebar')
    <div id="packageListSidebar">

        <package-search></package-search>

        <cart-count></cart-count>

        <?php if ($currentUser != null && $currentUser->first_name != ' '): ?>
            <a href="{{ route('frontend.profile.packages') }}" class="purPkgsBtn">{{ trans('frontend.package.purchased_packages') }}</a>
        <?php endif; ?>

        <popular-packages></popular-packages>
    </div>
@stop


@section('pagination')
    @include('package::frontend.packages.partials.pagination')
@stop


@section('partners')
    @include('configuration::frontend.partners.partners')
@stop