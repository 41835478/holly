@extends('layouts.master')

@section('apple-mobile-web-app-title', config('app.name').'后台')

@prepend('css')
  <link rel="stylesheet" href="{{ asset_url('css/admin.css') }}">
@endprepend

@prepend('js')
<script src="{{ asset_url('js/admin.js') }}"></script>
@endprepend
