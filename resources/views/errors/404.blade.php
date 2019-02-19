@extends('layouts/app')

@section('head')
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{t('facebook.site_name')}}">
    <meta property="og:title" content="{{t('facebook.title')}}">
    <meta property="og:url" content="{{t('facebook.site_url')}}{{Request::path()}}">
    <meta property="og:image" content="{{t('facebook.site_url')}}/assets/images/social/fb.png">
    <meta property="og:description" content="{{htmlentities(t('facebook.description'), ENT_QUOTES)}}">
@endsection

@section('html_header')
@endsection

@section('html_footer')
@endsection

@section('content')

<div class="container">
    <h1 class="homepage size48 margin-bottom margin-top-x">{{t('page.page_not_found')}}</h1>
    <img width="100%" src="{{ asset('assets/images/compress_semen_2.png') }}">
</div>

@endsection