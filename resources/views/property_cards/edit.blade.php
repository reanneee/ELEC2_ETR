@extends('layouts.app')

@section('content')
<h1>Edit Property Card</h1>
<form method="POST" action="{{ route('property_cards.update', $property_card) }}">
    @method('PUT')
    @include('property_cards._form')
</form>
@endsection

@include('property_cards._scripts')
