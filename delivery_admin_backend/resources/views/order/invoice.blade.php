<!-- resources/views/invoice.blade.php -->
<!DOCTYPE html>
<html>

<head>
    <title>{{ __('message.invoice') }}</title>
    <style>
        /* Modern invoice styling */
        body {
            color: #333;
            margin: 0;
            padding: 0;
            font-family: "DejaVu Sans", sans-serif;
            background-color: #f8f9fa;
            line-height: 1.6;
        }

        .invoice-container {
            max-width: 800px;
            margin: 30px auto;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            background-color: #ffffff;
            position: relative;
        }

        /* Header styling */
        .company-details {
            display: flex;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e9ecef;
        }

        .company-logo {
            width: 120px;
            height: 80px;
            margin-right: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa;
            border-radius: 4px;
        }

        .company-logo img {
            max-width: 100%;
            max-height: 80px;
            object-fit: contain;
        }

        .company-info h2 {
            margin: 0 0 10px 0;
            font-size: 24px;
            color: #2c3e50;
            font-weight: 700;
        }

        .invoice-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .invoice-title {
            font-size: 28px;
            font-weight: 700;
            color: #3498db;
            margin-bottom: 5px;
        }

        .invoice-details {
            text-align: right;
            color: #555;
        }

        .invoice-details div {
            margin-bottom: 5px;
        }

        .invoice-details strong {
            display: inline-block;
            width: 140px;
            margin-right: 10px;
            color: #333;
        }

        /* Address sections */
        .addresses {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .address-section {
            width: 48%;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }

        .address-title {
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 8px;
            color: #2c3e50;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 5px;
        }

        .address-content {
            word-wrap: break-word;
        }

        .payment-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }

        /* Table styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e9ecef;
        }

        th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #2c3e50;
        }

        .right-align {
            text-align: right;
        }

        .item-table th:first-child {
            width: 70%;
        }

        .item-table th:last-child {
            width: 30%;
        }

        .item-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        /* Total section */
        .total-section {
            margin-top: 30px;
            text-align: right;
        }

        .total-line {
            display: flex;
            justify-content: flex-end;
            padding: 8px 0;
        }

        .total-title {
            font-weight: bold;
            width: 150px;
            color: #2c3e50;
        }

        .total-value {
            width: 120px;
            text-align: right;
        }

        .grand-total {
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
            padding-top: 10px;
            border-top: 2px solid #e9ecef;
        }

        /* Footer */
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            color: #7f8c8d;
            font-size: 12px;
        }

        .footer-address {
            margin-bottom: 15px;
            text-align: center;
        }

        .footer-note {
            text-align: center;
            font-style: italic;
        }

        /* Placeholder for missing logo */
        .logo-placeholder {
            width: 100%;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #e9ecef;
            color: #7f8c8d;
            font-size: 14px;
            border-radius: 4px;
        }
    </style>
</head>

