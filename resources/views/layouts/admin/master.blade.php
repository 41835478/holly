@extends('layouts.master')

@section('apple-mobile-web-app-title', config('app.name').'后台')

@push('css')
  <link rel="stylesheet" href="{{ asset_url('css/admin.css') }}">
@endpush

@push('js')
<script src="{{ asset_url('js/admin.js') }}"></script>
@endpush
