@extends('layouts.master')

@section('alerts-container', 'body')
@section('baidu-analytics', config('services.baidu_analytics'))

@push('css')
  <link rel="stylesheet" href="{{ asset_url('css/site.css') }}">
@endpush

@push('js')
<script src="{{ asset_url('js/site.js') }}"></script>
@endpush

@section('body')
@hasSection ('header')
@yield('header')
@endif

<div class="container body-container">
@yield('content')
</div> <!-- //.body-container -->

@hasSection('footer')
@yield('footer')
@endif
@stop
