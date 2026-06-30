<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Summary</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #222;
            line-height: 1.5;
        }
        .header {
            border-bottom: 2px solid #B08401;
            padding-bottom: 10px;
            margin-bottom: 18px;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-table td {
            border: none;
            padding: 0;
            vertical-align: middle;
        }
        .logo-cell {
            width: 60px;
        }
        .logo {
            height: 45px;
            width: auto;
        }
        .title {
            font-size: 22px;
            font-weight: bold;
            color: #683B2B;
        }
        .subtitle {
            font-size: 12px;
            color: #666;
            margin-top: 4px;
        }
        .section {
            margin-top: 18px;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #683B2B;
            margin-bottom: 8px;
            border-left: 4px solid #B08401;
            padding-left: 8px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        td {
            padding: 8px 10px;
            border: 1px solid #ddd;
            vertical-align: top;
        }
        .label {
            width: 32%;
            background: #FAF6F2;
            font-weight: bold;
        }
        .footer {
            margin-top: 24px;
            font-size: 11px;
            color: #666;
            text-align: center;
        }
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: bold;
            background: #FFFBEB;
            color: #A16207;
            border: 1px solid #FDE68A;
        }
    </style>
</head>
<body>

    <div class="header">
        <table class="header-table">
            <tr>
                <td class="logo-cell">
                    <img src="{{ public_path('images/logo.png') }}" alt="Smart Rental Logo" class="logo">
                </td>
                <td>
                    <div class="title">Smart Rental — Payment Summary</div>
                    <div class="subtitle">Generated landlord payment summary for record keeping and reference.</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Landlord Information</div>
        <table>
            <tr>
                <td class="label">Landlord Name</td>
                <td>{{ $summary->landlord_name }}</td>
            </tr>
            <tr>
                <td class="label">Company Name</td>
                <td>{{ $summary->company_name }}</td>
            </tr>
            <tr>
                <td class="label">Email</td>
                <td>{{ $summary->landlord_email }}</td>
            </tr>
            <tr>
                <td class="label">Phone</td>
                <td>{{ $summary->landlord_phone }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Student Information</div>
        <table>
            <tr>
                <td class="label">Student Name</td>
                <td>{{ $summary->student_name }}</td>
            </tr>
            <tr>
                <td class="label">Student Email</td>
                <td>{{ $summary->student_email }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Room & Contract Information</div>
        <table>
            <tr>
                <td class="label">Room Title</td>
                <td>{{ $summary->room_title }}</td>
            </tr>
            <tr>
                <td class="label">Room Type</td>
                <td>{{ ucfirst((string) $summary->room_type) }}</td>
            </tr>
            <tr>
                <td class="label">Contract Start</td>
                <td>{{ optional($summary->contract_start)->format('d M Y') ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Contract End</td>
                <td>{{ optional($summary->contract_end)->format('d M Y') ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Payment Information</div>
        <table>
            <tr>
                <td class="label">Payment ID</td>
                <td>#{{ $summary->payment_id }}</td>
            </tr>
            <tr>
                <td class="label">Booking ID</td>
                <td>#{{ $summary->booking_id ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Payment Type</td>
                <td>{{ $summary->type_label }}</td>
            </tr>
            <tr>
                <td class="label">Rent Month</td>
                <td>{{ $summary->rent_month }}</td>
            </tr>
            <tr>
                <td class="label">Method</td>
                <td>{{ strtoupper((string) ($summary->method ?? '-')) }}</td>
            </tr>
            <tr>
                <td class="label">Provider</td>
                <td>{{ $summary->provider ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Reference</td>
                <td>{{ $summary->provider_ref ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Status</td>
                <td><span class="badge">{{ strtoupper((string) ($summary->status ?? '-')) }}</span></td>
            </tr>
            <tr>
                <td class="label">Submitted At</td>
                <td>{{ optional($summary->submitted_at)->format('d M Y, h:i A') ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Approved / Paid At</td>
                <td>{{ optional($summary->paid_at)->format('d M Y, h:i A') ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Amount Details</div>
        <table>
            <tr>
                <td class="label">Deposit Amount</td>
                <td>RM {{ number_format((float) $summary->deposit_amount, 2) }}</td>
            </tr>
            <tr>
                <td class="label">Monthly Rent</td>
                <td>RM {{ number_format((float) $summary->monthly_rent, 2) }}</td>
            </tr>
            <tr>
                <td class="label">Total Amount</td>
                <td><strong>RM {{ number_format((float) $summary->amount, 2) }}</strong></td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Smart Rental • MSU Student Accommodation • Payment Summary PDF
    </div>

</body>
</html>