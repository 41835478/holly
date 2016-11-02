@extends('layouts.site.master')

@section('alerts-container', '.body-container')

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
