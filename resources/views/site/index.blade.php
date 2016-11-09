@extends('layouts.site.default')

{{-- @section('full-title', 'A long title for the homepage') --}}
{{-- @section('keywords', 'keywords') --}}
{{-- @section('description', 'description') --}}

@push('css')
<style type="text/css">
.example {
  text-align: center;
  color: #e17;
  margin: 140px 0;
}
</style>
@endpush

@push('js')
<script>
$(function () {
  console.log('Welcome!');
});
</script>
@endpush

@section('content')
<div class="content">
  <div class="example">
    <h1>Welcome</h1>
  </div> <!-- //.example -->
</div> <!-- //.content -->
@stop
