@extends('layouts.site.default')

{{-- @section('keywords', 'keywords') --}}
{{-- @section('description', 'description') --}}

@push('css')
<style>
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
