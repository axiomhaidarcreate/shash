<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>فاتورة مشتريات - {{ $invoice->invoice_number ?? $invoice->id }}</title>
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
    <button class="btn-print" onclick="window.print()">🖨️ طباعة الفاتورة</button>

    <div class="header">
        <h1>{{ $loungeName }}</h1>
        <p>فاتورة مشتريات</p>
    </div>

    <div class="info-box">
        <div class="info-row">
            <span><strong>المورد:</strong> {{ $invoice->supplier->name }}</span>
            <span><strong>رقم الفاتورة:</strong> {{ $invoice->invoice_number ?? '-' }}</span>
        </div>
        <div class="info-row">
            <span><strong>تاريخ الفاتورة:</strong> {{ $invoice->created_at->format('Y-m-d h:i A') }}</span>
            <span><strong>طريقة الدفع:</strong> {{ $invoice->payment_type === 'cash' ? 'نقداً' : 'تقسيط' }}</span>
        </div>
        <div class="info-row" style="margin-top: 15px; border-top: 1px dashed #ccc; padding-top: 15px;">
            <span style="font-size: 1.2em;"><strong>المبلغ الإجمالي:</strong> <span style="color: #2c3e50;">{{ number_format($invoice->total_amount, 2) }} ريال</span></span>
        </div>
        @if($invoice->payment_type === 'installment')
        <div class="info-row" style="margin-top: 10px;">
            <span><strong>المدفوع مقدماً:</strong> <span style="color: #27ae60;">{{ number_format($invoice->paid_amount, 2) }} ريال</span></span>
            <span><strong>المتبقي (أقساط):</strong> <span style="color: #e74c3c;">{{ number_format($invoice->remaining_amount, 2) }} ريال</span></span>
        </div>
        @endif
        @if($invoice->notes)
        <div class="info-row" style="margin-top: 15px; border-top: 1px dashed #ccc; padding-top: 15px;">
            <span><strong>الملاحظات:</strong> {{ $invoice->notes }}</span>
        </div>
        @endif
    </div>

    @if($invoice->payment_type === 'installment' && $invoice->installments->count() > 0)
    <h3 style="color: #2c3e50;">جدول الأقساط</h3>
    <table>
        <thead>
            <tr>
                <th>رقم القسط</th>
                <th>المبلغ</th>
                <th>تاريخ الاستحقاق</th>
                <th>الحالة</th>
                <th>تاريخ الدفع</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->installments as $index => $inst)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td style="font-weight: bold;">{{ number_format($inst->amount, 2) }} ريال</td>
                <td>{{ $inst->due_date }}</td>
                <td>{{ $inst->status === 'paid' ? 'مدفوع' : 'قيد الانتظار' }}</td>
                <td>{{ $inst->paid_at ? \Carbon\Carbon::parse($inst->paid_at)->format('Y-m-d') : '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <div class="footer">
        <p>تاريخ الطباعة: {{ $printDate }}</p>
    </div>
    
    <script>
        window.onload = function() {
            // window.print();
        };
    </script>
</body>
</html>
