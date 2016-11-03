@extends('layouts.site.master')

@section('alerts-selector', '.body-container:first')
@section('alerts-position', 'top')

@section('header')
@include('includes.site.header')
@stop

@section('content')
<div class="container body-container">
  @yield('content')
</div> <!-- //.body-container -->
@overwrite

@section('footer')
@include('includes.site.footer')
@stop
