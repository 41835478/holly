@extends('layouts.master')

@section('alerts-selector', 'body')
@section('alerts-position', 'top')

@prepend('css')
  <link rel="stylesheet" href="{{ asset_url('css/site.css') }}">
@endprepend

@prepend('js')
<script src="{{ asset_url('js/site.js') }}"></script>
@endprepend

@section('body')
@yield('header')
@yield('content')
@yield('footer')
@stop
