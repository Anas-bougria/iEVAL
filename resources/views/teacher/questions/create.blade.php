@extends('layouts.app')
@section('title', 'Nouvelle question — ' . $evaluation->title)
@section('section', 'Enseignant · Questions')
@section('content')
<div class="card card-pad max-w-4xl">@include('teacher.questions._form')</div>
@endsection
