<script>
const descriptions = window.descriptions;

const spreadsheet = jspreadsheet(document.getElementById('spreadsheet'), {
    data: [[]],
    columns: [
        { type: 'text', title: 'Qty', width: 50 },
        { type: 'text', title: 'Unit', width: 70 },
        { type: 'autocomplete', title: 'Description', width: 250, source: descriptions },
        { type: 'text', title: 'Serial No.', width: 150 },
        { type: 'text', title: 'Property No.', width: 150 },
        { type: 'calendar', title: 'Date Acquired', width: 130, options: { format: 'YYYY-MM-DD' } },
        { type: 'text', title: 'Amount', width: 100 },
    ],
    minDimensions: [7, 10]
});

document.querySelector('form').addEventListener('submit', function () {
    document.getElementById('table_data').value = JSON.stringify(spreadsheet.getData());
});

</script>
