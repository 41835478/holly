@extends('layouts.admin.default')

@push('css')
<style type="text/css">
  .stat-box {
    cursor: pointer;
  }
</style>
@endpush

@push('js')
<script>
$(function () {
  $('.stat-box').click(function (e) {
    e.preventDefault();
    var url = $(this).children('a').attr('href');
    if (url) {
      location.href = url;
    }
  });
});
</script>
@endpush

@section('content')
<div class="row">

  <div class="col-md-3 col-sm-6 col-xs-12">
    <div class="small-box stat-box bg-green">
      <div class="inner">
        <h3>8769</h3>
        <p>今日新增用户</p>
      </div>
      <div class="icon">
        <i class="fa fa-users"></i>
      </div>
      <a href="/user" class="small-box-footer">
        总数：99888762 <i class="fa fa-arrow-circle-right"></i>
      </a>
    </div>
  </div>

  <div class="col-md-3 col-sm-6 col-xs-12">
    <div class="small-box stat-box bg-light-blue">
      <div class="inner">
        <h3>8733</h3>
        <p>今日新增设备</p>
      </div>
      <div class="icon">
        <i class="fa fa-mobile"></i>
      </div>
      <a href="/device" class="small-box-footer">
        总数：982273663 <i class="fa fa-arrow-circle-right"></i>
      </a>
    </div>
  </div>

</div>
@stop
