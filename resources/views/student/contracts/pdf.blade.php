<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Smart Rental Contract PDF</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #2d1f1f;
            line-height: 1.65;
            margin: 32px;
            background: #fffafa;
        }

        .header {
            border-bottom: 2px solid #c92a2a;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }

        /* LOGO */
        .logo-img {
            width: 42px;
            height: 42px;
            object-fit: contain;
            margin-right: 10px;
            vertical-align: middle;
        }

        .brand {
            display: inline-block;
            vertical-align: middle;
        }

        .brand-title {
            font-size: 20px;
            font-weight: bold;
            color: #4a2c2a;
        }

        .brand-sub {
            font-size: 11px;
            color: #7a5a5a;
        }

        .doc-title {
            margin-top: 18px;
            font-size: 18px;
            font-weight: bold;
            color: #4a2c2a;
        }

        .doc-sub {
            margin-top: 4px;
            color: #666;
            font-size: 11px;
        }

        .section {
            margin-top: 20px;
        }

        .section-title {
            font-size: 13px;
            font-weight: bold;
            color: #c92a2a;
            margin-bottom: 8px;
        }

        .box {
            border: 1px solid #efd6d6;
            border-radius: 8px;
            padding: 12px;
            background: #fff7f7;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 6px 0;
            vertical-align: top;
        }

        .label {
            width: 32%;
            color: #6a3d3a;
            font-weight: bold;
        }

        ul {
            margin: 0;
            padding-left: 18px;
        }

        li {
            margin-bottom: 6px;
        }

        .sign-box {
            margin-top: 25px;
            border: 1px solid #efd6d6;
            padding: 14px;
            background: #fff7f7;
            border-radius: 8px;
        }

        .status {
            display: inline-block;
            padding: 4px 10px;
            font-size: 11px;
            font-weight: bold;
            border-radius: 999px;
            background: #dcfce7;
            color: #166534;
        }

        .signature-image {
            margin-top: 12px;
            max-height: 110px;
            border: 1px solid #efd6d6;
            background: #ffffff;
            padding: 8px;
        }

        .footer {
            margin-top: 35px;
            font-size: 10px;
            color: #777;
            text-align: center;
            border-top: 1px solid #efd6d6;
            padding-top: 10px;
        }
    </style>
</head>

<body>
    @php
        $contractNo = $contract->contract_no ?? ('CTR-' . str_pad($contract->id, 6, '0', STR_PAD_LEFT));
    @endphp

    <!-- HEADER -->
    <div class="header">

        <img src="{{ public_path('images/logo.png') }}" class="logo-img">

        <div class="brand">
            <div class="brand-title">Smart Rental</div>
            <div class="brand-sub">MSU Student Accommodation Portal</div>
        </div>

        <div class="doc-title">Rental Agreement Contract</div>
        <div class="doc-sub">Contract No: {{ $contractNo }}</div>
    </div>

    <!-- SECTION 1 -->
    <div class="section">
        <div class="section-title">1. Contract Overview</div>
        <div class="box">
            This agreement confirms the tenancy arrangement between the student tenant and the landlord under the Smart Rental system. The tenant acknowledges the rental terms, payment obligations, and property responsibilities stated in this contract.
        </div>
    </div>

    <!-- SECTION 2 -->
    <div class="section">
        <div class="section-title">2. Parties Involved</div>
        <div class="box">
            <table>
                <tr>
                    <td class="label">Student Tenant</td>
                    <td>{{ $contract->student->name ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Landlord</td>
                    <td>{{ $contract->landlord->name ?? '-' }}</td>
                </tr>
            </table>
        </div>
    </div>

    <!-- SECTION 3 -->
    <div class="section">
        <div class="section-title">3. Rental Details</div>
        <div class="box">
            <table>
                <tr>
                    <td class="label">Room Title</td>
                    <td>{{ $contract->room_title ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Room Type</td>
                    <td>{{ $contract->room_type ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Start Date</td>
                    <td>{{ optional($contract->start_date)->format('d M Y') ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">End Date</td>
                    <td>{{ optional($contract->end_date)->format('d M Y') ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Monthly Rent</td>
                    <td>RM {{ number_format((float)$contract->monthly_rent, 2) }}</td>
                </tr>
                <tr>
                    <td class="label">Deposit Amount</td>
                    <td>RM {{ number_format((float)$contract->deposit_amount, 2) }}</td>
                </tr>
                <tr>
                    <td class="label">Total Amount</td>
                    <td>RM {{ number_format((float)$contract->total_amount, 2) }}</td>
                </tr>
            </table>
        </div>
    </div>

    <!-- SECTION 4 -->
    <div class="section">
        <div class="section-title">4. Tenant Responsibilities</div>
        <div class="box">
            <ul>
                <li>The tenant must pay monthly rent on or before the due date.</li>
                <li>The tenant must keep the room and property in clean and reasonable condition.</li>
                <li>The tenant is responsible for damages caused during the tenancy period.</li>
                <li>Illegal activities, disturbances, and unauthorized occupants are strictly prohibited.</li>
                <li>The landlord may deduct repair or replacement costs from the deposit where necessary.</li>
            </ul>
        </div>
    </div>

    <!-- SECTION 5 -->
    <div class="section">
        <div class="section-title">5. Agreement Confirmation</div>
        <div class="sign-box">
            <div><strong>Status:</strong> <span class="status">{{ $contract->is_signed ? 'SIGNED' : 'PENDING' }}</span></div>
            <div style="margin-top:8px;"><strong>Agreed To Terms:</strong> {{ $contract->agreed_to_terms ? 'Yes' : 'No' }}</div>
            <div><strong>Signed Name:</strong> {{ $contract->signed_name ?? '-' }}</div>
            <div><strong>Signed At:</strong> {{ optional($contract->signed_at)->format('d M Y, h:i A') ?? '-' }}</div>

            @if($contract->signature_data)
                <div style="margin-top: 12px;"><strong>Signature:</strong></div>
                <div class="signature-image">
                    <img src="{{ $contract->signature_data }}" style="max-height: 90px;">
                </div>
            @endif
        </div>
    </div>

    <!-- FOOTER -->
    <div class="footer">
        This document was generated by Smart Rental System.<br>
        Digital contract record for tenancy verification and reference.
    </div>

</body>
</html>