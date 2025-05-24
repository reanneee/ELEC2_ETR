<!DOCTYPE html>
<html>
<head>
    <title>Property Card PDF</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid black; padding: 5px; text-align: center; }
        .info p { margin: 2px 0; }
    </style>
</head>
<body>

<div class="header">
    <h2>PROPERTY CARD</h2>
</div>

<div class="info">
    <p><strong>Entity:</strong> {{ $property_card->entity->entity_name ?? 'N/A' }}</p>
    <p><strong>Property Number:</strong> {{ $property_card->property_number }}</p>
    <p><strong>Description:</strong> {{ $property_card->description }}</p>
</div>

@if($property_card->movements->count())
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Reference / PAR No.</th>
                <th>Receipt Qty</th>
                <th>Movement Qty</th>
                <th>Balance</th>
                <th>Officer (Issue/Transfer/Disposal)</th>
                <th>Amount</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            @foreach($property_card->movements as $movement)
                <tr>
                    <td>{{ $movement->movement_date }}</td>
                    <td>{{ $movement->par }}</td>
                    <td>{{ $movement->qty ?? '' }}</td>
                    <td>{{ $movement->movement_qty ?? '' }}</td>
                    <td>{{ $movement->balance }}</td>
                    <td>{{ $movement->office_officer }}</td>
                    <td>{{ number_format($movement->amount, 2) }}</td>
                    <td>{{ $movement->remarks }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <p>No movement records available.</p>
@endif

<script>
    window.onload = function() { window.print(); }
</script>
</body>
</html>