<body>
    <div class="invoice-container">
        <!-- Company details -->
        <div class="company-details">
            <div class="company-logo">
                @php
                    $logoUrl = null;
                    if (isset($invoice) && isset($invoice->image_url) && !empty($invoice->image_url)) {
                        $logoUrl = $invoice->image_url;
                    } elseif (isset($invoice) && method_exists($invoice, 'getFirstMediaUrl') && $invoice->getFirstMediaUrl('company_logo')) {
                        $logoUrl = $invoice->getFirstMediaUrl('company_logo');
                    } elseif (function_exists('getSingleMediaSettingImage') && isset($invoice)) {
                        $logoUrl = getSingleMediaSettingImage($invoice, 'company_logo');
                    }
                @endphp

                @if($logoUrl)
                    <img src="{{ $logoUrl }}" alt="Company Logo">
                @else
                    <div class="logo-placeholder">{{ optional($companyName)->value ?? 'Company Logo' }}</div>
                @endif
            </div>
            <div class="company-info">
                <h2>{{ optional($companyName)->value }}</h2>
                <div><strong>{{ __('message.contact_number') }}:</strong> {{ optional($companynumber)->value }}</div>
            </div>
        </div>

        <!-- Invoice header -->
        <div class="invoice-header">
            <div>
                <div class="invoice-title">{{ __('message.invoice') }}</div>
                <div class="invoice-number">#{{ optional($order)->id }}</div>
            </div>
            <div class="invoice-details">
                <div><strong>{{ __('message.invoice_date') }}:</strong> {{ $today }}</div>
                <div><strong>{{ __('message.order_date') }}:</strong> {{ date('d/m/Y', strtotime($order->created_at)) }}
                </div>
                <div><strong>{{ __('message.order_tracking') }}:</strong> {{ $order->milisecond }}</div>
            </div>
        </div>

        <!-- Address information -->
        <div class="addresses">
            <div class="address-section">
                <div class="address-title">{{ __('message.pickup_from') }}</div>
                <div class="address-content">{{ $order->pickup_point['address'] ?? 'N/A' }}</div>
            </div>
            <div class="address-section">
                <div class="address-title">{{ __('message.deliverd_to') }}</div>
                <div class="address-content">{{ $order->delivery_point['address'] ?? 'N/A' }}</div>
            </div>
        </div>

        <!-- Payment information -->
        <div class="payment-info">
            <div>
                <strong>{{ __('message.payment_via') }}:</strong>
                {{ ucfirst(optional($order->payment)->payment_type ?? 'N/A') }}
            </div>
            <div>
                <strong>{{ __('message.payment_date') }}:</strong>
                {{ isset($order->payment->created_at) ? date('d/m/Y', strtotime($order->payment->created_at)) : date('d/m/Y', strtotime($order->created_at)) }}
            </div>
        </div>

        <!-- Items table -->
        <table class="item-table">
            <thead>
                <tr>
                    <th>{{ __('message.description') }} ({{ optional($order)->parcel_type ?? 'Package' }})</th>
                    <th class="right-align">{{ __('message.price') }}</th>
                </tr>
            </thead>
            <tbody>
                @if($order->bid_type == 0)
                    <tr>
                        <td>{{ __('message.delivery_charges') }}</td>
                        <td class="right-align">{{ getPriceFormat($order->fixed_charges) }}</td>
                    </tr>
                    <tr>
                        <td>{{ __('message.distance_charge') }}</td>
                        <td class="right-align">{{ getPriceFormat($order->distance_charge) }}</td>
                    </tr>
                    <tr>
                        <td>{{ __('message.weight_charge') }}</td>
                        <td class="right-align">{{ getPriceFormat($order->weight_charge) }}</td>
                    </tr>
                    @if(!is_null($order->vehicle_charge) && $order->vehicle_charge > 0)
                        <tr>
                            <td>{{ __('message.vehicle_charge') }}</td>
                            <td class="right-align">{{ getPriceFormat($order->vehicle_charge) }}</td>
                        </tr>
                    @endif
                    @if(!is_null($order->insurance_charge) && $order->insurance_charge > 0)
                        <tr>
                            <td>{{ __('message.insurance_charge') }}</td>
                            <td class="right-align">{{ getPriceFormat($order->insurance_charge) }}</td>
                        </tr>
                    @endif
                @endif

                @php
                    $extra_charges_values = [];
                    $grand_total = 0;
                    $service_class = 'd-none';
                    $percentage = 0;
                    $extra_charges_texts = [];
                    if ($order->bid_type == 0) {
                        $sub_total = $order->fixed_charges + $order->distance_charge + $order->weight_charge +
                            ($order->vehicle_charge ?? 0) + ($order->insurance_charge ?? 0);
                    } else {
                        $sub_total = $order->total_amount;
                    }
                    if (is_array($order->extra_charges)) {
                        foreach ($order->extra_charges as $item) {
                            if (isset($item['value_type'])) {
                                $formatted_value = ($item['value_type'] == 'percentage') ? $item['value'] . '%' : getPriceFormat($item['value']);
                                if ($item['value_type'] == 'percentage') {
                                    $data_value = $sub_total * $item['value'] / 100;
                                    $key = str_replace('_', ' ', ucfirst($item['key']));
                                    $extra_charges_texts[] = $key . ' (' . $formatted_value . ')';
                                    $extra_charges_values[] = getPriceFormat($data_value);
                                } else {
                                    $key = str_replace('_', ' ', ucfirst($item['key']));
                                    $extra_charges_texts[] = $key . ' (' . $formatted_value . ')';
                                    $extra_charges_values[] = $formatted_value;
                                }
                            }
                        }
                        if (isset($item['value_type'])) {
                            $values = [];
                            $countFixed = 0;
                            foreach ($order->extra_charges as $item) {
                                if (in_array($item['value_type'], ['percentage', 'fixed'])) {
                                    if ($item['value_type'] == 'percentage') {
                                        $values[] = $sub_total * $item['value'] / 100;
                                    } elseif ($item['value_type'] == 'fixed') {
                                        $values[] = $item['value'];
                                    }
                                }
                            }
                            $percentage = array_sum($values);
                        }
                    }
                @endphp
            </tbody>
        </table>

        <!-- Total section -->
        <div class="total-section">
            @if($order->extra_charges != null)
                <div class="total-line">
                    <div class="total-title">{{ __('message.sub_total') }}</div>
                    <div class="total-value">{{ getPriceFormat($sub_total) }}</div>
                </div>
                @foreach ($extra_charges_texts as $index => $text)
                    <div class="total-line">
                        <div class="total-title">{{ $text }}</div>
                        <div class="total-value">{{ $extra_charges_values[$index] }}</div>
                    </div>
                @endforeach
            @endif
            <div class="total-line grand-total">
                <div class="total-title">{{ __('message.total') }}</div>
                <div class="total-value">{{ getPriceFormat($percentage + $sub_total) ?? getPriceFormat($sub_total) }}
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="footer-address">
                <strong>{{ __('message.address') }}:</strong> {{ optional($companyAddress)->value ?? 'N/A' }}
            </div>
            <div class="footer-note">
                <strong>{{ __('message.notes') }}:</strong>
                {{ __('message.this_report_was_generated_by_a_computer_and_does_not_require_a_signature_or_company_stamp_to_be_considered_valid') }}
            </div>
        </div>
    </div>
</body>

</html>