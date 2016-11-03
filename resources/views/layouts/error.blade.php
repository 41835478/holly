@extends('layouts.master')

@section('baidu-analytics', config('services.baidu_analytics'))

@section('title', 'Error '.$exception->getStatusCode())

@push('css')
<style type="text/css">
.message {
  font-size: 20px;
}
</style>
@endpush

@section('body')
<div class="message">
  @yield('message', $exception->getMessage())
</div>
@stop
