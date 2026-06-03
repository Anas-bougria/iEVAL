@extends('layouts.app')
@section('title', 'Nouvelle évaluation')
@section('section', 'Enseignant · Évaluations')
@section('content')
<div class="card card-pad max-w-4xl">@include('teacher.evaluations._form')</div>
@endsection
