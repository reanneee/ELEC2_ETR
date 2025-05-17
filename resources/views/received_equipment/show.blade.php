@extends('layouts.app')

@section('content')
<h1>Equipment Details - PAR #{{ $equipment->par_no }}</h1>

<p><strong>Entity:</strong> {{ $equipment->entity->entity_name ?? 'N/A' }}</p>
<p><strong>Date Acquired:</strong> {{ $equipment->date_acquired->format('Y-m-d') }}</p>
<p><strong>Amount:</strong> ₱{{ number_format($equipment->amount, 2) }}</p>

<h3>Descriptions</h3>
@foreach($equipment->descriptions as $description)
    <div>
        <strong>{{ $description->description }}</strong> - 
        {{ $description->quantity }} {{ $description->unit }}

        <ul>
            @foreach($description->items as $item)
                <li>
                    Serial: {{ $item->serial_no }},
                    Property No: {{ $item->property_no }},
                    Amount: ₱{{ number_format($item->amount, 2) }}
                </li>
            @endforeach
        </ul>
    </div>
@endforeach

<a href="{{ route('received_equipment.index') }}">Back</a>
@endsection
