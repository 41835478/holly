<!DOCTYPE html>
<html lang="@yield('lang', 'zh')">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <title>@hasSection('title')@yield('title') - {{ config('app.name') }}<?php else: ?>@yield('full_title', config('app.name'))@endif</title>
@hasSection('keywords')
  <meta name="keywords" content="@yield('keywords')">
@endif
@hasSection('description')
  <meta name="description" content="@yield('description')">
@endif
  <meta name="viewport" content="@yield('viewport', 'width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no')">
@section('apple-mobile')
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-title" content="@yield('apple-mobile-web-app-title', config('app.name'))">
@show
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="format-detection" content="@yield('format-detection', 'telephone=no,date=no,address=no,email=no,url=no')">
  <link rel="shortcut icon" type="image/x-icon" href="@yield('favicon', revision('/favicon.ico'))">
  <link rel="apple-touch-icon" href="@yield('apple-touch-icon', revision('/apple-touch-icon.png'))">
@section('IE')
  <!--[if lt IE 9]><script src="{{ asset_url('js/ie-compatible.js') }}"></script><![endif]-->
@show
@stack('css')
@stack('head')
</head>
@hasSection('body-class')
<body class="@yield('body-class')">
@else<body>
@endif
@include('includes.wechat-icon')
@include('includes.client')
@yield('body')
@stack('js')
@include('includes.baidu-analytics')
</body>
</html>
