@extends('layouts.app')

@section('content')
<h1>Create Property Card</h1>
<form method="POST" action="{{ route('property_cards.store') }}">
    @include('property_cards._form')
</form>
@endsection

@include('property_cards._scripts')
