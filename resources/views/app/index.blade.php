@extends('layouts.app.default')

@section('content')
<div class="content">
  <p>
    {{ $client->getUserAgent() }}
  </p>
</div>
@stop
