<table>
<thead>
    <tr>
        <th>Description</th>
        <th>Quantity</th>
        <th>Unit</th>
        <th>Items</th>
        <th>Actions</th>
    </tr>
</thead>
<tbody>
@foreach ($descriptions as $desc)
    <tr>
        <td>{{ $desc->description }}</td>
        <td>{{ $desc->quantity }}</td>
        <td>{{ $desc->unit }}</td>
        <td>
            <ul>
                @forelse ($desc->items as $item)
                    <li>
                        <strong>{{ $item->name ?? 'No name available' }}</strong><br>
                        Old Property No: {{ $item->property_no ?? 'N/A' }}<br>
                        @if (!empty($item->serial_no))
                            Serial: {{ $item->serial_no }}<br>
                        @endif
                        @if(isset($fundMatches[$item->item_id]))
                            Fund: {{ $fundMatches[$item->item_id]->account_code }} - {{ $fundMatches[$item->item_id]->account_title }}
                        @endif
                    </li>
                @empty
                    <li>No items found</li>
                @endforelse
            </ul>
        </td>
        <td>
            @foreach ($desc->items as $item)
                <form action="{{ route('inventory.store') }}" method="POST" style="display: inline-block; margin-bottom: 5px;">
                    @csrf
                    <input type="hidden" name="old_property_no" value="{{ $item->property_no }}">
                    <input type="hidden" name="description" value="{{ $desc->description }}">
                    <input type="hidden" name="unit" value="{{ $desc->unit }}">
                    <input type="hidden" name="article_item" value="{{ $item->name }}">
                    <button type="submit" class="btn btn-primary btn-sm">Add to Inventory</button>
                </form>
            @endforeach
        </td>
    </tr>
@endforeach
</tbody>
</table>

{{ $descriptions->links() }}