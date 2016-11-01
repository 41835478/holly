@extends('layouts.site.master')

@section('alerts-container', '.body-container')

@section('body.content')
<div class="container body-container">
  @yield('content')
</div>
@stop
