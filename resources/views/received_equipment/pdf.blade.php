<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>PAR - {{ $equipment->par_no }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 5px;
        }

        .header p {
            margin: 0;
            padding: 0;
            line-height: 1.2;
        }

        .title {
            font-weight: bold;
            font-size: 14px;
            text-align: center;
            margin: 10px 0 15px 0;
        }

        .agency {
            text-align: center;
            margin-bottom: 15px;
            font-size: 11px;
        }

        .par-info {
            text-align: right;
            margin-bottom: 10px;
            font-size: 11px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table.main-table th,
        table.main-table td {
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
            vertical-align: top;
            font-size: 11px;
        }

        th {
            font-weight: bold;
        }

        .entity-info {
            width: 100%;
            margin-bottom: 10px;
        }

        .entity-info td {
            border: none;
            padding: 2px;
            text-align: left;
            font-size: 11px;
        }

        .signatures {
            margin-top: 30px;
            width: 100%;
        }

        .signature-block {
            width: 48%;
            display: inline-block;
            vertical-align: top;
        }

        .signature-block.left {
            float: left;
        }

        .signature-block.right {
            float: right;
        }

        .signature-content {
            margin-top: 5px;
            font-size: 10px;
        }

        .signature-line {
            border-bottom: 1px solid #000;
            width: 100%;
        }

        .position {
            font-size: 9px;
            margin: 0;
        }

        .date-line {
            margin-top: 15px;
            border-bottom: 1px solid #000;
            width: 50%;
        }

        .description-cell {
            text-align: left;
            vertical-align: top;
        }

        .serial-numbers {
            text-align: left;
            padding-top: 5px;
            font-size: 10px;
        }

        /* Ensure text wrapping for long descriptions */
        .description-content {
            white-space: pre-line;
            word-wrap: break-word;
            text-align: left;
        }
        
        /* Property number cell */
        .property-cell {
            overflow-wrap: break-word;
            word-break: break-word;
            text-align: center;
        }
        
        /* Serial number cell */
        .serial-cell {
            text-align: left !important;
        }
    </style>
</head>

<body>
    <div class="header">
        <p>Republic of the Philippines</p>
        <p><b>PANGASINAN STATE UNIVERSITY</b></p>
        <p>Lingayen, Pangasinan</p>
    </div>

    <div class="title">PROPERTY ACKNOWLEDGMENT RECEIPT</div>

    <div class="agency">{{ $equipment->entity->branch->branch_name ?? 'PSU - LINGAYEN' }}<br>Agency</div>

    <table class="entity-info">
        <tr>
            <td width="20%">Entity Name:</td>
            <td width="50%">{{ $equipment->entity->name ?? 'Pangasinan State University' }}</td>
            <td width="30%" align="right">PAR No.: {{ $equipment->par_no }}</td>
        </tr>
        <tr>
            <td>Fund Cluster:</td>
            <td>{{ $equipment->entity->fundCluster->name ?? '' }}</td>
            <td></td>
        </tr>
    </table>

    <table class="main-table">
        <thead>
            <tr>
                <th width="8%">Quantity</th>
                <th width="8%">Unit</th>
                <th width="44%">Description</th>
                <th width="15%">Property<br>Number</th>
                <th width="12%">Date<br>Acquired</th>
                <th width="13%">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($equipment->descriptions as $description)
            @php
                // Calculate rows needed (main description + serial numbers header + items)
                $itemRows = count($description->items);
                $totalRows = 1 + 1 + $itemRows; // 1 for description, 1 for serial header
            @endphp
            
            <!-- Main row with description -->
            <tr>
                <td rowspan="{{ $totalRows }}">{{ $description->quantity }}</td>
                <td rowspan="{{ $totalRows }}">{{ $description->unit }}</td>
                <!-- Full description in one cell -->
                <td class="description-cell">
                    <div class="description-content">{{ $description->description }}</div>
                </td>
                <td class="property-cell"></td>
                <td></td>
                <td></td>
            </tr>

            <!-- Serial Numbers header -->
            <tr>
                <td class="serial-cell"><b>Serial Numbers:</b></td>
                <td class="property-cell"></td>
                <td></td>
                <td></td>
            </tr>

            <!-- Individual serial number items -->
            @foreach($description->items as $item)
            <tr>
                <td class="serial-cell">{{ $item->serial_no ?? 'N/A' }}</td>
                <td class="property-cell">{{ $item->property_no }}</td>
                <td>{{ \Carbon\Carbon::parse($item->date_acquired)->format('m/d/Y') }}</td>
                <td>₱{{ number_format($item->amount, 2) }}</td>
            </tr>
            @endforeach
            @endforeach
        </tbody>
    </table>

    <div class="signatures">
        <div class="signature-block left">
            <p>Received by:</p>
           
            <div class="signature-content">
                <p style="font-weight: bold; margin: 0;">{{ $equipment->received_by_name }}</p>
                <div class="signature-line"></div>
                <p class="position">Signature over Printed Name of End User</p>
                <p class="position">{{ $equipment->received_by_designation }}</p>
                <p class="position">Position/Office</p>
                <div class="date-line"></div>
                <p class="position">Date</p>
            </div>
        </div>

        <div class="signature-block right">
            <p>Issued by:</p>
        
            <div class="signature-content">
                <p style="font-weight: bold; margin: 0;">{{ $equipment->verified_by_name }}</p>
                <div class="signature-line"></div>
                <p class="position">Signature over Printed Name of Supply and/or Property Officer</p>
                <p class="position"><b>{{ $equipment->verified_by_designation }}</b></p>
                <p class="position">Position/Office</p>
                <div class="date-line"></div>
                <p class="position">Date</p>
            </div>
        </div>
    </div>
</body>

</html>