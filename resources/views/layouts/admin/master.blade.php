@extends('layouts.master')

@section('alerts-selector', 'body')
@section('alerts-position', 'top')

@push('css')
  <link rel="stylesheet" href="{{ asset_url('css/admin.css') }}">
@endpush

@push('js')
<script src="{{ asset_url('js/admin.js') }}"></script>
@endpush
