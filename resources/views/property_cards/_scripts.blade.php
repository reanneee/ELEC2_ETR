@section('scripts')
<script>
let movementIndex = {{ isset($property_card) ? $property_card->movements->count() : 1 }};

document.getElementById('add-row').addEventListener('click', function () {
    fetch("{{ route('property_cards.movement_row') }}")
        .then(response => response.text())
        .then(html => {
            html = html.replace(/__INDEX__/g, movementIndex);
            const tbody = document.querySelector('#movement-table tbody');
            const newRow = document.createElement('tr');
            newRow.innerHTML = html;
            tbody.appendChild(newRow);
            movementIndex++;
        });
});

document.addEventListener('click', function (e) {
    if (e.target.classList.contains('remove-row')) {
        e.target.closest('tr').remove();
    }
});
</script>
@endsection
