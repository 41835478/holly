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
</div>
@stop
