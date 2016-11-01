@extends('layouts.master')

@section('wechat-icon', asset_url('img/icon-300.png'))
@section('baidu-analytics', config('services.baidu_analytics'))
@section('alerts-container', 'body')

@push('css')
  <link rel="stylesheet" href="{{ asset_url('css/site.css') }}">
@endpush

@push('js')
<script src="{{ asset_url('js/site.js') }}"></script>
@include('includes.alerts')
@endpush

@section('body')
@yield('body.header')
@yield('body.content')
@yield('body.footer')
@stop
