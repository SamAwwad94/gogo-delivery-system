<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Invoice</title>
    <style>
        body {
            color: #555;
            margin: 0;
            padding: 0;
            font-family: "DejaVu Sans", sans-serif;
            font-size: 12px;
        }

        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            padding: .5cm;
            git add . git commit -m "Checkpoint: working login page"

            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            background-color: #ffffff;
        }

        .myheader {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #ddd;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .mydetails h2 {
            margin: 0;
            font-size: 20px;
            color: #333;
        }

        .mydetails .invoice-details {
            text-align: right;
        }

        .addresspickupdetails {
            text-align: left;
        }

        .addressdetails {
            text-align: right;
        }

        .mydetails {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }

        .details,
        .items,
        .totals {
            width: 100%;
            margin-bottom: 0;
            border-collapse: collapse;
        }

        .mydetails td {
            padding: 5px;
            vertical-align: top;
        }

        .details td,
        .items td,
        .items th,
        .totals td {
            padding: 8px;
            border: 1px solid #ddd;
        }

        .details td {
            width: 50%;
        }

        .items th {
            background-color: #f0f0f0;
            text-align: left;
        }

        .totals {
            text-align: right;
        }

        .totalsfinal {
            font-weight: bold;
            font-size: 16px;
        }

        .totals td:last-child {
            font-weight: bold;
        }

        .note {
            margin-top: 20px;
            font-size: 11px;
            color: #777;
        }

        .address {
            text-align: center;
            font-size: 11px;
            color: #555;
            margin: 5px 0;
        }

        .logo-container {
            max-width: 80px;
            max-height: 80px;
        }

        .logo-container img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        /* Ensure proper table width */
        table {
            border-collapse: collapse;
            width: 100%;
        }

        /* Print controls for preview */
        .print-controls {
            background: #fff;
            padding: 10px;
            margin-bottom: 15px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .print-controls button {
            background: #3498db;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            margin: 0 5px;
        }

        .dropdown.float-right {
            float: right;
            margin-right: 0.5rem;
        }

        @media print {
            .print-controls {
                display: none;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
</head>

<body>
    <div class="print-controls">
        <button onclick="window.print()">Print Invoice</button>
        <div class="dropdown d-inline-block">
            <button class="btn btn-md btn-success" type="button" id="previewDropdown" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                <i class="fa-regular fa-eye"></i>
            </button>
            <div class="dropdown-menu" aria-labelledby="previewDropdown">
                <a class="dropdown-item" href="{{ route('previousinvoice', ['view' => 1]) }}" target="_blank">
                    View in Browser
                </a>
                <a class="dropdown-item" href="{{ route('previousinvoice') }}" target="_blank">
                    Download PDF
                </a>
            </div>
        </div>

        <div class="dropdown float-right mr-2">
            <button class="btn btn-md btn-success" type="button" id="previewDropdownFloat" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                <i class="fa-regular fa-eye"></i>
            </button>
            <div class="dropdown-menu" aria-labelledby="previewDropdownFloat">
                <a class="dropdown-item" href="{{ route('previousinvoice', ['view' => 1]) }}" target="_blank">
                    View in Browser
                </a>
                <a class="dropdown-item" href="{{ route('previousinvoice') }}" target="_blank">
                    Download PDF
                </a>
            </div>
        </div>
    </div>

    <div class="invoice-container">
        <table class="mydetails">
            <tr>
                <td>
                    @php
                        // Get the company data from settings
                        $companyName = \App\Models\Setting::where('key', 'company_name')
                            ->where('type', 'order_invoice')
                            ->first();

                        $companyAddress = \App\Models\Setting::where('key', 'company_address')
                            ->where('type', 'order_invoice')
                            ->first();

                        // Get the company logo path from settings
                        $companyLogoPath = \App\Models\Setting::where('key', 'company_logo')
                            ->where('type', 'order_invoice')
                            ->value('value');

                        // Build the logo URL
                        $logoUrl = '';

                        // First check if we have a stored path and the file exists
                        if ($companyLogoPath && file_exists(public_path($companyLogoPath))) {
                            $logoUrl = asset($companyLogoPath);
                        } else {
                            // Fallback to default logo
                            $logoUrl = asset('images/logos/default-company-logo.png');

                            // Create directory if it doesn't exist
                            if (!file_exists(public_path('images/logos'))) {
                                mkdir(public_path('images/logos'), 0755, true);
                            }
                        }

                        // Get the company phone number from settings
                        $companyNumber = \App\Models\Setting::where('key', 'company_contact')
                            ->where('type', 'order_invoice')
                            ->first();
                    @endphp

                    <div class="logo-container">
                        @if(!empty($logoUrl))
                            <img src="{{ $logoUrl }}" alt="Company Logo">
                        @else
                            <!-- Text fallback if no image available -->
                            <div
                                style="width:80px;height:80px;background:#f5f5f5;display:flex;align-items:center;justify-content:center;">
                                <span>Logo</span>
                            </div>
                        @endif
                    </div>

                    <h2>{{ optional($companyName)->value ?: 'Company Name' }}</h2>
                    <strong>Contact Number:</strong>
                    {{ optional($companyNumber)->value ?: 'N/A' }}<br>
                    <strong>Address:</strong>
                    {{ optional($companyAddress)->value ?: 'Company Address Here' }}
                </td>
                <td class="invoice-details">
                    <strong>Invoice No</strong>
                    @if(isset($invoice) && isset($invoice->id))
                        {{ $invoice->id }}
                    @else
                        8021
                    @endif
                    <br>
                    <strong>Invoice Date</strong> {{ $today }}<br>
                    <strong>Order Date</strong>
                    @if(isset($order) && isset($order->created_at))
                        {{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y') }}
                    @else
                        {{ \Carbon\Carbon::now()->format('d/m/Y') }}
                    @endif
                    <br>
                    <strong>Order Tracking:</strong>
                    @if(isset($order) && isset($order->order_number))
                        {{ $order->order_number }}
                    @else
                        1650230335000
                    @endif
                    <br>
                </td>
            </tr>
        </table>
        <table class="mydetails">
            <tr>
                <td class="addresspickupdetails">
                    <strong>Pickup From</strong><br>
                    @if(isset($order) && isset($order->pickup_point))
                        {{ $order->pickup_point }}
                    @else
                        Rajkot, Gujarat, India
                    @endif
                </td>
                <td class="addressdetails">
                    <strong>Delivered To</strong><br>
                    @if(isset($order) && isset($order->delivery_point))
                        {{ $order->delivery_point }}
                    @else
                        Rajkot, Gujarat, India
                    @endif
                </td>
            </tr>
        </table>
        <table class="mydetails">
            <tr>
                <td class="addresspickupdetails">
                    <strong>Payment Via:</strong>
                    @if(isset($order) && isset($order->payment_type))
                        {{ $order->payment_type }}
                    @else
                        Cash
                    @endif
                </td>
                <td class="addressdetails">
                    <strong>Payment Date:</strong>
                    @if(isset($order) && isset($order->payment_date))
                        {{ \Carbon\Carbon::parse($order->payment_date)->format('d/m/Y') }}
                    @else
                        {{ \Carbon\Carbon::now()->format('d/m/Y') }}
                    @endif
                </td>
            </tr>
        </table>

        <table class="items">
            <thead>
                <tr>
                    <th class="addresspickupdetails">Description (Document)</th>
                    <th class="addressdetails">Price</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($order) && isset($order->delivery_charges))
                    <tr>
                        <td>Delivery Charges</td>
                        <td class="addressdetails">${{ number_format($order->delivery_charges, 2) }}</td>
                    </tr>
                @else
                    <tr>
                        <td>Delivery Charges</td>
                        <td class="addressdetails">${{ number_format(30.00, 2) }}</td>
                    </tr>
                @endif

                @if(isset($order) && isset($order->distance_charge))
                    <tr>
                        <td>Distance Charge</td>
                        <td class="addressdetails">${{ number_format($order->distance_charge, 2) }}</td>
                    </tr>
                @else
                    <tr>
                        <td>Distance Charge</td>
                        <td class="addressdetails">${{ number_format(50.00, 2) }}</td>
                    </tr>
                @endif

                @if(isset($order) && isset($order->weight_charge))
                    <tr>
                        <td>Weight Charge</td>
                        <td class="addressdetails">${{ number_format($order->weight_charge, 2) }}</td>
                    </tr>
                @else
                    <tr>
                        <td>Weight Charge</td>
                        <td class="addressdetails">${{ number_format(40.00, 2) }}</td>
                    </tr>
                @endif

                @if(isset($order) && isset($order->vehicle_charge))
                    <tr>
                        <td>Vehicle Charge</td>
                        <td class="addressdetails">${{ number_format($order->vehicle_charge, 2) }}</td>
                    </tr>
                @else
                    <tr>
                        <td>Vehicle Charge</td>
                        <td class="addressdetails">${{ number_format(300.00, 2) }}</td>
                    </tr>
                @endif
            </tbody>
        </table>
        <table class="totals">
            <tr class="totalsfinal">
                <td>Total</td>
                <td>
                    @if(isset($order) && isset($order->total_amount))
                        ${{ number_format((float) $order->total_amount, 2) }}
                    @else
                        ${{ number_format((float) 420.00, 2) }}
                    @endif
                </td>
            </tr>
        </table>
        <p class="address"><strong>Address:</strong></p>
        <p class="address">{{ optional($companyAddress)->value ?: 'Company Address Here' }}</p>
        <p class="note">
            <strong>Notes:</strong>
            This report was generated by a computer and does not require a signature or company stamp to be considered
            valid.
        </p>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-print functionality
        if (window.location.search.includes('autoprint=1')) {
            window.onload = function () {
                setTimeout(function () { window.print(); }, 500);
            };
        }
    </script>
</body>

</html>