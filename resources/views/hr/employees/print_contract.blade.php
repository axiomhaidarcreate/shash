<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>عقد عمل - {{ $employee->full_name_ar ?? $employee->full_name }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap');
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Tajawal', sans-serif; background: #f1f5f9; color: #1e293b; }
        .toolbar {
            background: white; border-bottom: 1px solid #e2e8f0; padding: 1rem 2rem;
            display: flex; align-items: center; justify-content: space-between;
        }
        .toolbar h2 { font-size: 1.1rem; font-weight: 700; color: #1e293b; }
        .toolbar p { font-size: 0.8rem; color: #64748b; margin-top: 2px; }
        .toolbar .actions { display: flex; gap: 0.5rem; }
        .btn {
            padding: 0.5rem 1.25rem; border-radius: 0.75rem; font-size: 0.85rem;
            font-weight: 700; cursor: pointer; text-decoration: none; display: inline-flex;
            align-items: center; gap: 0.4rem; border: none; font-family: inherit;
        }
        .btn-primary { background: #115e59; color: white; }
        .btn-secondary { background: white; color: #475569; border: 1px solid #e2e8f0; }
        .contract-page {
            max-width: 800px; margin: 2rem auto; background: white; border-radius: 1rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e2e8f0;
            padding: 3rem 3.5rem;
        }
        .contract-title {
            font-size: 1.5rem; font-weight: 800; text-align: center;
            margin-bottom: 2rem; padding-bottom: 1rem; border-bottom: 2px solid #1e293b;
        }
        .contract-body { white-space: pre-wrap; font-size: 1.05rem; line-height: 2.2; text-align: justify; }
        .signatures {
            margin-top: 5rem; display: flex; justify-content: space-between; padding: 0 2rem;
        }
        .sig-block { text-align: center; font-weight: 700; font-size: 1rem; }
        .sig-line { border-bottom: 2px solid #1e293b; width: 12rem; margin: 4rem auto 0.5rem; }
        .sig-label { font-size: 0.8rem; color: #64748b; }

        @media print {
            body { background: white; }
            .toolbar { display: none !important; }
            .contract-page { box-shadow: none; border: none; margin: 0; padding: 2rem; border-radius: 0; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <div>
            <h2>طباعة العقد: {{ $employee->full_name_ar ?? $employee->full_name }}</h2>
            <p>نموذج العقد المربوط: {{ $template->title }}</p>
        </div>
        <div class="actions">
            <button onclick="window.print()" class="btn btn-primary">
                🖨️ طباعة العقد
            </button>
            <a href="{{ route('hr.employees.index') }}" class="btn btn-secondary">رجوع للموظفين</a>
        </div>
    </div>

    <div class="contract-page">
        <h1 class="contract-title">{{ $template->title }}</h1>
        <div class="contract-body">{!! nl2br(e($content)) !!}</div>

        <div class="signatures">
            <div class="sig-block">
                <p>الطرف الأول (الشركة)</p>
                <div class="sig-line"></div>
                <p class="sig-label">التوقيع والختم</p>
            </div>
            <div class="sig-block">
                <p>الطرف الثاني (الموظف)</p>
                <div class="sig-line"></div>
                <p class="sig-label">توقيع المستلم</p>
            </div>
        </div>
    </div>
</body>
</html>


