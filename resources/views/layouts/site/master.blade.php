@extends('layouts.master')

@section('alerts-selector', 'body')
@section('alerts-position', 'top')
@section('baidu-analytics', config('services.baidu_analytics'))

@push('css')
  <link rel="stylesheet" href="{{ asset_url('css/site.css') }}">
@endpush

@push('js')
<script src="{{ asset_url('js/site.js') }}"></script>
@endpush

@section('body')
@yield('header')
@yield('content')
@yield('footer')
@stop
