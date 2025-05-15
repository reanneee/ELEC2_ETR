@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <h1>Welcome, {{ Auth::user()->name }}!</h1>
    <a href="{{ route('entities.index') }}" class="btn btn-primary mt-3">Manage Entities</a>
@endsection
