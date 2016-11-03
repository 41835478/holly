@extends('layouts.admin.master')

@section('alerts-selector', '.content-wrapper :first.content')
@section('alerts-position', 'top')
@section('body-class', 'hold-transition skin-blue sidebar-mini fixed')

@section('body')
<div class="wrapper">
  @include('includes.admin.main-header')
  @include('includes.admin.main-sidebar')
  @include('includes.admin.content-wrapper')
  @include('includes.admin.main-footer')
</div>
@stop
