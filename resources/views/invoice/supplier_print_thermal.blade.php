<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>طباعة حراري - {{ $supplier->name }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            width: 80mm;
            margin: 0 auto;
            padding: 10px;
            color: #000;
            font-size: 14px;
            line-height: 1.4;
        }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .mb-2 { margin-bottom: 10px; }
        .divider { border-top: 1px dashed #000; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; }
        td, th { text-align: right; vertical-align: top; }
        .btn-print { margin-bottom: 20px; padding: 10px; width: 100%; background: #3498db; color: white; border: none; cursor: pointer; }
        @media print { .btn-print { display: none; } body { width: 100%; padding: 0; margin: 0; } }
    </style>
</head>
<body>
    <button class="btn-print" onclick="window.print()">🖨️ طباعة</button>

    <div class="text-center mb-2">
        <h2 style="margin: 0;">{{ $loungeName }}</h2>
        <div style="font-size: 12px;">كشف حساب مورد</div>
    </div>

    <div class="divider"></div>

    <table>
        <tr>
            <td style="width: 40%;" class="font-bold">المورد:</td>
            <td>{{ $supplier->name }}</td>
        </tr>
        <tr>
            <td class="font-bold">الاسم التجاري:</td>
            <td>{{ $supplier->trade_name ?: ($supplier->company ?? '-') }}</td>
        </tr>
        <tr>
            <td class="font-bold">المسؤول:</td>
            <td>{{ $supplier->contact_person ?? '-' }}</td>
        </tr>
        <tr>
            <td class="font-bold">الهاتف:</td>
            <td dir="ltr">{{ $supplier->phone ?? '-' }}</td>
        </tr>
        @if($supplier->tax_number)
        <tr>
            <td class="font-bold">رقم ضريبي:</td>
            <td dir="ltr">{{ $supplier->tax_number }}</td>
        </tr>
        @endif
    </table>

    <div class="divider"></div>

    <div class="text-center" style="font-size: 1.2em; font-weight: bold;">
        الرصيد: {{ number_format($supplier->balance, 2) }} ريال
    </div>

    <div class="divider"></div>
    <div class="text-center" style="font-size: 11px;">
        تاريخ الطباعة: <br> {{ $printDate }}
    </div>
    
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
