@extends('layouts.admin.default')

@section('title', '管理员列表')

@push('js')
{!! $dataTable->scripts() !!}
@include('admin.partials.admin-user-datatable')
@endpush

@section('content')
<div class="row">
  <div class="col-xs-12">
    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title">管理员列表</h3>
      </div>
      <div class="box-body">
        {!! $dataTable->table() !!}
      </div>
    </div>
  </div>
</div>
@stop
