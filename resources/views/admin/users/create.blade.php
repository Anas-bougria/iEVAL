@extends('layouts.app')
@section('title', 'Nouvel utilisateur')
@section('section', 'Administration · Utilisateurs')

@section('content')
<div class="card card-pad max-w-3xl">
    @include('admin.users._form', ['user' => $user])
</div>
@endsection
