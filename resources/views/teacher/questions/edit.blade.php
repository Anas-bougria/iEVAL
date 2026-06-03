@extends('layouts.app')
@section('title', 'Modifier question')
@section('section', 'Enseignant · ' . $evaluation->title)
@section('content')
<div class="card card-pad max-w-4xl">@include('teacher.questions._form')</div>
@endsection
