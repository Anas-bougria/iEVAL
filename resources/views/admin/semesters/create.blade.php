@extends('layouts.app')
@section('title', 'Nouveau semestre')
@section('section', 'Administration · Semestres')
@section('content')
<div class="card card-pad max-w-2xl">
    @include('admin.semesters._form')
</div>
@endsection
