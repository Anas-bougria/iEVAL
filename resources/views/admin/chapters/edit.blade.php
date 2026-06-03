@extends('layouts.app')
@section('title', $chapter->title)
@section('section', 'Administration · Chapitres')
@section('content')
<div class="card card-pad max-w-3xl">@include('admin.chapters._form')</div>
@endsection
