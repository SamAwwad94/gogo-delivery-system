<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('message.delivery_labels') }}</title>
    <style>
        @page {
            size: 4in 6in;
            margin: 0;
        }

        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
            color: #333;
            font-size: 12px;
            line-height: 1.4;
        }

        .print-controls {
            background: #fff;
            padding: 15px;
            margin-bottom: 20px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .print-controls button {
            background: #3498db;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            transition: background 0.3s;
            margin: 0 5px;
        }

        .print-controls button:hover {
            background: #2980b9;
        }

        .label-container {
            width: 4in;
            height: 6in;
            margin: 0 auto 30px;
            padding: 0.2in;
            box-sizing: border-box;
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
            page-break-after: always;
        }

        .label-header {
            display: flex;
            padding-bottom: 0.15in;
            border-bottom: 1px solid #ddd;
            margin-bottom: 0.15in;
        }

        .logo-section {
            flex: 0 0 1in;
        }

        .logo-section img {
            max-width: 1in;
            max-height: 0.8in;
            object-fit: contain;
        }

        .company-section {
            flex: 1;
            padding-left: 0.1in;
        }

        .company-name {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 0.05in;
        }

        .tracking-section {
            text-align: center;
            margin: 0.15in 0;
        }

        .tracking-title {
            text-transform: uppercase;
            font-weight: bold;
            font-size: 11px;
            margin-bottom: 0.05in;
        }

        .barcode-image {
            width: 100%;
            max-height: 0.6in;
            margin: 0.05in 0;
        }

        .tracking-number {
            font-size: 14px;
            font-weight: bold;
            margin-top: 0.05in;
            letter-spacing: 1px;
        }

        .address-section {
            margin: 0.1in 0;
            padding: 0.1in;
            border: 1px solid #ddd;
            border-radius: 0.1in;
        }

        .address-title {
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 0.05in;
            color: #555;
            border-bottom: 1px dashed #eee;
            padding-bottom: 0.05in;
        }

        .address-content {
            margin-top: 0.05in;
        }

        .address-name {
            font-weight: bold;
        }

        .address-details {
            margin-top: 0.05in;
        }

        .contact {
            margin-top: 0.05in;
            font-size: 11px;
        }

        .contact strong {
            display: inline-block;
            width: 0.5in;
        }

        .package-info {
            display: flex;
            margin: 0.1in 0;
            font-size: 11px;
        }

        .info-item {
            flex: 1;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 0.05in;
            margin: 0 0.05in;
            text-align: center;
        }

        .info-item:first-child {
            margin-left: 0;
        }

        .info-item:last-child {
            margin-right: 0;
        }

        .info-title {
            font-weight: bold;
            color: #555;
            font-size: 9px;
            text-transform: uppercase;
        }

        .info-value {
            font-weight: bold;
            margin-top: 0.03in;
            font-size: 11px;
        }

        .shipping-instructions {
            font-size: 11px;
            margin: 0.1in 0;
            padding: 0.1in;
            background: #f9f9f9;
            border-radius: 4px;
        }

        .instructions-title {
            font-weight: bold;
            margin-bottom: 0.05in;
            color: #555;
        }

        .shipping-symbols {
            display: flex;
            flex-wrap: wrap;
            margin-top: 0.1in;
            justify-content: space-around;
        }

        .symbol-icon {
            width: 0.3in;
            height: 0.3in;
            object-fit: contain;
            margin: 0.05in;
        }

        .order-count {
            margin: 0 5px;
            background: #34495e;
            color: white;
            border-radius: 4px;
            padding: 3px 8px;
            font-size: 10px;
            vertical-align: middle;
        }

        @media print {
            .print-controls {
                display: none;
            }

            body {
                background: white;
            }

            .label-container {
                box-shadow: none;
                margin-bottom: 0;
            }
        }
    </style>
</head>

<body>
    <div class="print-controls">
        <span>{{ __('message.total_labels') }}: <span class="order-count">{{ count($orders) }}</span></span>
        <button onclick="window.print()">{{ __('message.print_all_labels') }}</button>
        <button onclick="window.location.href='{{ url()->previous() }}'">{{ __('message.back') }}</button>
    </div>

    @foreach($orders as $order)
        <div class="label-container">
            <!-- Label Header with Logo and Company Info -->
            <div class="label-header">
                <div class="logo-section">
                    @if(isset($invoice) && isset($invoice->image_url) && !empty($invoice->image_url))
                        <img src="{{ $invoice->image_url }}" alt="Company Logo">
                    @else
                        <div class="company-name">{{ optional($companyName)->value ?? 'Gogo Delivery' }}</div>
                    @endif
                </div>
                <div class="company-section">
                    <div class="company-name">{{ optional($companyName)->value ?? 'Gogo Delivery' }}</div>
                    <div>{{ optional($companyAddress)->value ?? 'Company Address' }}</div>
                    @if($labelnumber == 1)
                        <div>{{ optional($companyNumber)->value ?? 'Phone Number' }}</div>
                    @endif
                    @if($order->is_shipped == 1)
                        <div><strong>{{ __('message.shipped_via') }}:</strong>
                            {{ optional($order->couriercompany)->name ?? 'N/A' }}</div>
                    @endif
                </div>
            </div>

            <!-- Tracking Section -->
            <div class="tracking-section">
                <div class="tracking-title">{{ __('message.tracking_number') }}</div>
                <img src="data:image/png;base64,{{ $barcodeBase64[$order->id] }}" alt="Barcode" class="barcode-image">
                <div class="tracking-number">{{ $order->milisecond }}</div>
            </div>

            <!-- Package Information -->
            <div class="package-info">
                <div class="info-item">
                    <div class="info-title">{{ __('message.order_date') }}</div>
                    <div class="info-value">{{ $order->formattedCreatedAt }}</div>
                </div>
                @if($order->is_shipped == 1)
                    <div class="info-item">
                        <div class="info-title">{{ __('message.shipping_date') }}</div>
                        <div class="info-value">{{ $order->formattedShippedAt }}</div>
                    </div>
                @endif
                <div class="info-item">
                    <div class="info-title">{{ __('message.parcel_type') }}</div>
                    <div class="info-value">{{ $order->parcel_type ?? 'Package' }}</div>
                </div>
            </div>

            <!-- From Address -->
            <div class="address-section">
                <div class="address-title">{{ __('message.from') }}</div>
                <div class="address-content">
                    <div class="address-name">{{ $order->pickup_point['name'] ?? 'Sender' }}</div>
                    <div class="address-details">{{ $order->pickup_point['address'] ?? 'Address' }}</div>
                    <div class="contact">
                        <strong>{{ __('message.phone') }}:</strong> {{ $order->pickup_point['contact_number'] ?? 'N/A' }}
                    </div>
                </div>
            </div>

            <!-- To Address -->
            <div class="address-section">
                <div class="address-title">{{ __('message.to') }}</div>
                <div class="address-content">
                    <div class="address-name">{{ $order->delivery_point['name'] ?? 'Recipient' }}</div>
                    <div class="address-details">{{ $order->delivery_point['address'] ?? 'Address' }}</div>
                    <div class="contact">
                        <strong>{{ __('message.phone') }}:</strong> {{ $order->delivery_point['contact_number'] ?? 'N/A' }}
                    </div>
                </div>
            </div>

            <!-- Shipping Instructions if available -->
            @if(isset($order->delivery_point['instruction']) && $order->delivery_point['instruction'] != null)
                <div class="shipping-instructions">
                    <div class="instructions-title">{{ __('message.shipping_Ins') }}</div>
                    <div>{{ $order->delivery_point['instruction'] }}</div>
                </div>
            @endif

            <!-- Package Symbols -->
            @php
                $packagingSymbols = json_decode($order->packaging_symbols, true);
            @endphp
            @if (is_array($packagingSymbols) && count($packagingSymbols) > 0)
                <div class="shipping-symbols">
                    @foreach ($packagingSymbols as $symbol)
                        @php
                            $icon = '';
                            switch ($symbol['key']) {
                                case 'fragile':
                                    $icon = asset('images/fragile.png');
                                    break;
                                case 'keep_dry':
                                    $icon = asset('images/keep-dry.png');
                                    break;
                                case 'this_way_up':
                                    $icon = asset('images/up-arrows-couple-sign-for-packaging.png');
                                    break;
                                case 'do_not_stack':
                                    $icon = asset('images/do-not-stack.png');
                                    break;
                                case 'temperature_sensitive':
                                    $icon = asset('images/temperature.png');
                                    break;
                                case 'recycle':
                                    $icon = asset('images/symbols.png');
                                    break;
                                case 'do_not_use_hooks':
                                    $icon = asset('images/do-not-hook.png');
                                    break;
                                case 'explosive_material':
                                    $icon = asset('images/flammable.png');
                                    break;
                                case 'hazardous_material':
                                    $icon = asset('images/hazard.png');
                                    break;
                                case 'perishable':
                                    $icon = asset('images/ice-cube.png');
                                    break;
                                case 'do_not_open_with_sharp_objects':
                                    $icon = asset('images/knives.png');
                                    break;
                                case 'bike_delivery':
                                    $icon = asset('images/fast-delivery.png');
                                    break;
                            }
                        @endphp
                        @if ($icon)
                            <img src="{{ $icon }}" alt="{{ $symbol['title'] }}" class="symbol-icon" title="{{ $symbol['title'] }}">
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
    @endforeach

    <script>
        window.onload = function () {
            // Auto-trigger print dialog when specifically requested via URL param
            if (window.location.search.includes('autoprint=1')) {
                setTimeout(function () {
                    window.print();
                }, 500);
            }
        };
    </script>
</body>

</html>