<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تذكرة دخول #{{ $ticket->barcode }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-weight: bold !important; }

        body {
            font-family: 'Arial', sans-serif;
            background: #f0f0f0;
            direction: rtl;
            text-align: center;
            padding: 20px 0;
        }

        .ticket-wrapper {
            width: 72mm;
            margin: 0 auto 20px auto;
            padding: 6mm 4mm;
            border: 2px dashed #333;
            background: #fff;
            page-break-after: always;
            break-after: page;
        }
        


        .logo-area {
            margin-bottom: 8px;
            border-bottom: 1px solid #333;
            padding-bottom: 6px;
        }

        .logo-area h1 {
            font-size: 20pt;
            font-weight: 900;
            letter-spacing: 1px;
        }

        .logo-area p {
            font-size: 9pt;
            color: #555;
        }

        .ticket-id {
            font-size: 60pt;
            font-weight: 900;
            margin: 10px 0;
            letter-spacing: 2px;
        }

        .ticket-row {
            font-size: 12pt;
            font-weight: bold;
            margin: 2px 0;
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .ticket-row .label {
            color: #333;
        }

        .ticket-row .value {
            color: #000;
        }

        .barcode-section {
            margin: 10px 0;
            padding: 8px 0;
            border-top: 1px dashed #333;
            border-bottom: 1px dashed #333;
        }

        .barcode-text {
            font-family: 'Courier New', monospace;
            font-size: 11pt;
            font-weight: bold;
            letter-spacing: 3px;
            background: #f0f0f0;
            padding: 4px 10px;
            border-radius: 4px;
            display: inline-block;
            margin: 4px 0;
        }

        .products-section {
            margin: 8px 0;
            padding: 6px 0;
            border-top: 1px dashed #ccc;
        }

        .products-section h3 {
            font-size: 11pt;
            margin-bottom: 4px;
            color: #333;
            font-weight: bold;
        }

        .product-item {
            font-size: 18pt;
            font-weight: 900;
            padding: 8px 0;
            color: #000;
            border: 2px solid #000;
            border-radius: 4px;
            margin-top: 4px;
            background: #f9f9f9;
        }

        .footer-note {
            font-size: 8pt;
            color: #777;
            margin-top: 6px;
        }

        .no-print {
            text-align: center;
            margin: 20px 0;
        }

        @media print {
            @page { margin: 0; size: 80mm auto; }
            body { padding: 0 4mm; background: #fff; }
            .ticket-wrapper { margin: 0 auto; border: none; width: 100%; box-sizing: border-box; padding: 0 5mm; }
            .no-print { display: none !important; }
        }
    </style>
    @if(request('auto'))
    <script>
        window.onload = function() { window.print(); }
    </script>
    @endif
</head>
<body>
    @php
        $products = ($ticket->selected_products && is_array($ticket->selected_products) && count($ticket->selected_products) > 0) 
            ? $ticket->selected_products 
            : [null];
            
        if (request()->has('product_index')) {
            $idx = request('product_index');
            if (isset($products[$idx])) {
                $products = [$products[$idx]];
            }
        }
    @endphp

    @foreach($products as $index => $product)
    <div class="ticket-wrapper">
        <div class="logo-area">
            <h1>🎫 تذكرة دخول</h1>
            <p>Entrance Ticket</p>
        </div>

        @if($ticket->is_subscription)
            <div style="border: 2px solid #000; padding: 6px; font-size: 12pt; font-weight: 900; margin: 8px 0; border-radius: 6px; text-align: center;">
                ⭐ اشتراك مجاني ⭐
            </div>
        @endif


        <div class="ticket-id">#{{ $ticket->barcode }}</div>

        @if($ticket->ticketTemplate)
        <div class="ticket-row">
            <span class="label">نوع التذكرة:</span>
            <span class="value">{{ $ticket->ticketTemplate->name }}</span>
        </div>
        @endif

        <div class="ticket-row">
            <span class="label">التاريخ:</span>
            <span class="value">{{ $ticket->created_at->format('Y/m/d') }}</span>
        </div>

        <div class="ticket-row">
            <span class="label">الوقت:</span>
            <span class="value">{{ $ticket->created_at->format('h:i A') }}</span>
        </div>

        <div class="ticket-row">
            <span class="label">الصلاحية:</span>
            <span class="value">12 ساعة</span>
        </div>
        <div class="ticket-row" style="font-size: 10pt; color: #555;">
            <span class="label">ينتهي في:</span>
            <span class="value">{{ $ticket->created_at->addHours(12)->format('Y/m/d h:i A') }}</span>
        </div>

        {{-- Selected Product --}}
        @if($product)
        <div class="products-section">
            <h3>الطلب:</h3>
            <div class="product-item">{{ $product }}</div>
        </div>
        @endif

        <div class="barcode-section">
            <div style="font-size: 8pt; color: #555; margin-bottom: 4px;">الباركود</div>
            {{-- Real SVG Barcode --}}
            <svg id="barcode-svg-{{ $index }}" style="max-width: 100%; height: auto;"></svg>
            <div class="barcode-text" style="display: none;">{{ $ticket->barcode }}</div>
        </div>

        <div class="footer-note">
            يرجى الاحتفاظ بهذه التذكرة<br>
            للمساعدة يرجى مراجعة موظف الاستقبال
        </div>
    </div>
    @endforeach

    <div class="no-print">
        <button onclick="window.print()" style="background: #333; color: #fff; border: none; padding: 10px 24px; border-radius: 6px; cursor: pointer; font-size: 14px; font-weight: bold;">
            طباعة التذاكر
        </button>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <script>
        function renderBarcodes() {
            @foreach($products as $index => $product)
            try {
                JsBarcode("#barcode-svg-{{ $index }}", "{{ $ticket->barcode }}", {
                    format: "CODE128",
                    lineColor: "#000",
                    width: 3,
                    height: 70,
                    displayValue: true,
                    fontSize: 24,
                    fontOptions: "bold",
                    margin: 2
                });
            } catch(e) {
                console.error("Barcode error:", e);
            }
            @endforeach
        }

        if (document.readyState === 'loading') {
            document.addEventListener("DOMContentLoaded", renderBarcodes);
        } else {
            renderBarcodes();
        }
    </script>
</body>
</html>
