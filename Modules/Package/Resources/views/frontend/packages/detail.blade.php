@extends('layouts.site-inner')


@section('content-header')
    <h2>{{ trans('frontend.package.healthcare_packages') }}</h2>
    <bread-crumbs :items="[{text: '{{trans('frontend.breadcrumbs.home')}}', href: '{{route('homepage')}}'}, {text: '{{trans('frontend.breadcrumbs.packages')}}', href: '{{route('frontend.package.package.index-template')}}'}]"></bread-crumbs>
@stop


@section('content')
    <div id="packageDetailContent">
        <package-detail></package-detail>
    </div>
@stop


@section('right-sidebar')
    <div id="packageDetailSidebar" style="margin-top: 0px;">
        <cart-count></cart-count>

        <?php if ($currentUser != null && $currentUser->first_name != ' '): ?>
            <a href="{{ route('frontend.profile.packages') }}" class="purPkgsBtn">{{ trans('frontend.package.purchased_packages') }}</a>
        <?php endif; ?>
    </div>
@stop


@section('partners')
    @include('configuration::frontend.partners.partners')
@stop