<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>كشف حساب المورد - {{ $supplier->name }}</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; padding: 40px; color: #333; line-height: 1.6; }
        .header { text-align: center; margin-bottom: 40px; border-bottom: 2px solid #ddd; padding-bottom: 20px; }
        .header h1 { margin: 0; color: #2c3e50; font-size: 24px; }
        .header p { margin: 5px 0; color: #7f8c8d; }
        .info-box { border: 1px solid #ddd; padding: 20px; border-radius: 8px; margin-bottom: 30px; background: #f9f9f9; }
        .info-row { display: flex; justify-content: space-between; margin-bottom: 10px; }
        .info-row strong { color: #2c3e50; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: right; }
        th { background-color: #f4f6f7; color: #2c3e50; }
        .footer { margin-top: 50px; text-align: center; color: #7f8c8d; font-size: 12px; }
        .btn-print { margin-bottom: 20px; padding: 10px 20px; background: #3498db; color: white; border: none; border-radius: 5px; cursor: pointer; }
        @media print { .btn-print { display: none; } body { padding: 0; } }
    </style>
</head>
<body>
    <button class="btn-print" onclick="window.print()">🖨️ طباعة التقرير</button>

    <div class="header">
        <h1>{{ $loungeName }}</h1>
        <p>كشف حساب المورد (A4)</p>
    </div>

    <div class="info-box">
        <div class="info-row">
            <span><strong>اسم المورد:</strong> {{ $supplier->name }}</span>
            <span><strong>الشركة / الاسم التجاري:</strong> {{ $supplier->trade_name ?: ($supplier->company ?? '-') }}</span>
        </div>
        <div class="info-row">
            <span><strong>رقم الهاتف:</strong> <span dir="ltr">{{ $supplier->phone ?? '-' }}</span></span>
            <span><strong>اسم المسؤول:</strong> {{ $supplier->contact_person ?? '-' }}</span>
        </div>
        <div class="info-row">
            <span><strong>الرقم الضريبي:</strong> {{ $supplier->tax_number ?? '-' }}</span>
            <span><strong>الرقم الوطني:</strong> {{ $supplier->national_id ?? '-' }}</span>
        </div>
        <div class="info-row">
            <span><strong>المدينة:</strong> {{ $supplier->city ?? '-' }}</span>
            <span><strong>العنوان:</strong> {{ $supplier->address ?? '-' }}</span>
        </div>
        @if($supplier->website)
        <div class="info-row">
            <span><strong>الموقع الجغرافي / الإلكتروني:</strong> {{ $supplier->website }}</span>
            <span></span>
        </div>
        @endif
        <div class="info-row" style="margin-top: 15px; border-top: 1px dashed #ccc; padding-top: 15px;">
            <span><strong>تاريخ الإضافة:</strong> {{ $supplier->created_at->format('Y-m-d') }}</span>
            <span style="font-size: 1.2em;"><strong>الرصيد الحالي:</strong> <span style="color: {{ $supplier->balance < 0 ? '#e74c3c' : '#27ae60' }};">{{ number_format($supplier->balance, 2) }} ريال</span></span>
        </div>
    </div>

    <div class="footer">
        <p>تاريخ الطباعة: {{ $printDate }}</p>
    </div>
    
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
