@extends('layouts.app')
@section('title', $evaluation->title)
@section('section', 'Enseignant · Évaluation')
@section('content')
<div class="card card-pad max-w-4xl">@include('teacher.evaluations._form')</div>
@endsection
