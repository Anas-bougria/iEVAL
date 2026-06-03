@extends('layouts.app')
@section('title', 'Nouveau module')
@section('section', 'Administration · Modules')
@section('content')
<div class="card card-pad max-w-3xl">@include('admin.modules._form')</div>
@endsection
