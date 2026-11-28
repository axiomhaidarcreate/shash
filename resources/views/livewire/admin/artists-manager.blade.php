<div>
    <style>
        @media print {
            body { 
                background: white !important; 
                color: black !important; 
                margin: 0; 
                padding: 0; 
                filter: grayscale(100%) contrast(1.5) !important;
                -webkit-filter: grayscale(100%) contrast(1.5) !important;
            }
            * {
                color: black !important;
                background-color: transparent !important;
                border-color: black !important;
                text-shadow: none !important;
                box-shadow: none !important;
            }
            .sidebar, .navbar, .page-header, .d-print-none, .btn { display: none !important; }
            .main-content, .content-wrapper, main { margin-left: 0 !important; width: 100% !important; padding: 0 !important; }
            .card { border: 1px solid black !important; margin-bottom: 20px !important; break-inside: avoid; }
            .card h5, .card h6 { color: black !important; font-weight: bold !important; }
            .data-table th, .data-table td { color: black !important; border: 1px solid black !important; padding: 8px !important; }
            .data-table th { background-color: transparent !important; font-weight: bold !important; border-bottom: 2px solid black !important; }
            .badge { border: 1px solid black !important; color: black !important; font-weight: bold !important; }
            table { width: 100% !important; border-collapse: collapse !important; }
            div[style*="position:fixed"] { display: none !important; }
        }
        /* Custom styles for the filter inputs to remain dark on focus */
        .form-control:focus {
            background-color: rgba(255,255,255,0.05) !important;
            color: #ffffff !important;
            border-color: #4CAF50 !important;
            box-shadow: 0 0 0 0.25rem rgba(76, 175, 80, 0.25) !important;
        }
        select.form-control option {
            background-color: #1a1a2e !important;
            color: #ffffff !important;
        }
        /* For date picker icons to remain visible */
        input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(0.8);
            cursor: pointer;
        }

        /* Filter Pills Buttons */
        .btn-filter {
            border: 1px solid rgba(255,255,255,0.1);
            background: rgba(255,255,255,0.02);
            color: var(--text-secondary);
            border-radius: 20px;
            padding: 0.4rem 1rem;
            font-size: 0.85rem;
            transition: all 0.3s ease;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            text-decoration: none;
            white-space: nowrap;
        }
        .btn-filter:hover {
            background: rgba(76,175,80,0.1);
            color: #4CAF50;
            border-color: rgba(76,175,80,0.3);
        }
        .btn-filter.active {
            background: linear-gradient(135deg, #4CAF50, #2E7D32);
            color: white;
            border-color: transparent;
            box-shadow: 0 4px 10px rgba(76,175,80,0.3);
        }

        /* ===== Responsive Mobile Styles ===== */
        @media (max-width: 768px) {
            /* الشاشة الرئيسية: المربعات الأربعة → عمودين */
            .artists-main-grid {
                grid-template-columns: repeat(2, 1fr) !important;
                gap: 0.6rem !important;
            }
            .artists-main-grid .card {
                padding: 1rem 0.6rem !important;
            }
            .artists-main-grid .card i {
                font-size: 1.8rem !important;
            }
            .artists-main-grid .card h6 {
                font-size: 0.8rem !important;
            }
            .artists-main-grid .card p {
                font-size: 0.65rem !important;
            }

            /* عنوان الصفحة */
            .page-header h1 {
                font-size: 1.2rem !important;
            }
            .page-header p {
                font-size: 0.8rem !important;
            }

            /* فلاتر الحجوزات: 5 أعمدة → عمود واحد */
            .booking-filters-grid {
                grid-template-columns: 1fr !important;
                gap: 10px !important;
            }

            /* أزرار الفلاتر السريعة → التفاف */
            .quick-filters {
                flex-wrap: wrap !important;
                gap: 6px !important;
            }
            .btn-filter {
                font-size: 0.75rem !important;
                padding: 0.3rem 0.7rem !important;
            }

            /* عنوان الفلاتر → عمود بدل صف */
            .filter-header-flex {
                flex-direction: column !important;
                align-items: flex-start !important;
                gap: 10px !important;
            }

            /* ملخص الحسابات: 4 أعمدة → عمودين */
            .accounts-summary-grid {
                grid-template-columns: repeat(2, 1fr) !important;
                gap: 0.6rem !important;
            }
            .accounts-summary-grid .card {
                padding: 0.8rem !important;
            }
            .accounts-summary-grid .card div[style*="font-size: 1.6rem"] {
                font-size: 1.2rem !important;
            }

            /* تفاصيل الحفلة في الحسابات → عمود واحد */
            .accounts-detail-grid {
                grid-template-columns: 1fr !important;
            }
            .accounts-profit-grid {
                grid-template-columns: 1fr !important;
                gap: 0.6rem !important;
            }

            /* المودالات: عرض كامل */
            .modal-content-responsive {
                width: 100% !important;
                max-width: 100% !important;
                max-height: 100vh !important;
                border-radius: 0 !important;
                margin: 0 !important;
            }

            /* طاقم الحجز: 2 أعمدة → عمود واحد */
            .crew-grid {
                grid-template-columns: 1fr !important;
                gap: 10px !important;
            }

            /* جدول الحجوزات: تقليل الحد الأدنى للعرض */
            .bookings-table {
                min-width: 700px !important;
            }

            /* الأزرار العلوية: التفاف */
            .top-actions {
                flex-wrap: wrap !important;
                gap: 8px !important;
            }

            /* بطاقة الفلتر */
            .card {
                padding: 0.8rem !important;
            }

            /* جداول البيانات: خط أصغر */
            .data-table th, .data-table td {
                font-size: 0.75rem !important;
                padding: 6px 4px !important;
            }

            /* أزرار الإجراءات */
            .action-buttons {
                flex-wrap: wrap !important;
                gap: 4px !important;
            }
            .action-buttons .btn-sm {
                width: 28px !important;
                height: 28px !important;
                font-size: 0.75rem !important;
            }
        }

        @media (max-width: 480px) {
            /* شاشات صغيرة جداً */
            .artists-main-grid {
                grid-template-columns: 1fr 1fr !important;
                gap: 0.5rem !important;
            }
            .artists-main-grid .card {
                padding: 0.8rem 0.5rem !important;
            }
            .artists-main-grid .card i {
                font-size: 1.5rem !important;
            }

            .booking-filters-grid {
                grid-template-columns: 1fr !important;
            }

            .accounts-summary-grid {
                grid-template-columns: 1fr 1fr !important;
            }

            .page-header h1 {
                font-size: 1rem !important;
            }
        }
    </style>
    <div class="page-header">
        <h1><i class="fa-solid fa-microphone-lines" style="color: #E91E63;"></i> إدارة الحفلات والحجوزات</h1>
        <p>إضافة وإدارة الحفلات والحجوزات والفنانين والعازفين</p>
    </div>

    {{-- ==========================================
         الشاشة الرئيسية: 4 مربعات
    ========================================== --}}
    @if($currentView === 'main')

    <div class="artists-main-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.8rem; margin-bottom: 2rem;">
        <!-- Card 1: الفنانين -->
        <div class="card" style="cursor:pointer; text-align:center; padding: 1.2rem 0.8rem; background: linear-gradient(145deg, #1a1a2e, #16213e); border: 1px solid rgba(233, 30, 99, 0.2); transition: all 0.3s ease;" 
             wire:click="goToView('artists')"
             onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 20px rgba(233,30,99,0.25)'; this.style.borderColor='rgba(233,30,99,0.5)'"
             onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow=''; this.style.borderColor='rgba(233,30,99,0.2)'">
            <i class="fa-solid fa-microphone-lines" style="font-size:2.2rem; color: #E91E63; margin-bottom: 0.6rem; filter: drop-shadow(0 6px 10px rgba(233,30,99,0.4));"></i>
            <h6 style="font-weight:800; margin-bottom:0.2rem; color: #f0f0f0; font-size:0.9rem;">الفنانين</h6>
            <p style="color: var(--text-secondary); font-size:0.72rem; margin-bottom:0.5rem;">إدارة الفنانين المسجلين</p>
            <span class="badge" style="background: rgba(233,30,99,0.15); color: #E91E63; border: 1px solid rgba(233,30,99,0.3); padding: 0.2rem 0.6rem; font-size:0.72rem;">{{ $artists->count() }} فنان</span>
        </div>

        <!-- Card 2: العازفين -->
        <div class="card" style="cursor:pointer; text-align:center; padding: 1.2rem 0.8rem; background: linear-gradient(145deg, #1a1a2e, #0d2137); border: 1px solid rgba(33, 150, 243, 0.2); transition: all 0.3s ease;" 
             wire:click="goToView('musicians')"
             onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 20px rgba(33,150,243,0.25)'; this.style.borderColor='rgba(33,150,243,0.5)'"
             onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow=''; this.style.borderColor='rgba(33,150,243,0.2)'">
            <i class="fa-solid fa-guitar" style="font-size:2.2rem; color: #2196F3; margin-bottom: 0.6rem; filter: drop-shadow(0 6px 10px rgba(33,150,243,0.4));"></i>
            <h6 style="font-weight:800; margin-bottom:0.2rem; color: #f0f0f0; font-size:0.9rem;">العازفين</h6>
            <p style="color: var(--text-secondary); font-size:0.72rem; margin-bottom:0.5rem;">إدارة العازفين المسجلين</p>
            <span class="badge" style="background: rgba(33,150,243,0.15); color: #2196F3; border: 1px solid rgba(33,150,243,0.3); padding: 0.2rem 0.6rem; font-size:0.72rem;">{{ $musicians->count() }} عازف</span>
        </div>

        <!-- Card 3: حجز فنان وعازف -->
        <div class="card" style="cursor:pointer; text-align:center; padding: 1.2rem 0.8rem; background: linear-gradient(145deg, #1a1a2e, #1a2e1a); border: 1px solid rgba(76, 175, 80, 0.2); transition: all 0.3s ease;" 
             wire:click="goToView('bookings')"
             onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 20px rgba(76,175,80,0.25)'; this.style.borderColor='rgba(76,175,80,0.5)'"
             onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow=''; this.style.borderColor='rgba(76,175,80,0.2)'">
            <i class="fa-solid fa-calendar-check" style="font-size:2.2rem; color: #4CAF50; margin-bottom: 0.6rem; filter: drop-shadow(0 6px 10px rgba(76,175,80,0.4));"></i>
            <h6 style="font-weight:800; margin-bottom:0.2rem; color: #f0f0f0; font-size:0.9rem;">حجز فنان وعازف</h6>
            <p style="color: var(--text-secondary); font-size:0.72rem; margin-bottom:0.5rem;">حجز فنان وعازف لحفلة</p>
            <span class="badge" style="background: rgba(76,175,80,0.15); color: #4CAF50; border: 1px solid rgba(76,175,80,0.3); padding: 0.2rem 0.6rem; font-size:0.72rem;">{{ $bookings->count() }} حجز</span>
        </div>

        <!-- Card 4: الحسابات -->
        <div class="card" style="cursor:pointer; text-align:center; padding: 1.2rem 0.8rem; background: linear-gradient(145deg, #1a1a2e, #1a2e2e); border: 1px solid rgba(0, 188, 212, 0.2); transition: all 0.3s ease;" 
             wire:click="goToView('accounts')"
             onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 20px rgba(0,188,212,0.25)'; this.style.borderColor='rgba(0,188,212,0.5)'"
             onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow=''; this.style.borderColor='rgba(0,188,212,0.2)'">
            <i class="fa-solid fa-chart-pie" style="font-size:2.2rem; color: #00BCD4; margin-bottom: 0.6rem; filter: drop-shadow(0 6px 10px rgba(0,188,212,0.4));"></i>
            <h6 style="font-weight:800; margin-bottom:0.2rem; color: #f0f0f0; font-size:0.9rem;">الحسابات</h6>
            <p style="color: var(--text-secondary); font-size:0.72rem; margin-bottom:0.5rem;">تقارير وتحليل الحفلات</p>
            <span class="badge" style="background: rgba(0,188,212,0.15); color: #00BCD4; border: 1px solid rgba(0,188,212,0.3); padding: 0.2rem 0.6rem; font-size:0.72rem;"><i class="fa-solid fa-chart-line me-1"></i> تقارير</span>
        </div>
    </div>



    @endif

    {{-- ==========================================
         شاشة إدارة الفنانين
    ========================================== --}}
    @if($currentView === 'artists')

    <div style="margin-bottom: 1.5rem; display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
        <button wire:click="goToView('main')" class="btn btn-outline" style="border-color: rgba(233,30,99,0.4); color: #E91E63;">
            <i class="fa-solid fa-arrow-right me-1"></i> رجوع
        </button>
        <button wire:click="openArtistModal" class="btn" style="background: linear-gradient(135deg, #E91E63, #C2185B); color:white;">
            <i class="fa-solid fa-plus me-1"></i> إضافة فنان جديد
        </button>
    </div>

    <div class="card">
        <h5 style="font-weight:800; margin-bottom:1rem; color: #E91E63;"><i class="fa-solid fa-users me-2"></i> قائمة الفنانين ({{ $filteredArtists->count() }})</h5>
        
        {{-- فلتر الجنسية --}}
        <div style="margin-bottom: 1rem; display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
            <div style="display:flex; align-items:center; gap:8px;">
                <i class="fa-solid fa-globe" style="color: #E91E63;"></i>
                <label style="font-weight:700; font-size:0.85rem; white-space:nowrap;">بحث بالجنسية:</label>
            </div>
            <select wire:model.live="filter_artist_nationality" class="form-control" style="max-width: 220px; background: rgba(255,255,255,0.02); border-color: rgba(233,30,99,0.3); color: var(--text-primary); font-size:0.85rem;">
                <option value="">جميع الجنسيات</option>
                @foreach($artistNationalities as $nat)
                    <option value="{{ $nat }}">{{ $nat }}</option>
                @endforeach
            </select>
            @if($filter_artist_nationality)
                <button wire:click="$set('filter_artist_nationality', '')" class="btn btn-sm" style="background: rgba(233,30,99,0.12); border: 1px solid rgba(233,30,99,0.3); color: #E91E63; font-size:0.8rem; border-radius:20px; padding: 0.25rem 0.8rem;">
                    <i class="fa-solid fa-times me-1"></i> إلغاء فلتر الجنسية
                </button>
            @endif
            <div style="display:flex; align-items:center; gap:8px; margin-right: 15px;">
                <i class="fa-solid fa-venus-mars" style="color: #E91E63;"></i>
                <label style="font-weight:700; font-size:0.85rem; white-space:nowrap;">الجنس:</label>
            </div>
            <select wire:model.live="filter_artist_gender" class="form-control" style="max-width: 150px; background: rgba(255,255,255,0.02); border-color: rgba(233,30,99,0.3); color: var(--text-primary); font-size:0.85rem;">
                <option value="">الكل</option>
                <option value="male">ذكر</option>
                <option value="female">أنثى</option>
            </select>
            @if($filter_artist_gender)
                <button wire:click="$set('filter_artist_gender', '')" class="btn btn-sm" style="background: rgba(233,30,99,0.12); border: 1px solid rgba(233,30,99,0.3); color: #E91E63; font-size:0.8rem; border-radius:20px; padding: 0.25rem 0.8rem;">
                    <i class="fa-solid fa-times me-1"></i> إلغاء فلتر الجنس
                </button>
            @endif
        </div>

        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الصورة</th>
                        <th>الاسم الحقيقي</th>
                        <th>الاسم الفني</th>
                        <th>الجنسية</th>
                        <th>الرقم التعريفي</th>
                        <th>الجوال</th>
                        <th class="text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($filteredArtists as $a)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                @if($a->image)
                                    <img src="{{ asset('storage/' . $a->image) }}" style="width:40px; height:40px; border-radius:50%; object-fit:cover; border:2px solid var(--border-color);">
                                @else
                                    <div style="width:40px; height:40px; border-radius:50%; background: linear-gradient(135deg, #E91E63, #C2185B); display:flex; align-items:center; justify-content:center; color:white; font-weight:800; font-size:0.85rem;">{{ mb_substr($a->real_name, 0, 1) }}</div>
                                @endif
                            </td>
                            <td style="font-weight:700;">{{ $a->real_name }}</td>
                            <td>{{ $a->stage_name ?? '-' }}</td>
                            <td>{{ $a->nationality ?? '-' }}</td>
                            <td><span dir="ltr">{{ $a->license_number ?? '-' }}</span></td>
                            <td><span dir="ltr">{{ $a->phone ?? '-' }}</span></td>
                            <td class="text-center">
                                <div style="display:flex; gap:6px; justify-content:center;">
                                    <button wire:click="editArtist({{ $a->id }})" class="btn btn-warning btn-sm" title="تعديل">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                    <button wire:click="deleteArtist({{ $a->id }})" wire:confirm="هل أنت متأكد من حذف هذا الفنان؟" class="btn btn-danger btn-sm" title="حذف">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center py-3" style="color: var(--text-secondary);">{{ $filter_artist_nationality ? 'لا يوجد فنانين بهذه الجنسية.' : 'لا يوجد فنانين مسجلين بعد. اضغط "إضافة فنان جديد" لإضافة أول فنان.' }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @endif

    {{-- ==========================================
         شاشة إدارة العازفين
    ========================================== --}}
    @if($currentView === 'musicians')

    <div style="margin-bottom: 1.5rem; display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
        <button wire:click="goToView('main')" class="btn btn-outline" style="border-color: rgba(33,150,243,0.4); color: #2196F3;">
            <i class="fa-solid fa-arrow-right me-1"></i> رجوع
        </button>
        <button wire:click="openMusicianModal" class="btn" style="background: linear-gradient(135deg, #2196F3, #1565C0); color:white;">
            <i class="fa-solid fa-plus me-1"></i> إضافة عازف جديد
        </button>
    </div>

    <div class="card">
        <h5 style="font-weight:800; margin-bottom:1rem; color: #2196F3;"><i class="fa-solid fa-guitar me-2"></i> قائمة العازفين ({{ $musicians->count() }})</h5>
        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الصورة</th>
                        <th>الاسم الحقيقي</th>
                        <th>الاسم الفني</th>
                        <th>نوع العزف</th>
                        <th>الرقم التعريفي</th>
                        <th>الجوال</th>
                        <th class="text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($musicians as $m)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                @if($m->image)
                                    <img src="{{ asset('storage/' . $m->image) }}" style="width:40px; height:40px; border-radius:50%; object-fit:cover; border:2px solid var(--border-color);">
                                @else
                                    <div style="width:40px; height:40px; border-radius:50%; background: linear-gradient(135deg, #2196F3, #1565C0); display:flex; align-items:center; justify-content:center; color:white; font-weight:800; font-size:0.85rem;">{{ mb_substr($m->real_name, 0, 1) }}</div>
                                @endif
                            </td>
                            <td style="font-weight:700;">{{ $m->real_name }}</td>
                            <td>{{ $m->stage_name ?? '-' }}</td>
                            <td>
                                @if($m->instrument)
                                    <span class="badge" style="background: rgba(33,150,243,0.15); color: #2196F3; border: 1px solid rgba(33,150,243,0.3);">{{ $m->instrument }}</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td><span dir="ltr">{{ $m->license_number ?? '-' }}</span></td>
                            <td><span dir="ltr">{{ $m->phone ?? '-' }}</span></td>
                            <td class="text-center">
                                <div style="display:flex; gap:6px; justify-content:center;">
                                    <button wire:click="editMusician({{ $m->id }})" class="btn btn-warning btn-sm" title="تعديل">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                    <button wire:click="deleteMusician({{ $m->id }})" wire:confirm="هل أنت متأكد من حذف هذا العازف؟" class="btn btn-danger btn-sm" title="حذف">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center py-3" style="color: var(--text-secondary);">لا يوجد عازفين مسجلين بعد. اضغط "إضافة عازف جديد" لإضافة أول عازف.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @endif




    {{-- ==========================================
         شاشة الحسابات
    ========================================== --}}
    @if($currentView === 'accounts')

    <div style="margin-bottom: 1.5rem; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap;" class="d-print-none">
        <div style="display:flex; align-items:center; gap:12px;">
            <button wire:click="goToView('main')" class="btn btn-outline" style="border-color: rgba(0, 188, 212, 0.4); color: #00BCD4;">
                <i class="fa-solid fa-arrow-right me-1"></i> رجوع
            </button>
            <h5 class="m-0 fw-bold" style="color: #00BCD4;"><i class="fa-solid fa-chart-pie me-2"></i> الحسابات والتقارير</h5>
        </div>
        <button onclick="window.print()" class="btn" style="background: linear-gradient(135deg, #00BCD4, #00838F); color:white; border:none;">
            <i class="fa-solid fa-print me-1"></i> طباعة A4
        </button>
    </div>

    <!-- فلاتر -->
    <div class="card mb-4 d-print-none" style="border: 1px solid rgba(0, 188, 212, 0.2);">
        <div class="d-flex justify-content-between align-items-center mb-3 filter-header-flex" style="flex-wrap: wrap; gap: 10px;">
            <h6 class="fw-bold m-0" style="color: #00BCD4;"><i class="fa-solid fa-filter me-2"></i> فلاتر البحث</h6>
            <div class="d-flex gap-2 quick-filters" style="overflow-x: auto; max-width: 100%; padding-bottom: 4px;">
                <button wire:click="resetAccountsFilters" class="btn-filter {{ $accounts_filter_status === '' && $accounts_date_from === '' && $accounts_date_to === '' && $accounts_selected_artist_id === '' && $accounts_selected_event_id === '' ? 'active' : '' }}">
                    <i class="fa-solid fa-border-all me-1"></i> الكل
                </button>
                <button wire:click="accountsFilterToday" class="btn-filter {{ $accounts_date_from === now()->toDateString() && $accounts_date_to === now()->toDateString() && $accounts_filter_status === '' ? 'active' : '' }}">
                    <i class="fa-solid fa-calendar-day me-1"></i> اليوم
                </button>
                <button wire:click="accountsFilterThisMonth" class="btn-filter {{ $accounts_date_from === now()->startOfMonth()->toDateString() && $accounts_date_to === now()->endOfMonth()->toDateString() && $accounts_filter_status === '' ? 'active' : '' }}">
                    <i class="fa-solid fa-calendar-week me-1"></i> حفلات الشهر
                </button>
                <button wire:click="accountsFilterNextMonth" class="btn-filter {{ $accounts_date_from === now()->addMonth()->startOfMonth()->toDateString() && $accounts_date_to === now()->addMonth()->endOfMonth()->toDateString() && $accounts_filter_status === '' ? 'active' : '' }}">
                    <i class="fa-solid fa-calendar-plus me-1"></i> حفلات الشهر القادم
                </button>
                <button wire:click="setAccountsStatus('approved')" class="btn-filter {{ $accounts_filter_status === 'approved' ? 'active' : '' }}">
                    <i class="fa-solid fa-check-circle me-1"></i> المعتمد
                </button>
                <button wire:click="setAccountsStatus('completed')" class="btn-filter {{ $accounts_filter_status === 'completed' ? 'active' : '' }}">
                    <i class="fa-solid fa-flag-checkered me-1"></i> المكتمل
                </button>
            </div>
        </div>
        <div class="row align-items-end" style="gap: 0;">
            <div class="col-md-3 mb-3">
                <label class="form-label fw-bold" style="font-size:0.8rem;">من تاريخ</label>
                <input type="date" wire:model.live="accounts_date_from" class="form-control">
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label fw-bold" style="font-size:0.8rem;">إلى تاريخ</label>
                <input type="date" wire:model.live="accounts_date_to" class="form-control">
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label fw-bold" style="font-size:0.8rem;">الفنان</label>
                <select wire:model.live="accounts_selected_artist_id" class="form-control">
                    <option value="">كل الفنانين</option>
                    @foreach($artists as $a)
                        <option value="{{ $a->id }}">{{ $a->real_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label fw-bold" style="font-size:0.8rem;">الحفلة</label>
                <select wire:model.live="accounts_selected_event_id" class="form-control">
                    <option value="">كل الحفلات</option>
                    @foreach($events as $e)
                        <option value="{{ $e->id }}">{{ $e->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- ملخص إجمالي -->
    @if(isset($totalExpense))
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.5rem;">
        <div class="card text-center" style="border: 1px solid rgba(0, 188, 212, 0.2); padding: 1rem;">
            <i class="fa-solid fa-calendar-check" style="font-size:1.5rem; color: #00BCD4; margin-bottom: 0.4rem;"></i>
            <div style="font-size: 1.6rem; font-weight: 900; color: #00BCD4;">{{ count($accountEventsData) }}</div>
            <div style="font-size: 0.8rem; color: var(--text-secondary);">عدد الحفلات</div>
        </div>
        <div class="card text-center" style="border: 1px solid rgba(76,175,80,0.2); padding: 1rem;">
            <i class="fa-solid fa-sack-dollar" style="font-size:1.5rem; color: #4CAF50; margin-bottom: 0.4rem;"></i>
            <div style="font-size: 1.6rem; font-weight: 900; color: #4CAF50;">{{ number_format($totalIncome, 0) }}</div>
            <div style="font-size: 0.8rem; color: var(--text-secondary);">إجمالي الدخل (ر.ي)</div>
        </div>
        <div class="card text-center" style="border: 1px solid rgba(233,30,99,0.2); padding: 1rem;">
            <i class="fa-solid fa-money-bill-trend-up" style="font-size:1.5rem; color: #E91E63; margin-bottom: 0.4rem;"></i>
            <div style="font-size: 1.6rem; font-weight: 900; color: #E91E63;">{{ number_format($totalExpense, 0) }}</div>
            <div style="font-size: 0.8rem; color: var(--text-secondary);">إجمالي الصرف / التكلفة (ر.ي)</div>
        </div>
        <div class="card text-center" style="border: 1px solid rgba(212, 175, 55, 0.2); padding: 1rem; {{ $totalProfit > 0 ? 'background: rgba(76, 175, 80, 0.05);' : ($totalProfit < 0 ? 'background: rgba(233, 30, 99, 0.05);' : '') }}">
            <i class="fa-solid fa-vault" style="font-size:1.5rem; color: #d4af37; margin-bottom: 0.4rem;"></i>
            <div style="font-size: 1.6rem; font-weight: 900; color: {{ $totalProfit >= 0 ? '#4CAF50' : '#E91E63' }};" dir="ltr">{{ number_format($totalProfit, 0) }}</div>
            <div style="font-size: 0.8rem; color: var(--text-secondary);">صافي الأرباح (ر.ي)</div>
        </div>
    </div>
    @endif

    <!-- جدول الحفلات -->
    @if(isset($accountEventsData))
    <div class="card mb-4" style="border: 1px solid rgba(0, 188, 212, 0.15);">
        <h5 style="font-weight:800; margin-bottom:1rem; color: #00BCD4;"><i class="fa-solid fa-list-check me-2"></i> تفاصيل الحفلات</h5>
        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الحفلة</th>
                        <th>التاريخ</th>
                        <th>الفنان / السعر</th>
                        <th>العازف / السعر</th>
                        <th>الدخل</th>
                        <th>الصرف</th>
                        <th>الربح</th>
                        <th class="text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($accountEventsData as $idx => $item)
                        <tr style="{{ $accounts_selected_event_id == $item['event']->id ? 'background: rgba(0,188,212,0.08); border-right: 3px solid #00BCD4;' : '' }}">
                            <td>{{ $idx + 1 }}</td>
                            <td style="font-weight:700;">{{ $item['event']->title }}</td>
                            <td>{{ $item['event']->event_date ? \Carbon\Carbon::parse($item['event']->event_date)->format('Y-m-d') : '-' }}</td>
                            <td>
                                @foreach($item['artist_bookings'] as $ab)
                                    @if($ab->artist)
                                        @php
                                            $isFemaleArtist = ($ab->artist->gender ?? 'male') === 'female';
                                            $artBg = $isFemaleArtist ? 'rgba(244,67,54,0.1)' : 'rgba(33,150,243,0.1)';
                                            $artColor = $isFemaleArtist ? '#F44336' : '#2196F3';
                                            $artBorder = $isFemaleArtist ? 'rgba(244,67,54,0.2)' : 'rgba(33,150,243,0.2)';
                                        @endphp
                                        <div style="margin-bottom: 2px;">
                                            <span class="badge" style="background: {{ $artBg }}; color: {{ $artColor }}; border:1px solid {{ $artBorder }}; font-size:0.72rem;">{{ $ab->artist->stage_name ?: $ab->artist->real_name }}</span>
                                            @if($item['event']->is_ended)
                                                <span style="font-size: 0.75rem; font-weight: bold; color: {{ $artColor }}; display:block; margin-top:2px;">{{ number_format($ab->price, 0) }} ر.ي</span>
                                            @endif
                                        </div>
                                    @endif
                                @endforeach
                                @if($item['artist_bookings']->whereNotNull('artist_id')->isEmpty()) - @endif
                            </td>
                            <td>
                                @foreach($item['artist_bookings'] as $ab)
                                    @if($ab->musician)
                                        @php
                                            $isFemaleMusician = ($ab->musician->gender ?? 'male') === 'female';
                                            $musBg = $isFemaleMusician ? 'rgba(244,67,54,0.1)' : 'rgba(33,150,243,0.1)';
                                            $musColor = $isFemaleMusician ? '#F44336' : '#2196F3';
                                            $musBorder = $isFemaleMusician ? 'rgba(244,67,54,0.2)' : 'rgba(33,150,243,0.2)';
                                        @endphp
                                        <div style="margin-bottom: 2px;">
                                            <span class="badge" style="background: {{ $musBg }}; color: {{ $musColor }}; border:1px solid {{ $musBorder }}; font-size:0.72rem;">{{ $ab->musician->stage_name ?: $ab->musician->real_name }}</span>
                                            @if($item['event']->is_ended)
                                                <span style="font-size: 0.75rem; font-weight: bold; color: {{ $musColor }}; display:block; margin-top:2px;">{{ number_format($ab->musician_price, 0) }} ر.ي</span>
                                            @endif
                                        </div>
                                    @endif
                                @endforeach
                                @if($item['artist_bookings']->whereNotNull('musician_id')->isEmpty()) - @endif
                            </td>
                            <td>
                                @if($item['income'] > 0)
                                    <span style="font-weight:700; color: #4CAF50;">{{ number_format($item['income'], 0) }}</span>
                                @else
                                    <span style="color: var(--text-secondary);">-</span>
                                @endif
                            </td>
                            <td><span style="font-weight:700; color: #E91E63;">{{ number_format($item['expense'], 0) }}</span></td>
                            <td dir="ltr" style="font-weight:900; color: {{ $item['profit'] >= 0 ? '#4CAF50' : '#E91E63' }};">
                                {{ number_format($item['profit'], 0) }}
                            </td>
                            <td class="text-center">
                                <button wire:click="editEvent({{ $item['event']->id }})" class="btn btn-sm me-1" style="background: rgba(33,150,243,0.1); border: 1px solid rgba(33,150,243,0.3); color: #2196F3; border-radius:6px; margin-bottom: 2px;" title="تعديل الحفلة">
                                    <i class="fa-solid fa-pen"></i> تعديل
                                </button>
                                <button wire:click="deleteEvent({{ $item['event']->id }})" wire:confirm="هل أنت متأكد من حذف هذه الحفلة نهائياً؟ سيتم حذف جميع الحجوزات المرتبطة بها." class="btn btn-sm" style="background: rgba(244,67,54,0.1); border: 1px solid rgba(244,67,54,0.3); color: #F44336; border-radius:6px;" title="حذف الحفلة">
                                    <i class="fa-solid fa-trash"></i> حذف
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="text-center py-3" style="color: var(--text-secondary);">لا توجد حفلات ضمن هذه الفترة.</td></tr>
                    @endforelse
                </tbody>
                @if(isset($totalExpense) && count($accountEventsData) > 0)
                <tfoot>
                    <tr style="background: rgba(255,255,255,0.05); font-weight: 800; border-top: 2px solid var(--border-color);">
                        <td colspan="3" class="text-start" style="color: #00BCD4;">الإجمالي الكلي</td>
                        <td style="color: #E91E63;"><i class="fa-solid fa-microphone me-1"></i> {{ $totalArtists ?? 0 }}</td>
                        <td style="color: #2196F3;"><i class="fa-solid fa-guitar me-1"></i> {{ $totalMusicians ?? 0 }}</td>
                        <td style="color: #4CAF50;">{{ number_format($totalIncome, 0) }}</td>
                        <td style="color: #E91E63;">{{ number_format($totalExpense, 0) }}</td>
                        <td style="color: {{ ($totalProfit ?? 0) >= 0 ? '#4CAF50' : '#E91E63' }};" dir="ltr">{{ number_format($totalProfit ?? 0, 0) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
    @endif

    <!-- تفاصيل حفلة محددة -->
    @if(isset($selEvent) && $selEvent)
    <div class="card mb-4" style="border: 1px solid rgba(212, 175, 55, 0.3); border-radius:12px;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold m-0" style="color: #d4af37;"><i class="fa-solid fa-star me-2"></i> تفاصيل: {{ $selEvent->title }}</h5>
            <button wire:click="$set('accounts_selected_event_id', null)" class="btn btn-sm" style="border:1px solid rgba(255,255,255,0.1); color:var(--text-secondary);">&times; إغلاق</button>
        </div>

        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.8rem; margin-bottom: 1rem;">
            <div style="padding: 0.8rem; background: rgba(255,255,255,0.02); border: 1px solid var(--border-color); border-radius:8px;">
                <div style="font-size:0.75rem; color:var(--text-secondary); margin-bottom:3px;">التاريخ</div>
                <div class="fw-bold">{{ $selEvent->event_date ?? 'غير محدد' }}</div>
            </div>
            <div style="padding: 0.8rem; background: rgba(255,255,255,0.02); border: 1px solid var(--border-color); border-radius:8px;">
                <div style="font-size:0.75rem; color:var(--text-secondary); margin-bottom:3px;">الوقت</div>
                <div class="fw-bold">{{ $selEvent->event_time ? \Carbon\Carbon::parse($selEvent->event_time)->format('h:i A') : 'غير محدد' }}</div>
            </div>
        </div>

        @if($selEvent->description)
            <div class="p-3 mb-3" style="background:rgba(255,255,255,0.02); border-radius:8px; border:1px solid rgba(255,255,255,0.05);">
                <div style="font-size:0.75rem; color:var(--text-secondary); margin-bottom:3px;">الوصف</div>
                <div style="font-size:0.9rem;">{{ $selEvent->description }}</div>
            </div>
        @endif

        <!-- الطاقم -->
        <h6 class="fw-bold mb-2" style="color: #E91E63;"><i class="fa-solid fa-microphone me-1"></i> الطاقم المحجوز</h6>
        @if(isset($selArtistBookings) && $selArtistBookings->count() > 0)
            @foreach($selArtistBookings as $sab)
                <div class="p-3 mb-2" style="background: linear-gradient(145deg, rgba(233,30,99,0.03), rgba(33,150,243,0.03)); border: 1px solid rgba(233,30,99,0.15); border-radius:8px;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            @if($sab->artist)
                                @php
                                    $isFemaleArtist = ($sab->artist->gender ?? 'male') === 'female';
                                    $artBg = $isFemaleArtist ? 'rgba(244,67,54,0.1)' : 'rgba(33,150,243,0.1)';
                                    $artColor = $isFemaleArtist ? '#F44336' : '#2196F3';
                                    $artBorder = $isFemaleArtist ? 'rgba(244,67,54,0.2)' : 'rgba(33,150,243,0.2)';
                                @endphp
                                <span class="badge" style="background:{{ $artBg }}; color:{{ $artColor }}; border:1px solid {{ $artBorder }};"><i class="fa-solid fa-microphone me-1"></i> {{ $sab->artist->stage_name ?: $sab->artist->real_name }}</span>
                            @endif
                            @if($sab->musician)
                                @php
                                    $isFemaleMusician = ($sab->musician->gender ?? 'male') === 'female';
                                    $musBg = $isFemaleMusician ? 'rgba(244,67,54,0.1)' : 'rgba(33,150,243,0.1)';
                                    $musColor = $isFemaleMusician ? '#F44336' : '#2196F3';
                                    $musBorder = $isFemaleMusician ? 'rgba(244,67,54,0.2)' : 'rgba(33,150,243,0.2)';
                                @endphp
                                <span class="badge" style="background:{{ $musBg }}; color:{{ $musColor }}; border:1px solid {{ $musBorder }};"><i class="fa-solid fa-guitar me-1"></i> {{ $sab->musician->stage_name ?: $sab->musician->real_name }}</span>
                            @endif
                        </div>
                        @php
                            $isPastSel = false;
                            if($selEvent->event_date) {
                                $dtSel = $selEvent->event_date . ' ' . ($selEvent->event_time ?? '23:59:59');
                                try { $isPastSel = \Carbon\Carbon::parse($dtSel)->isPast(); } catch(\Exception $e) {}
                            }
                        @endphp
                        @if($isPastSel)
                            <span style="font-weight:800; color: #E91E63;">{{ number_format($sab->price, 0) }} ر.ي</span>
                        @endif
                    </div>
                    @if($sab->notes)
                        <div style="font-size:0.8rem; color:var(--text-secondary); margin-top:5px;"><i class="fa-solid fa-note-sticky me-1"></i> {{ $sab->notes }}</div>
                    @endif
                </div>
            @endforeach
        @else
            <div class="text-muted" style="font-size:0.85rem;">لا توجد حجوزات فنانين لهذه الحفلة.</div>
        @endif

        <!-- حجوزات العملاء -->
        <h6 class="fw-bold mb-2 mt-3" style="color: #4CAF50;"><i class="fa-solid fa-users me-1"></i> حجوزات العملاء ({{ isset($selCustomerBookings) ? $selCustomerBookings->count() : 0 }})</h6>
        @if(isset($selCustomerBookings) && $selCustomerBookings->count() > 0)
            <div style="overflow-x:auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>اسم العميل</th>
                            <th>الجوال</th>
                            <th>الحالة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($selCustomerBookings as $cb)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="fw-bold">{{ $cb->customer_name }}</td>
                                <td dir="ltr">{{ $cb->customer_phone }}</td>
                                <td>
                                    @if($cb->status === 'approved')
                                        <span class="badge" style="background:rgba(76,175,80,0.15); color:#4CAF50; border:1px solid rgba(76,175,80,0.3);">موافق</span>
                                    @elseif($cb->status === 'pending')
                                        <span class="badge" style="background:rgba(255,152,0,0.15); color:#ff9800; border:1px solid rgba(255,152,0,0.3);">معلق</span>
                                    @elseif($cb->status === 'rejected')
                                        <span class="badge" style="background:rgba(233,30,99,0.15); color:#E91E63; border:1px solid rgba(233,30,99,0.3);">مرفوض</span>
                                    @elseif($cb->status === 'completed')
                                        <span class="badge" style="background:rgba(0,188,212,0.15); color:#00BCD4; border:1px solid rgba(0,188,212,0.3);">مكتمل</span>
                                    @else
                                        <span class="badge">{{ $cb->status }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-muted" style="font-size:0.85rem;">لا توجد حجوزات عملاء لهذه الحفلة.</div>
        @endif

        <!-- ملخص الربحية -->
        @if($isPastSel ?? false)
        @php
            $selTotalExpense = $selArtistBookings->sum('price');
        @endphp
        <div class="mt-4 p-3" style="background: linear-gradient(145deg, rgba(0,188,212,0.05), rgba(0,188,212,0.02)); border: 2px solid rgba(0,188,212,0.3); border-radius:10px;">
            <h6 class="fw-bold text-center mb-3" style="color: #00BCD4;"><i class="fa-solid fa-calculator me-2"></i> ملخص الحفلة</h6>
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; text-align:center;">
                <div>
                    <div style="font-size:0.75rem; color:var(--text-secondary);">حجوزات العملاء</div>
                    <div style="font-size:1.3rem; font-weight:900; color:#4CAF50;">{{ $selCustomerBookings->count() }}</div>
                </div>
                <div>
                    <div style="font-size:0.75rem; color:var(--text-secondary);">تكلفة الفنانين</div>
                    <div style="font-size:1.3rem; font-weight:900; color:#E91E63;">{{ number_format($selTotalExpense, 0) }} ر.ي</div>
                </div>
                <div>
                    <div style="font-size:0.75rem; color:var(--text-secondary);">عدد الفنانين</div>
                    <div style="font-size:1.3rem; font-weight:900; color:#d4af37;">{{ $selArtistBookings->count() }}</div>
                </div>
            </div>
        </div>
        @endif
    </div>
    @endif




    @endif

    {{-- ==========================================
         شاشة الحجوزات (بطاقات حفلات)
    ========================================== --}}
    @if($currentView === 'bookings')
    
    <div style="margin-bottom: 1.5rem; display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
        <button wire:click="goToView('main')" class="btn btn-outline" style="border-color: rgba(76, 175, 80, 0.4); color: #4CAF50;">
            <i class="fa-solid fa-arrow-right me-1"></i> رجوع
        </button>
        <button wire:click="openBookingModal" class="btn" style="background: linear-gradient(135deg, #4CAF50, #2E7D32); color:white;">
            <i class="fa-solid fa-plus me-1"></i> إضافة حجز جديد
        </button>
        <button wire:click="openAddMonthModal" class="btn" style="background: linear-gradient(135deg, #ff9800, #f57c00); color:white; border:none;">
            <i class="fa-solid fa-calendar-plus me-1"></i> إضافة شهر
        </button>
    </div>

    <!-- الفلاتر المتقدمة لحجوزات الحفلات -->
    <div class="card mb-4" style="border: 1px solid rgba(76,175,80,0.2); background: linear-gradient(145deg, #1a1a2e, #1e2a1e); border-radius: 12px; padding: 1.5rem; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
        <div class="filter-header-flex d-flex justify-content-between align-items-center mb-4 border-bottom pb-3" style="border-color: rgba(255,255,255,0.05) !important; flex-wrap: wrap; gap: 15px;">
            <h5 class="fw-bold m-0" style="color: #4CAF50;">
                <i class="fa-solid fa-filter me-2"></i> فرز وبحث الحجوزات
            </h5>
            
            <!-- أزرار الفلاتر السريعة -->
            <div class="d-flex gap-2 quick-filters flex-wrap">
                <style>
                    .btn-filter {
                        border: 1px solid rgba(255,255,255,0.1);
                        background: rgba(255,255,255,0.02);
                        color: var(--text-secondary);
                        border-radius: 20px;
                        padding: 0.4rem 1rem;
                        font-size: 0.85rem;
                        transition: all 0.3s ease;
                    }
                    .btn-filter:hover {
                        background: rgba(76,175,80,0.1);
                        color: #4CAF50;
                        border-color: rgba(76,175,80,0.3);
                    }
                    .btn-filter.active {
                        background: linear-gradient(135deg, #4CAF50, #2E7D32);
                        color: white;
                        border-color: transparent;
                        box-shadow: 0 4px 10px rgba(76,175,80,0.3);
                    }
                </style>
                <button wire:click="resetFilters" class="btn-filter {{ $filter_booking_status === '' && $filter_booking_date_from === '' && $filter_booking_artist_id === '' && $filter_booking_musician_id === '' && $filter_booking_month === '' ? 'active' : '' }}">
                    <i class="fa-solid fa-border-all me-1"></i> الكل
                </button>
                <button wire:click="filterToday" class="btn-filter {{ $filter_booking_date_from === now()->toDateString() && $filter_booking_date_to === now()->toDateString() ? 'active' : '' }}">
                    <i class="fa-solid fa-calendar-day me-1"></i> اليوم
                </button>
                <button wire:click="filterThisMonth" class="btn-filter {{ $filter_booking_date_from === now()->startOfMonth()->toDateString() && $filter_booking_date_to === now()->endOfMonth()->toDateString() ? 'active' : '' }}">
                    <i class="fa-solid fa-calendar-week me-1"></i> حفلات الشهر
                </button>
                <button wire:click="filterNextMonth" class="btn-filter {{ $filter_booking_date_from === now()->addMonth()->startOfMonth()->toDateString() && $filter_booking_date_to === now()->addMonth()->endOfMonth()->toDateString() ? 'active' : '' }}">
                    <i class="fa-solid fa-calendar-plus me-1"></i> حفلات الشهر القادم
                </button>
                <select wire:model.live="filter_booking_month" class="btn-filter" style="cursor: pointer; outline: none; background-color: #161822; color: #e2e8f0; border: 1px solid rgba(76,175,80,0.35);">
                    <option value="">📅 اختر الشهر...</option>
                    @foreach($availableBookingMonths as $ymKey => $ymLabel)
                        <option value="{{ $ymKey }}">{{ $ymLabel }}</option>
                    @endforeach
                </select>
                <button wire:click="$set('filter_booking_status', '{{ $filter_booking_status === 'available' ? '' : 'available' }}')" class="btn-filter {{ $filter_booking_status === 'available' ? 'active' : '' }}">
                    <i class="fa-solid fa-circle-check me-1"></i> متاح
                </button>
                <button wire:click="$set('filter_booking_status', '{{ $filter_booking_status === 'booked' ? '' : 'booked' }}')" class="btn-filter {{ $filter_booking_status === 'booked' ? 'active' : '' }}">
                    <i class="fa-solid fa-bookmark me-1"></i> محجوز
                </button>
                <button wire:click="$set('filter_booking_status', '{{ $filter_booking_status === 'approved' ? '' : 'approved' }}')" class="btn-filter {{ $filter_booking_status === 'approved' ? 'active' : '' }}">
                    <i class="fa-solid fa-check-circle me-1"></i> المعتمد
                </button>
                <button wire:click="$set('filter_booking_status', '{{ $filter_booking_status === 'completed' ? '' : 'completed' }}')" class="btn-filter {{ $filter_booking_status === 'completed' ? 'active' : '' }}">
                    <i class="fa-solid fa-flag-checkered me-1"></i> المكتمل
                </button>
                <button wire:click="$set('filter_booking_status', '{{ $filter_booking_status === 'holiday' ? '' : 'holiday' }}')" class="btn-filter {{ $filter_booking_status === 'holiday' ? 'active' : '' }}">
                    <i class="fa-solid fa-crown me-1"></i> العطلات
                </button>
            </div>
        </div>

        <div class="booking-filters-grid" style="display: grid; grid-template-columns: repeat(5, minmax(150px, 1fr)); gap: 12px; align-items: end; overflow-x: auto; padding-bottom: 8px;">
            <div>
                <label class="form-label" style="font-size:0.85rem; color:var(--text-secondary); white-space: nowrap;">الفنان</label>
                <div class="input-group">
                    <span class="input-group-text" style="background: rgba(255,255,255,0.02); border-color: rgba(255,255,255,0.1); color: #4CAF50;">
                        <i class="fa-solid fa-microphone"></i>
                    </span>
                    <select wire:model.live="filter_booking_artist_id" class="form-control" style="background: rgba(255,255,255,0.02); border-color: rgba(255,255,255,0.1); color: var(--text-primary);">
                        <option value="">جميع الفنانين</option>
                        @foreach($artists as $artist)
                            <option value="{{ $artist->id }}">{{ $artist->real_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="form-label" style="font-size:0.85rem; color:var(--text-secondary); white-space: nowrap;">العازف</label>
                <div class="input-group">
                    <span class="input-group-text" style="background: rgba(255,255,255,0.02); border-color: rgba(255,255,255,0.1); color: #2196F3;">
                        <i class="fa-solid fa-guitar"></i>
                    </span>
                    <select wire:model.live="filter_booking_musician_id" class="form-control" style="background: rgba(255,255,255,0.02); border-color: rgba(255,255,255,0.1); color: var(--text-primary);">
                        <option value="">جميع العازفين</option>
                        @foreach($musicians as $musician)
                            <option value="{{ $musician->id }}">{{ $musician->real_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div>
                <label class="form-label" style="font-size:0.85rem; color:var(--text-secondary); white-space: nowrap;">حالة الحفلة</label>
                <div class="input-group">
                    <span class="input-group-text" style="background: rgba(255,255,255,0.02); border-color: rgba(255,255,255,0.1); color: #4CAF50;">
                        <i class="fa-solid fa-layer-group"></i>
                    </span>
                    <select wire:model.live="filter_booking_status" class="form-control" style="background: rgba(255,255,255,0.02); border-color: rgba(255,255,255,0.1); color: var(--text-primary);">
                        <option value="">جميع الحالات</option>
                        <option value="available">متاح (بدون حجوزات)</option>
                        <option value="booked">محجوز (به حجوزات)</option>
                        <option value="approved">معتمد (قادمة أو قائمة)</option>
                        <option value="suggested">مقترح (بانتظار الاعتماد)</option>
                        <option value="completed">مكتملة</option>
                        <option value="holiday">العطلات (الخميس والجمعة)</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="form-label" style="font-size:0.85rem; color:var(--text-secondary); white-space: nowrap;">من تاريخ</label>
                <div class="input-group">
                    <span class="input-group-text" style="background: rgba(255,255,255,0.02); border-color: rgba(255,255,255,0.1); color: #4CAF50;">
                        <i class="fa-regular fa-calendar"></i>
                    </span>
                    <input type="date" wire:model.live="filter_booking_date_from" class="form-control" style="background: rgba(255,255,255,0.02); border-color: rgba(255,255,255,0.1); color: var(--text-primary);">
                </div>
            </div>

            <div>
                <label class="form-label" style="font-size:0.85rem; color:var(--text-secondary); white-space: nowrap;">إلى تاريخ</label>
                <div class="input-group">
                    <span class="input-group-text" style="background: rgba(255,255,255,0.02); border-color: rgba(255,255,255,0.1); color: #4CAF50;">
                        <i class="fa-regular fa-calendar-check"></i>
                    </span>
                    <input type="date" wire:model.live="filter_booking_date_to" class="form-control" style="background: rgba(255,255,255,0.02); border-color: rgba(255,255,255,0.1); color: var(--text-primary);">
                </div>
            </div>
        </div>
    </div>

    <!-- دليل الألوان والحالات -->
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3 px-2" style="font-size: 0.8rem; color: var(--text-secondary);">
        <div class="d-flex align-items-center gap-2">
            <span style="display:inline-block; width:44px; height:10px; border-radius:4px; background: linear-gradient(90deg, #ffb300, #ffd54f, #ffb300);"></span>
            <span>تعني يوم <strong style="color:#ffd54f;">الخميس أو الجمعة</strong></span>
        </div>
        <div class="d-flex align-items-center flex-wrap gap-3">
            <span class="d-inline-flex align-items-center gap-1"><span style="width:10px; height:10px; border-radius:3px; background:#525866; display:inline-block;"></span> مكتملة</span>
            <span class="d-inline-flex align-items-center gap-1"><span style="width:10px; height:10px; border-radius:3px; background:#2e7d32; display:inline-block;"></span> متاح</span>
            <span class="d-inline-flex align-items-center gap-1"><span style="width:10px; height:10px; border-radius:3px; background:#c62828; display:inline-block;"></span> محجوز</span>
            <span class="d-inline-flex align-items-center gap-1"><span style="width:10px; height:10px; border-radius:3px; background:#25334a; border:1px solid #2196F3; display:inline-block;"></span> اليوم</span>
        </div>
    </div>

    <style>
        .bookings-matrix-grid {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            gap: 8px;
            align-items: stretch;
        }
        @media (max-width: 1200px) {
            .bookings-matrix-grid {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }
        }
        @media (max-width: 768px) {
            .bookings-matrix-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
        @media (max-width: 480px) {
            .bookings-matrix-grid {
                grid-template-columns: 1fr;
            }
        }
        .booking-matrix-card {
            background: #141418;
            border-radius: 8px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            height: 100%;
            min-height: 205px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .booking-matrix-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.45);
        }
        .booking-matrix-action-btn {
            flex: 1;
            height: 26px;
            padding: 0;
            border-radius: 5px;
            font-size: 0.72rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            cursor: pointer;
            transition: opacity 0.2s ease;
        }
        .booking-matrix-action-btn:hover {
            opacity: 0.85;
        }
    </style>

    @if($eventsWithBookings->count() > 0)
        <div class="bookings-matrix-grid">
            @foreach($eventsWithBookings as $ev)
                @php
                    $evBookings = $ev->artistBookings;
                    $isPast = false;
                    $isDateReached = false;
                    $carbonDate = null;
                    if ($ev->event_date) {
                        try { $carbonDate = \Carbon\Carbon::parse($ev->event_date); } catch (\Exception $e) {}
                        $dtStr = $ev->event_date . ' ' . ($ev->event_time ?? '23:59:59');
                        try { $isPast = \Carbon\Carbon::parse($dtStr)->isPast(); } catch (\Exception $e) {}
                        try { $isDateReached = \Carbon\Carbon::parse($ev->event_date)->startOfDay()->lte(now()->startOfDay()); } catch (\Exception $e) {}
                    }

                    $isWeekend = $carbonDate ? in_array($carbonDate->dayOfWeek, [\Carbon\Carbon::THURSDAY, \Carbon\Carbon::FRIDAY]) : false;
                    $isToday = $carbonDate ? $carbonDate->isToday() : false;
                    $hasBookings = $evBookings->count() > 0;
                    $hasSuggested = $evBookings->where('status', 'suggested')->count() > 0;

                    if ($ev->is_ended) {
                        $statusLabel = 'مكتملة';
                        $headerBg = '#525866';
                        $borderColor = 'rgba(158, 158, 158, 0.35)';
                    } elseif ($isToday) {
                        $statusLabel = 'اليوم';
                        $headerBg = '#25334a';
                        $borderColor = 'rgba(33, 150, 243, 0.45)';
                    } elseif ($hasBookings) {
                        $statusLabel = 'محجوز';
                        $headerBg = '#c62828';
                        $borderColor = 'rgba(229, 57, 53, 0.45)';
                    } else {
                        $statusLabel = 'متاح';
                        $headerBg = '#2e7d32';
                        $borderColor = 'rgba(76, 175, 80, 0.45)';
                    }
                @endphp

                <div class="booking-matrix-card" style="border: 1px solid {{ $borderColor }};">
                    @if($isWeekend)
                        <div style="height: 8px; width: 100%; background: linear-gradient(90deg, #ffb300, #ffd54f, #ffb300);" title="يوم خميس أو جمعة"></div>
                    @endif

                    <div style="background: {{ $headerBg }}; color: #ffffff; text-align: center; font-weight: 800; font-size: 0.78rem; padding: 4px 6px; letter-spacing: 0.3px;">
                        {{ $statusLabel }}
                    </div>

                    <div style="padding: 8px 7px; flex: 1; display: flex; flex-direction: column; gap: 6px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; gap: 4px; font-size: 0.84rem;">
                            <span style="color: #ff9800; font-weight: 800; display: inline-flex; align-items: center; gap: 4px; white-space: nowrap;">
                                <span style="width: 6px; height: 6px; border-radius: 50%; background: #ff9800; display: inline-block;"></span>
                                {{ $carbonDate ? $carbonDate->locale('ar')->translatedFormat('l') : '---' }}
                            </span>
                            <span style="color: #4CAF50; font-weight: 800; direction: ltr;">
                                {{ $carbonDate ? $carbonDate->format('Y-m-d') : 'غير محدد' }}
                            </span>
                        </div>

                        <div style="font-weight: 700; font-size: 0.78rem; color: #ffffff; text-align: center; line-height: 1.3; word-break: break-word;">
                            {{ $ev->title ?? 'بدون عنوان' }}
                            @if($ev->is_hidden)
                                <span class="badge ms-1" style="background: rgba(158,158,158,0.15); color: #9E9E9E; border: 1px solid rgba(158,158,158,0.3); font-size: 0.6rem; padding: 1px 5px; border-radius: 8px;">مخفية</span>
                            @endif
                        </div>

                        @if($evBookings->count() > 0)
                            <div style="display: flex; flex-direction: column; gap: 4px; align-items: center; margin-top: 2px;">
                                @if($evBookings->whereNotNull('artist_id')->count() > 0)
                                    <div style="display: flex; flex-wrap: wrap; gap: 3px; justify-content: center;">
                                        @foreach($evBookings->whereNotNull('artist_id') as $bk)
                                            @php
                                                $isFemale = ($bk->artist->gender ?? 'male') === 'female';
                                                $bgColor = $isFemale ? 'rgba(233,30,99,0.14)' : 'rgba(33,150,243,0.14)';
                                                $textColor = $isFemale ? '#f06292' : '#4fc3f7';
                                                $bColor = $isFemale ? 'rgba(233,30,99,0.3)' : 'rgba(33,150,243,0.3)';
                                            @endphp
                                            <span style="background: {{ $bgColor }}; color: {{ $textColor }}; border: 1px solid {{ $bColor }}; font-size: 0.92rem; font-weight: 800; padding: 5px 10px; border-radius: 14px; display: inline-flex; align-items: center; gap: 6px; max-width: 100%;">
                                                <i class="fa-solid fa-microphone" style="font-size: 0.82rem;"></i>
                                                <span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $bk->artist->stage_name ?: $bk->artist->real_name ?? '' }}</span>
                                                @if(!$ev->is_ended)
                                                    <button wire:click="deleteBooking({{ $bk->id }})" wire:confirm="هل أنت متأكد من حذف حجز الفنان {{ $bk->artist->stage_name ?: $bk->artist->real_name ?? '' }} من هذه الحفلة؟" style="background: none; border: none; color: #F44336; cursor: pointer; padding: 0; font-size: 0.85rem; line-height: 1;" title="حذف هذا الحجز">
                                                        <i class="fa-solid fa-xmark"></i>
                                                    </button>
                                                @endif
                                            </span>
                                        @endforeach
                                    </div>
                                @endif

                                @if($evBookings->whereNotNull('musician_id')->count() > 0)
                                    <div style="display: flex; flex-wrap: wrap; gap: 4px; justify-content: center;">
                                        @foreach($evBookings->whereNotNull('musician_id') as $bk)
                                            <span style="background: rgba(76,175,80,0.14); color: #81c784; border: 1px solid rgba(76,175,80,0.35); font-size: 0.9rem; font-weight: 800; padding: 5px 10px; border-radius: 14px; display: inline-flex; align-items: center; gap: 6px; max-width: 100%;">
                                                <span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $bk->musician->stage_name ?: $bk->musician->real_name ?? '' }}</span>
                                                @if(!$ev->is_ended)
                                                    <button wire:click="deleteBooking({{ $bk->id }})" wire:confirm="هل أنت متأكد من حذف حجز العازف {{ $bk->musician->stage_name ?: $bk->musician->real_name ?? '' }} من هذه الحفلة؟" style="background: none; border: none; color: #F44336; cursor: pointer; padding: 0; font-size: 0.85rem; line-height: 1;" title="حذف هذا الحجز">
                                                        <i class="fa-solid fa-xmark"></i>
                                                    </button>
                                                @endif
                                            </span>
                                        @endforeach
                                    </div>
                                @endif

                                @if($hasSuggested && !$ev->is_ended)
                                    <div style="display: flex; flex-wrap: wrap; gap: 3px; justify-content: center;">
                                        @foreach($evBookings->where('status', 'suggested') as $bk)
                                            <button wire:click="approveBooking({{ $bk->id }})" style="background: rgba(33,150,243,0.15); border: 1px solid rgba(33,150,243,0.35); color: #4fc3f7; font-size: 0.62rem; padding: 1px 6px; border-radius: 10px; cursor: pointer;" title="اعتماد حجز {{ $bk->artist->real_name ?? ($bk->musician->real_name ?? '') }}">
                                                <i class="fa-solid fa-check me-1"></i>اعتماد
                                            </button>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endif

                        @if($isDateReached && !$ev->is_ended)
                            <div style="margin-top: auto; padding-top: 4px; text-align: center;">
                                <button wire:click="openEndEventModal({{ $ev->id }})" class="btn btn-sm w-100" style="background: rgba(76,175,80,0.15); border: 1px solid rgba(76,175,80,0.4); color: #66bb6a; font-size: 0.68rem; font-weight: 700; padding: 3px 6px; border-radius: 5px;" title="إنهاء الحفلة">
                                    <i class="fa-solid fa-flag-checkered me-1"></i> إنهاء الحفلة
                                </button>
                            </div>
                        @endif
                    </div>

                    <div style="margin-top: auto; padding: 6px; border-top: 1px solid rgba(255,255,255,0.06); background: rgba(0,0,0,0.2);">
                        @if($ev->is_ended)
                            <div style="display: flex; gap: 5px;">
                                <button wire:click="openEndEventModal({{ $ev->id }})" class="booking-matrix-action-btn" style="background: rgba(33,150,243,0.16); border: 1px solid rgba(33,150,243,0.35); color: #4fc3f7; gap: 4px;" title="تعديل تفاصيل الإنهاء">
                                    <i class="fa-solid fa-pen-to-square"></i> تعديل
                                </button>
                                <button wire:click="hideEvent({{ $ev->id }})" wire:confirm="هل أنت متأكد من إخفاء هذه الحفلة من القائمة؟ (يمكنك إيجادها عبر البحث لاحقاً)" class="booking-matrix-action-btn" style="background: rgba(158,158,158,0.16); border: 1px solid rgba(158,158,158,0.35); color: #bdbdbd; gap: 4px;" title="إخفاء من القائمة">
                                    <i class="fa-solid fa-eye-slash"></i> حذف
                                </button>
                            </div>
                        @else
                            <div style="display: flex; gap: 3px; justify-content: space-between;">
                                <button wire:click="openAddToEventModal({{ $ev->id }})" class="booking-matrix-action-btn" style="background: rgba(76,175,80,0.16); border: 1px solid rgba(76,175,80,0.35); color: #66bb6a;" title="إضافة فنان/عازف">
                                    <i class="fa-solid fa-user-plus"></i>
                                </button>
                                <a href="{{ route('admin.customers', ['market_event' => $ev->id]) }}" class="booking-matrix-action-btn" style="background: rgba(255,152,0,0.16); border: 1px solid rgba(255,152,0,0.35); color: #ffb74d;" title="تسويق الحفلة">
                                    <i class="fa-solid fa-paper-plane"></i>
                                </a>
                                <button wire:click="openPostShareModal({{ $ev->id }})" class="booking-matrix-action-btn" style="background: rgba(156,39,176,0.16); border: 1px solid rgba(156,39,176,0.35); color: #ce93d8;" title="مشاركة البوست">
                                    <i class="fa-solid fa-share-nodes"></i>
                                </button>
                                <button wire:click="editEvent({{ $ev->id }})" class="booking-matrix-action-btn" style="background: rgba(33,150,243,0.16); border: 1px solid rgba(33,150,243,0.35); color: #4fc3f7;" title="تعديل الحفلة">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <button wire:click="openUploadPostImageModal({{ $ev->id }})" class="booking-matrix-action-btn" style="background: rgba(0,188,212,0.16); border: 1px solid rgba(0,188,212,0.35); color: #4dd0e1;" title="رفع صورة البوست">
                                    <i class="fa-solid fa-upload"></i>
                                </button>
                                <button wire:click="deleteEvent({{ $ev->id }})" wire:confirm="هل أنت متأكد من حذف هذه الحفلة نهائياً؟" class="booking-matrix-action-btn" style="background: rgba(233,30,99,0.16); border: 1px solid rgba(233,30,99,0.35); color: #f06292;" title="حذف الحفلة">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="card text-center py-5" style="border: 1px solid rgba(76,175,80,0.2);">
            <div style="color: var(--text-secondary);">
                <i class="fa-solid fa-calendar-check fa-2x mb-3" style="color: rgba(76,175,80,0.3);"></i>
                <h6>لا توجد حجوزات بعد. اضغط "إضافة حجز جديد" لبدء حجز فنان.</h6>
            </div>
        </div>
    @endif
    
    @endif

    {{-- ==========================================
         MODAL: إضافة / تعديل فنان
    ========================================== --}}
    @if($isArtistModalOpen)
        <div style="position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:1050; display:flex; justify-content:center; align-items:center; backdrop-filter: blur(4px);">
            <div class="card p-4" style="background:var(--surface-color); border:1px solid rgba(233,30,99,0.3); width:95%; max-width:550px; max-height:90vh; overflow-y:auto; border-radius:16px; box-shadow: 0 20px 40px rgba(0,0,0,0.6);">
                <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
                    <h5 class="fw-bold m-0" style="color: #E91E63;">
                        <i class="fa-solid fa-user-plus me-2"></i> 
                        {{ $editingArtistId ? 'تعديل بيانات الفنان' : 'إضافة فنان جديد' }}
                    </h5>
                    <button type="button" style="font-size:1.3rem; background:none; border:none; color:var(--text-secondary); cursor:pointer;" wire:click="$set('isArtistModalOpen', false)">&times;</button>
                </div>
                <form wire:submit.prevent="saveArtist">
                    <div class="mb-3">
                        <label class="form-label fw-bold">الاسم الحقيقي <span class="text-danger">*</span></label>
                        <input type="text" wire:model="artist_real_name" class="form-control" required placeholder="أدخل الاسم الحقيقي...">
                        @error('artist_real_name') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">الاسم الفني</label>
                        <input type="text" wire:model="artist_stage_name" class="form-control" placeholder="الاسم الفني (اختياري)...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">الجنسية</label>
                        <input type="text" wire:model="artist_nationality" class="form-control" placeholder="الجنسية...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">الرقم التعريفي (رقم مزاولة المهنة)</label>
                        <input type="text" wire:model="artist_license_number" class="form-control" placeholder="رقم الترخيص...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">رقم الجوال</label>
                        <input type="text" wire:model="artist_phone" class="form-control" dir="ltr" placeholder="رقم الجوال...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">الجنس</label>
                        <select wire:model="artist_gender" class="form-control">
                            <option value="male">ذكر</option>
                            <option value="female">أنثى</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">صورة شخصية</label>
                        @if($existingArtistImage)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $existingArtistImage) }}" style="width:60px; height:60px; border-radius:50%; object-fit:cover; border:2px solid var(--border-color);">
                                <small style="color: var(--text-secondary);">الصورة الحالية</small>
                            </div>
                        @endif
                        <input type="file" wire:model="artist_image" class="form-control" accept="image/*">
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                        <button type="button" wire:click="$set('isArtistModalOpen', false)" class="btn" style="background: var(--surface-hover); color: var(--text-primary); border:1px solid var(--border-color);">إلغاء</button>
                        <button type="submit" class="btn" style="background: linear-gradient(135deg, #E91E63, #C2185B); color:white;">
                            <i class="fa-solid fa-check me-1"></i> {{ $editingArtistId ? 'حفظ التعديلات' : 'حفظ الفنان' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ==========================================
         MODAL: إضافة / تعديل عازف
    ========================================== --}}
    @if($isMusicianModalOpen)
        <div style="position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:1050; display:flex; justify-content:center; align-items:center; backdrop-filter: blur(4px);">
            <div class="card p-4" style="background:var(--surface-color); border:1px solid rgba(33,150,243,0.3); width:95%; max-width:550px; max-height:90vh; overflow-y:auto; border-radius:16px; box-shadow: 0 20px 40px rgba(0,0,0,0.6);">
                <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
                    <h5 class="fw-bold m-0" style="color: #2196F3;">
                        <i class="fa-solid fa-guitar me-2"></i>
                        {{ $editingMusicianId ? 'تعديل بيانات العازف' : 'إضافة عازف جديد' }}
                    </h5>
                    <button type="button" style="font-size:1.3rem; background:none; border:none; color:var(--text-secondary); cursor:pointer;" wire:click="$set('isMusicianModalOpen', false)">&times;</button>
                </div>
                <form wire:submit.prevent="saveMusician">
                    <div class="mb-3">
                        <label class="form-label fw-bold">الاسم الحقيقي <span class="text-danger">*</span></label>
                        <input type="text" wire:model="musician_real_name" class="form-control" required placeholder="أدخل الاسم الحقيقي...">
                        @error('musician_real_name') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">الاسم الفني</label>
                        <input type="text" wire:model="musician_stage_name" class="form-control" placeholder="الاسم الفني (اختياري)...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">الجنسية <span class="text-danger">*</span></label>
                        <input type="text" wire:model="musician_nationality" class="form-control" required placeholder="الجنسية...">
                        @error('musician_nationality') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">نوع العزف</label>
                        <select wire:model.live="musician_instrument" class="form-control">
                            <option value="">-- اختر نوع الآلة --</option>
                            <option value="عود">عود</option>
                            <option value="طبل">طبل</option>
                            <option value="أورج">أورج</option>
                            <option value="كمان">كمان</option>
                            <option value="قيثارة">قيثارة</option>
                            <option value="ناي">ناي</option>
                            <option value="دف">دف</option>
                            <option value="أخرى">أخرى (أدخل يدوياً)</option>
                        </select>
                    </div>
                    @if($musician_instrument === 'أخرى')
                    <div class="mb-3">
                        <label class="form-label fw-bold">حدد نوع الآلة <span class="text-danger">*</span></label>
                        <input type="text" wire:model="musician_instrument_custom" class="form-control" placeholder="اكتب نوع الآلة هنا..." required>
                    </div>
                    @endif
                    <div class="mb-3">
                        <label class="form-label fw-bold">الرقم التعريفي</label>
                        <input type="text" wire:model="musician_license_number" class="form-control" placeholder="رقم الترخيص...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">رقم الجوال</label>
                        <input type="text" wire:model="musician_phone" class="form-control" dir="ltr" placeholder="رقم الجوال...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">صورة شخصية</label>
                        @if($existingMusicianImage)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $existingMusicianImage) }}" style="width:60px; height:60px; border-radius:50%; object-fit:cover; border:2px solid var(--border-color);">
                                <small style="color: var(--text-secondary);">الصورة الحالية</small>
                            </div>
                        @endif
                        <input type="file" wire:model="musician_image" class="form-control" accept="image/*">
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                        <button type="button" wire:click="$set('isMusicianModalOpen', false)" class="btn" style="background: var(--surface-hover); color: var(--text-primary); border:1px solid var(--border-color);">إلغاء</button>
                        <button type="submit" class="btn" style="background: linear-gradient(135deg, #2196F3, #1565C0); color:white;">
                            <i class="fa-solid fa-check me-1"></i> {{ $editingMusicianId ? 'حفظ التعديلات' : 'حفظ العازف' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ==========================================
         MODAL: حجز فنان وعازف
    ========================================== --}}
    @if($isBookingModalOpen)
        <div style="position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:1050; display:flex; justify-content:center; align-items:center; backdrop-filter: blur(4px);">
            <div class="card p-4 modal-content-responsive" style="background:var(--surface-color); border:1px solid rgba(76,175,80,0.3); width:95%; max-width:600px; max-height:90vh; overflow-y:auto; border-radius:16px; box-shadow: 0 20px 40px rgba(0,0,0,0.6);">
                <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
                    <h5 class="fw-bold m-0" style="color: #4CAF50;"><i class="fa-solid fa-calendar-check me-2"></i> حجز فنان وعازف</h5>
                    <button type="button" style="font-size:1.3rem; background:none; border:none; color:var(--text-secondary); cursor:pointer;" wire:click="$set('isBookingModalOpen', false)">&times;</button>
                </div>
                
                @if($conflictWarning)
                <div class="alert alert-warning d-flex flex-column align-items-center text-center p-4 mb-4" style="border-radius: 12px; border: 2px solid #ff9800; background-color: rgba(255, 152, 0, 0.1);">
                    <i class="fa-solid fa-triangle-exclamation fa-3x mb-3" style="color: #ff9800;"></i>
                    <h5 class="fw-bold text-dark" style="color: #ff9800 !important;">تنبيه تعارض حجز!</h5>
                    <p class="mb-3 text-dark" style="color: #e0e0e0 !important;">الفنان <strong>{{ $conflictArtistName }}</strong> محجوز مسبقاً في هذا اليوم.<br>هل تريد حجز فنان آخر؟</p>
                    <div class="d-flex gap-3">
                        <button type="button" wire:click="cancelBookingConflict" class="btn btn-warning fw-bold" style="color: #000; background-color: #ff9800; border-color: #ff9800;"><i class="fa-solid fa-user-pen me-1"></i> نعم، أريد اختيار فنان آخر</button>
                        <button type="button" wire:click="proceedWithBooking" class="btn btn-outline-secondary" style="color: #bbb; border-color: #bbb;"><i class="fa-solid fa-check-double me-1"></i> لا، استمر بالحفظ</button>
                    </div>
                </div>
                @else
                <form wire:submit.prevent="saveBooking">
                    {{-- اختيار قالب حفلة --}}
                    @if($eventTemplates->count() > 0)
                    <div class="mb-3 p-3" style="background: rgba(156,39,176,0.06); border: 1px solid rgba(156,39,176,0.2); border-radius: 8px;">
                        <label class="form-label fw-bold" style="color: #CE93D8;"><i class="fa-solid fa-copy me-1"></i> اختيار قالب حفلة (اختياري)</label>
                        <select wire:model.live="booking_template_id" class="form-control">
                            <option value="">-- بدون قالب (إدخال يدوي) --</option>
                            @foreach($eventTemplates as $tmpl)
                                <option value="{{ $tmpl->id }}">{{ $tmpl->title }}</option>
                            @endforeach
                        </select>
                        <small style="color: var(--text-secondary); font-size: 0.75rem;">اختر قالب لتعبئة البيانات تلقائياً، أو اكتب يدوياً بالأسفل.</small>
                    </div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">عنوان الحفلة (اختياري)</label>
                            <input type="text" wire:model="booking_event_title" class="form-control" placeholder="عنوان الحفلة...">
                            @error('booking_event_title') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">تاريخ الحفلة <span class="text-danger">*</span></label>
                            <input type="date" wire:model="booking_event_date" class="form-control" required>
                            @error('booking_event_date') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label class="form-label fw-bold">وصف الحفلة</label>
                            <textarea wire:model="booking_event_description" class="form-control" rows="2" placeholder="وصف الحفلة..."></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">وقت الحفلة</label>
                            <input type="time" wire:model="booking_event_time" class="form-control">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">صورة الحفلة / الفنان</label>
                        <input type="file" wire:model="booking_event_image" class="form-control" accept="image/*">
                    </div>

                    <div class="p-3 mb-3" style="background: rgba(255,255,255,0.02); border: 1px solid var(--border-color); border-radius: 8px;">
                        <h6 class="fw-bold mb-3 border-bottom pb-2" style="color: var(--primary-color);">تحديد الطاقم</h6>
                        <div class="crew-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                            <div>
                                <label class="form-label fw-bold" style="font-size: 0.9rem;">اختيار الفنان 1 (اختياري)</label>
                                <select wire:model="booking_artist_id_1" class="form-control">
                                    <option value="">-- اضغط لاختيار الفنان --</option>
                                    @foreach($artists as $a)
                                        <option value="{{ $a->id }}">{{ $a->stage_name ?: $a->real_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="form-label fw-bold" style="font-size: 0.9rem;">اختيار الفنان 2 (اختياري)</label>
                                <select wire:model="booking_artist_id_2" class="form-control">
                                    <option value="">-- اضغط لاختيار الفنان --</option>
                                    @foreach($artists as $a)
                                        <option value="{{ $a->id }}">{{ $a->stage_name ?: $a->real_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="crew-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                            <div>
                                <label class="form-label fw-bold" style="font-size: 0.9rem;">اختيار العازف 1 (اختياري)</label>
                                <select wire:model="booking_musician_id_1" class="form-control">
                                    <option value="">-- اضغط لاختيار العازف --</option>
                                    @foreach($musicians as $m)
                                        <option value="{{ $m->id }}">{{ $m->real_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="form-label fw-bold" style="font-size: 0.9rem;">اختيار العازف 2 (اختياري)</label>
                                <select wire:model="booking_musician_id_2" class="form-control">
                                    <option value="">-- اضغط لاختيار العازف --</option>
                                    @foreach($musicians as $m)
                                        <option value="{{ $m->id }}">{{ $m->real_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">البوست / الملاحظات</label>
                        <textarea wire:model="booking_notes" class="form-control" rows="3" placeholder="البوست..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">حالة الحجز</label>
                        <select wire:model="booking_status" class="form-control">
                            <option value="approved">معتمد</option>
                            <option value="suggested">مقترح</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">السعر (لن يظهر للعملاء أو في الحفلات حتى انتهاء الموعد)</label>
                        <input type="number" wire:model="booking_price" class="form-control" placeholder="0" min="0" step="1">
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                        <button type="button" wire:click="$set('isBookingModalOpen', false)" class="btn" style="background: var(--surface-hover); color: var(--text-primary); border:1px solid var(--border-color);">إلغاء</button>
                        <button type="submit" class="btn btn-success"><i class="fa-solid fa-check me-1"></i> حفظ</button>
                    </div>
                </form>
                @endif
            </div>
        </div>
    @endif

    {{-- ==========================================
         MODAL: إنشاء حفلة
    ========================================== --}}
    @if($isEventModalOpen)
        <div style="position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:1050; display:flex; justify-content:center; align-items:center; backdrop-filter: blur(4px);">
            <div class="card p-4 modal-content-responsive" style="background:var(--surface-color); border:1px solid rgba(212,175,55,0.3); width:95%; max-width:550px; max-height:90vh; overflow-y:auto; border-radius:16px; box-shadow: 0 20px 40px rgba(0,0,0,0.6);">
                <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
                    <h5 class="fw-bold m-0" style="color: var(--primary-color);">
                        <i class="fa-solid fa-star me-2"></i> 
                        {{ $editingEventId ? 'تعديل الحفلة' : 'إنشاء حفلة جديدة' }}
                    </h5>
                    <button type="button" style="font-size:1.3rem; background:none; border:none; color:var(--text-secondary); cursor:pointer;" wire:click="$set('isEventModalOpen', false)">&times;</button>
                </div>
                @if(!$editingEventId)
                <div class="p-3 mb-3 rounded" style="background: rgba(212,175,55,0.08); border: 1px solid rgba(212,175,55,0.2);">
                    <small style="color: var(--primary-color);"><i class="fa-solid fa-circle-info me-1"></i> سيتم إشعار جميع العملاء تلقائياً عند إنشاء الحفلة وستظهر في تطبيق العملاء.</small>
                </div>
                @endif
                <form wire:submit.prevent="saveEvent">
                    <div class="mb-3">
                        <label class="form-label fw-bold">عنوان الحفلة <span class="text-danger">*</span></label>
                        <input type="text" wire:model="event_title" class="form-control" required placeholder="اسم أو عنوان الحفلة...">
                        @error('event_title') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">وصف الحفلة</label>
                        <textarea wire:model="event_description" class="form-control" rows="3" placeholder="تفاصيل الحفلة..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">تاريخ الحفلة</label>
                        <input type="date" wire:model="event_date" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">وقت الحفلة</label>
                        <input type="time" wire:model="event_time" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">صورة الحفلة / الفنان</label>
                        @if($existingEventImage)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $existingEventImage) }}" style="width:100px; height:60px; object-fit:cover; border:2px solid var(--border-color); border-radius: 8px;">
                                <small style="color: var(--text-secondary);">الصورة الحالية</small>
                            </div>
                        @endif
                        <input type="file" wire:model="event_image" class="form-control" accept="image/*">
                    </div>
                    
                    @if($editingEventId)
                        @php
                            $currentBookings = \App\Models\ArtistBooking::with(['artist', 'musician'])->where('event_id', $editingEventId)->get();
                        @endphp
                        <div class="mb-3 p-3 rounded" style="background: rgba(33,150,243,0.05); border: 1px solid rgba(33,150,243,0.2);">
                            <label class="form-label fw-bold text-info mb-2"><i class="fa-solid fa-users me-1"></i> الفنانين والعازفين في هذه الحفلة ({{ count($currentBookings) }}):</label>
                            
                            @if(count($currentBookings) > 0)
                                <div style="display:flex; flex-direction:column; gap:10px;">
                                    @foreach($currentBookings as $bk)
                                        <div class="p-2 rounded" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08);">
                                            <div class="row g-2 align-items-center">
                                                {{-- Artist Select --}}
                                                <div class="col-md-5">
                                                    <label style="font-size:0.7rem; color:var(--text-secondary);"><i class="fa-solid fa-microphone me-1" style="color:#E91E63;"></i> الفنان</label>
                                                    <select class="form-select form-select-sm" style="font-size:0.8rem;" 
                                                        wire:change="updateBookingArtist({{ $bk->id }}, $event.target.value)">
                                                        @foreach($artists as $art)
                                                            <option value="{{ $art->id }}" {{ $bk->artist_id == $art->id ? 'selected' : '' }}>{{ $art->real_name }} {{ $art->stage_name ? '('.$art->stage_name.')' : '' }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                {{-- Musician Select --}}
                                                <div class="col-md-5">
                                                    <label style="font-size:0.7rem; color:var(--text-secondary);"><i class="fa-solid fa-guitar me-1" style="color:#2196F3;"></i> العازف</label>
                                                    <select class="form-select form-select-sm" style="font-size:0.8rem;"
                                                        wire:change="updateBookingMusician({{ $bk->id }}, $event.target.value)">
                                                        <option value="">بدون عازف</option>
                                                        @foreach($musicians as $mus)
                                                            <option value="{{ $mus->id }}" {{ $bk->musician_id == $mus->id ? 'selected' : '' }}>{{ $mus->real_name }} {{ $mus->instrument ? '('.$mus->instrument.')' : '' }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                {{-- Delete Button --}}
                                                <div class="col-md-2 text-center" style="padding-top: 18px;">
                                                    <button type="button" wire:click="deleteBooking({{ $bk->id }})" wire:confirm="هل أنت متأكد من حذف هذا الحجز؟" class="btn btn-sm" style="background: rgba(244,67,54,0.12); border: 1px solid rgba(244,67,54,0.3); color: #F44336; font-size:0.75rem;" title="حذف الحجز">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center text-muted py-2" style="font-size:0.85rem;">
                                    <i class="fa-solid fa-user-slash me-1"></i> لا يوجد فنانين أو عازفين محجوزين لهذه الحفلة.
                                </div>
                            @endif

                            <div class="mt-2 text-center">
                                <button type="button" wire:click="openAddToEventModal({{ $editingEventId }})" class="btn btn-sm" style="background: rgba(76,175,80,0.12); border: 1px solid rgba(76,175,80,0.3); color: #4CAF50; font-size:0.8rem;">
                                    <i class="fa-solid fa-user-plus me-1"></i> إضافة فنان/عازف جديد
                                </button>
                            </div>
                        </div>
                    @endif
                    <div class="mb-3 form-check">
                        <input class="form-check-input" type="checkbox" wire:model="event_is_active" id="eventActive">
                        <label class="form-check-label" for="eventActive">نشطة (ظاهرة للعملاء)</label>
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                        <button type="button" wire:click="$set('isEventModalOpen', false)" class="btn" style="background: var(--surface-hover); color: var(--text-primary); border:1px solid var(--border-color);">إلغاء</button>
                        <button type="submit" class="btn"><i class="fa-solid fa-check me-1"></i> {{ $editingEventId ? 'حفظ التعديلات' : 'إنشاء الحفلة' }}</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ==========================================
         MODAL: تفاصيل الحفلة
    ========================================== --}}
    @if($isEventDetailsModalOpen && $viewingEvent)
        <div style="position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:1050; display:flex; justify-content:center; align-items:center; backdrop-filter: blur(4px);">
            <div class="card p-0" style="background:var(--surface-color); border:1px solid rgba(212, 175, 55, 0.3); width:95%; max-width:650px; max-height:90vh; overflow-y:auto; border-radius:16px; box-shadow: 0 20px 40px rgba(0,0,0,0.6);">
                
                <!-- Header with Image -->
                <div style="position:relative; height: 180px; width: 100%; border-bottom: 2px solid #d4af37;">
                    @if($viewingEvent->artist_image)
                        <img src="{{ asset('storage/' . $viewingEvent->artist_image) }}" style="width:100%; height:100%; object-fit:cover; border-top-left-radius:15px; border-top-right-radius:15px;" alt="Party Image">
                    @else
                        <div style="width:100%; height:100%; background: linear-gradient(135deg, #1a1a2e, #2e1a2e); display:flex; align-items:center; justify-content:center; color:#d4af37; border-top-left-radius:15px; border-top-right-radius:15px;">
                            <i class="fa-solid fa-star fa-3x"></i>
                        </div>
                    @endif
                    <button type="button" style="position:absolute; top:15px; right:15px; font-size:1.3rem; background:rgba(0,0,0,0.5); border:none; color:#fff; width:35px; height:35px; border-radius:50%; cursor:pointer; display:flex; align-items:center; justify-content:center;" wire:click="$set('isEventDetailsModalOpen', false)">&times;</button>
                </div>

                <!-- Event Info -->
                <div class="p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h4 class="fw-bold m-0" style="color: #d4af37;">{{ $viewingEvent->title }}</h4>
                            <div style="font-size: 0.85rem; color: var(--text-secondary); margin-top:5px;">
                                <i class="fa-solid fa-calendar-day me-1 text-primary"></i> {{ $viewingEvent->event_date ?? 'غير محدد' }}
                                @if($viewingEvent->event_time)
                                    <span class="mx-2">|</span> <i class="fa-regular fa-clock me-1 text-info"></i> {{ \Carbon\Carbon::parse($viewingEvent->event_time)->format('h:i A') }}
                                @endif
                            </div>
                        </div>
                        <div>
                            @if($viewingEvent->is_active)
                                <span class="badge" style="background: rgba(76,175,80,0.15); color: #4CAF50; border: 1px solid rgba(76,175,80,0.3); font-size:0.8rem; padding:0.3rem 0.6rem;">نشطة</span>
                            @else
                                <span class="badge" style="background: rgba(158,158,158,0.15); color: #9E9E9E; border: 1px solid rgba(158,158,158,0.3); font-size:0.8rem; padding:0.3rem 0.6rem;">غير نشطة</span>
                            @endif
                        </div>
                    </div>

                    @if($viewingEvent->description)
                        <div class="mb-4 p-3" style="background: rgba(255,255,255,0.03); border-radius:8px; border:1px solid rgba(255,255,255,0.05);">
                            <h6 style="color:var(--text-secondary); font-size:0.8rem;"><i class="fa-solid fa-align-right me-1"></i> وصف الحفلة:</h6>
                            <p class="m-0" style="font-size:0.9rem; line-height:1.6;">{{ $viewingEvent->description }}</p>
                        </div>
                    @endif

                    <!-- Event Bookings / Crew -->
                    <h5 class="fw-bold mb-3" style="color: #f0f0f0; border-bottom:1px solid var(--border-color); padding-bottom:8px;"><i class="fa-solid fa-users me-2 text-primary"></i> الطاقم والحجوزات</h5>
                    
                    @if(count($viewingEventBookings) > 0)
                        <div style="display:flex; flex-direction:column; gap:10px;">
                            @foreach($viewingEventBookings as $bk)
                                <div class="p-3" style="background: linear-gradient(145deg, rgba(33,150,243,0.05), rgba(33,150,243,0.02)); border: 1px solid rgba(33,150,243,0.2); border-radius:10px;">
                                    <div class="row align-items-center">
                                        <div class="col-md-5 mb-2 mb-md-0">
                                            @if($bk->artist)
                                                <div class="d-flex align-items-center mb-1">
                                                    <div style="width:30px; height:30px; border-radius:50%; background:rgba(233,30,99,0.15); color:#E91E63; display:flex; align-items:center; justify-content:center; margin-left:10px;">
                                                        <i class="fa-solid fa-microphone"></i>
                                                    </div>
                                                    <div>
                                                        <div style="font-size:0.75rem; color:var(--text-secondary);">الفنان</div>
                                                        <div class="fw-bold" style="font-size:0.95rem;">{{ $bk->artist->real_name }} {{ $bk->artist->stage_name ? '('.$bk->artist->stage_name.')' : '' }}</div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-md-5">
                                            @if($bk->musician)
                                                    <div style="width:30px; height:30px; border-radius:50%; background:rgba(33,150,243,0.15); color:#2196F3; display:flex; align-items:center; justify-content:center; margin-left:10px;">
                                                        <i class="fa-solid fa-guitar"></i>
                                                    </div>
                                                    <div>
                                                        <div style="font-size:0.75rem; color:var(--text-secondary);">العازف</div>
                                                        <div class="fw-bold" style="font-size:0.95rem;">{{ $bk->musician->real_name }} {{ $bk->musician->instrument ? '('.$bk->musician->instrument.')' : '' }}</div>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-muted" style="font-size:0.8rem;"><i class="fa-solid fa-ban me-1"></i> بدون عازف</span>
                                            @endif
                                        </div>
                                        <div class="col-md-2 text-center">
                                            @if(!$viewingEvent->is_ended)
                                                <button wire:click="deleteBooking({{ $bk->id }})" wire:confirm="هل أنت متأكد من حذف هذا الحجز (الفنان/العازف) من الحفلة؟" class="btn btn-sm" style="background: rgba(244,67,54,0.12); border: 1px solid rgba(244,67,54,0.3); color: #F44336; font-size:0.75rem;" title="حذف هذا الحجز">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                    @if($bk->notes)
                                        <div class="mt-2 pt-2 border-top border-secondary">
                                            <div style="font-size:0.75rem; color:var(--text-secondary);">ملاحظات / البوست:</div>
                                            <div style="font-size:0.85rem;">{{ $bk->notes }}</div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center p-3 text-muted" style="background:rgba(255,255,255,0.02); border-radius:8px;">لا توجد حجوزات مرتبطة بهذه الحفلة.</div>
                    @endif

                    @php
                        $isPastView = false;
                        if($viewingEvent->event_date) {
                            $dtStringView = $viewingEvent->event_date . ' ' . ($viewingEvent->event_time ?? '00:00:00');
                            try {
                                $isPastView = \Carbon\Carbon::parse($dtStringView)->isPast();
                            } catch(\Exception $e) {}
                        }
                        $totalPriceView = $viewingEventBookings->sum('price') + $viewingEventBookings->sum('musician_price');
                    @endphp

                    @if($isPastView && $totalPriceView > 0)
                        <div class="mt-4 p-3 text-center" style="background: rgba(76, 175, 80, 0.1); border: 1px dashed #4CAF50; border-radius: 8px;">
                            <h6 style="color: #4CAF50; margin:0;" class="fw-bold">
                                إجمالي سعر الحجوزات: {{ number_format($totalPriceView, 0) }} ر.ي
                            </h6>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    @endif

    {{-- ==========================================
         MODAL: إنهاء الحفلة
    ========================================== --}}
    @if($isEndEventModalOpen)
        <div style="position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:1050; display:flex; justify-content:center; align-items:center; backdrop-filter: blur(4px);">
            <div class="card p-4 modal-content-responsive" style="background:var(--surface-color); border:1px solid #ff9800; width:95%; max-width:550px; max-height:90vh; overflow-y:auto; border-radius:16px; box-shadow: 0 20px 40px rgba(0,0,0,0.6);">
                <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
                    <h5 class="fw-bold m-0" style="color: #ff9800;">
                        <i class="fa-solid fa-flag-checkered me-2"></i> إنهاء الحفلة
                    </h5>
                    <button type="button" style="font-size:1.3rem; background:none; border:none; color:var(--text-secondary); cursor:pointer;" wire:click="$set('isEndEventModalOpen', false)">&times;</button>
                </div>
                <form wire:submit.prevent="confirmEndEvent">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold" style="color: #4CAF50;">دخل الحفلة الإجمالي (ر.ي)</label>
                            <input type="number" wire:model.live="endingEventIncome" class="form-control" style="font-size:1.2rem; font-weight:bold; text-align:center;" min="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold" style="color: #f44336;">مصروفات أخرى (ر.ي)</label>
                            <input type="number" wire:model.live="endingEventOtherExpenses" class="form-control" style="font-size:1.2rem; font-weight:bold; text-align:center;" min="0">
                        </div>
                        <div class="col-12 mt-2">
                            <small class="text-muted">أدخل إجمالي الدخل وأي مصروفات إضافية (غير أسعار الفنانين).</small>
                        </div>
                    </div>

                    @php
                        $endingBookingsList = \App\Models\ArtistBooking::with(['artist', 'musician'])->where('event_id', $endingEventId)->get();
                    @endphp

                    @if($endingBookingsList->count() > 0)
                        <h6 class="fw-bold mb-3" style="color: #E91E63;"><i class="fa-solid fa-money-bill-wave me-1"></i> عدد الفنانين: {{ $endingBookingsList->count() }} - أسعار الخرج</h6>
                        <div class="p-3 mb-3" style="background: rgba(255,255,255,0.02); border: 1px solid var(--border-color); border-radius: 8px;">
                            @foreach($endingBookingsList as $bk)
                                <div class="mb-3 {{ !$loop->last ? 'border-bottom pb-3' : '' }}">
                                    @if($bk->artist)
                                        <div class="mb-2">
                                            <label class="form-label fw-bold"><span class="badge" style="background:rgba(233,30,99,0.1); color:#E91E63; border:1px solid rgba(233,30,99,0.2);">فنان: {{ $bk->artist->real_name }}</span> سعر الفنان</label>
                                            <input type="number" wire:model.live="endingEventArtistPrices.{{ $bk->id }}" class="form-control" min="0" placeholder="السعر...">
                                        </div>
                                    @endif
                                    @if($bk->musician)
                                        <div class="mb-2">
                                            <label class="form-label fw-bold"><span class="badge" style="background:rgba(33,150,243,0.1); color:#2196F3; border:1px solid rgba(33,150,243,0.2);">عازف: {{ $bk->musician->real_name }}</span> سعر العازف</label>
                                            <input type="number" wire:model.live="endingEventMusicianPrices.{{ $bk->id }}" class="form-control" min="0" placeholder="السعر...">
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @php
                        $otherExp = is_numeric($endingEventOtherExpenses) ? (float)$endingEventOtherExpenses : 0;
                        $totalExpenseEnd = collect($endingEventArtistPrices)->map(fn($v) => is_numeric($v) ? (float)$v : 0)->sum() + collect($endingEventMusicianPrices)->map(fn($v) => is_numeric($v) ? (float)$v : 0)->sum() + $otherExp;
                        $incomeVal = is_numeric($endingEventIncome) ? (float)$endingEventIncome : 0;
                        $remainingEnd = $incomeVal - $totalExpenseEnd;
                    @endphp
                    <div class="p-3 mb-3" style="background: linear-gradient(145deg, rgba(212,175,55,0.08), rgba(212,175,55,0.03)); border: 2px solid rgba(212,175,55,0.3); border-radius: 10px;">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                            <span style="font-weight:700; color: #4CAF50;"><i class="fa-solid fa-sack-dollar me-1"></i> الدخل الكلي:</span>
                            <span style="font-weight:900; font-size:1.1rem; color: #4CAF50;">{{ number_format($incomeVal, 0) }} ر.ي</span>
                        </div>
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                            <span style="font-weight:700; color: #E91E63;"><i class="fa-solid fa-money-bill-trend-up me-1"></i> إجمالي الخرج:</span>
                            <span style="font-weight:900; font-size:1.1rem; color: #E91E63;">{{ number_format($totalExpenseEnd, 0) }} ر.ي</span>
                        </div>
                        <div style="display:flex; justify-content:space-between; align-items:center; border-top: 2px solid rgba(212,175,55,0.3); padding-top: 8px; margin-top:4px;">
                            <span style="font-weight:800; color: #d4af37; font-size: 1.05rem;"><i class="fa-solid fa-vault me-1"></i> المتبقي (الصافي):</span>
                            <span style="font-weight:900; font-size:1.3rem; color: {{ $remainingEnd >= 0 ? '#4CAF50' : '#E91E63' }};">{{ number_format($remainingEnd, 0) }} ر.ي</span>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                        <button type="button" wire:click="$set('isEndEventModalOpen', false)" class="btn" style="background: var(--surface-hover); color: var(--text-primary); border:1px solid var(--border-color);">إلغاء</button>
                        <button type="submit" class="btn" style="background: linear-gradient(135deg, #ff9800, #F57C00); color:white;">
                            <i class="fa-solid fa-check me-1"></i> تأكيد وإنهاء الحفلة
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ==========================================
         MODAL: إضافة فنان/عازف لحفلة موجودة
    ========================================== --}}
    @if($isAddToEventModalOpen)
        <div style="position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:1050; display:flex; justify-content:center; align-items:center; backdrop-filter: blur(4px);">
            <div class="card p-4 modal-content-responsive" style="background:var(--surface-color); border:1px solid rgba(76,175,80,0.3); width:95%; max-width:500px; max-height:90vh; overflow-y:auto; border-radius:16px; box-shadow: 0 20px 40px rgba(0,0,0,0.6);">
                <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
                    <h5 class="fw-bold m-0" style="color: #4CAF50;">
                        <i class="fa-solid fa-user-plus me-2"></i> إضافة فنان/عازف للحفلة
                    </h5>
                    <button type="button" style="font-size:1.3rem; background:none; border:none; color:var(--text-secondary); cursor:pointer;" wire:click="$set('isAddToEventModalOpen', false)">&times;</button>
                </div>
                <form wire:submit.prevent="saveAddToEvent">
                    <div class="mb-3">
                        <label class="form-label fw-bold">اختيار الفنان <span class="text-danger">*</span></label>
                        <select wire:model="addToEvent_artist_id" class="form-control" required>
                            <option value="">-- اضغط لاختيار الفنان --</option>
                            @foreach($artists as $a)
                                <option value="{{ $a->id }}">{{ $a->real_name }} {{ $a->stage_name ? '('.$a->stage_name.')' : '' }}</option>
                            @endforeach
                        </select>
                        @error('addToEvent_artist_id') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">اختيار العازف (اختياري)</label>
                        <select wire:model="addToEvent_musician_id" class="form-control">
                            <option value="">-- اضغط لاختيار العازف --</option>
                            @foreach($musicians as $m)
                                <option value="{{ $m->id }}">{{ $m->real_name }} {{ $m->instrument ? '('.$m->instrument.')' : '' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">السعر</label>
                        <input type="number" wire:model="addToEvent_price" class="form-control" placeholder="0" min="0" step="1">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">ملاحظات</label>
                        <textarea wire:model="addToEvent_notes" class="form-control" rows="2" placeholder="ملاحظات..."></textarea>
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                        <button type="button" wire:click="$set('isAddToEventModalOpen', false)" class="btn" style="background: var(--surface-hover); color: var(--text-primary); border:1px solid var(--border-color);">إلغاء</button>
                        <button type="submit" class="btn btn-success"><i class="fa-solid fa-check me-1"></i> إضافة</button>
                    </div>
                </form>
            </div>
        </div>
    @endif


    {{-- ==========================================
         MODAL: Post Share
    ========================================== --}}
    @if($isPostShareModalOpen && $postShareEvent)
        @php
            $shareImageUrl = $postShareEvent->artist_image ? asset('storage/' . $postShareEvent->artist_image) : '';
            $rawShareText = $postShareEvent->title . ($postShareEvent->event_date ? ' - ' . \Carbon\Carbon::parse($postShareEvent->event_date)->format('Y-m-d') : '');
            $rawShareUrl = url('/');
            
            $shareText = urlencode($rawShareText);
            $shareUrl = urlencode($rawShareUrl);
            
            $facebookUrl = 'https://www.facebook.com/sharer/sharer.php?u=' . $shareUrl . '&quote=' . $shareText;
            $twitterUrl = 'https://twitter.com/intent/tweet?text=' . $shareText . '&url=' . $shareUrl;
        @endphp
        <div style="position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.75); z-index:1050; display:flex; justify-content:center; align-items:center; backdrop-filter: blur(6px);">
            <div class="card p-4 text-center" style="background:var(--surface-color); border:1px solid rgba(156,39,176,0.35); width:95%; max-width:520px; max-height:90vh; overflow-y:auto; border-radius:20px; box-shadow: 0 24px 60px rgba(0,0,0,0.7);">
                <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
                    <h5 class="fw-bold m-0" style="color: #9C27B0;">
                        <i class="fa-solid fa-share-nodes me-2"></i> مشاركة البوست
                    </h5>
                    <button type="button" style="font-size:1.3rem; background:none; border:none; color:var(--text-secondary); cursor:pointer;" wire:click="$set('isPostShareModalOpen', false)">&times;</button>
                </div>

                {{-- صورة البوست --}}
                @if($postShareEvent->artist_image)
                    <div style="position:relative; margin-bottom:16px;">
                        <img src="{{ asset('storage/' . $postShareEvent->artist_image) }}" style="width:100%; max-height:280px; object-fit:contain; border-radius:12px; border:1px solid rgba(156,39,176,0.25);">
                    </div>
                @else
                    <div style="width:100%; height:160px; background:rgba(156,39,176,0.05); border-radius:12px; display:flex; flex-direction:column; align-items:center; justify-content:center; color:#9C27B0; margin-bottom:16px; border:1px dashed rgba(156,39,176,0.3);">
                        <i class="fa-solid fa-image fa-2x mb-2"></i>
                        <small style="color:var(--text-secondary)">لا توجد صورة بعد — ارفع صورة أولاً</small>
                    </div>
                @endif

                <h6 class="fw-bold mb-1" style="font-size:1rem;">{{ $postShareEvent->title }}</h6>
                @if($postShareEvent->event_date)
                    <p style="color:var(--text-secondary); font-size:0.82rem; margin-bottom:20px;">
                        <i class="fa-solid fa-calendar-day me-1" style="color:#9C27B0;"></i>
                        {{ \Carbon\Carbon::parse($postShareEvent->event_date)->format('Y-m-d') }}
                    </p>
                @endif

                {{-- أزرار المشاركة --}}
                <p style="font-size:0.8rem; color:var(--text-secondary); margin-bottom:12px;">اختر منصة المشاركة:</p>
                <div style="display:flex; justify-content:center; gap:10px; flex-wrap:wrap; margin-bottom:20px;">
                    <button type="button" onclick="shareImageToWhatsApp('{{ $shareImageUrl }}', '{{ addslashes($rawShareText) }}', '{{ addslashes($rawShareUrl) }}')" class="btn btn-sm" style="background:#25D366; color:white; border-radius:10px; padding:9px 18px; font-size:0.9rem; border:none;">
                        <i class="fa-brands fa-whatsapp me-1"></i> واتساب
                    </button>
                    <a href="{{ $facebookUrl }}" target="_blank" class="btn btn-sm" style="background:#1877F2; color:white; border-radius:10px; padding:9px 18px; font-size:0.9rem; text-decoration:none;">
                        <i class="fa-brands fa-facebook-f me-1"></i> فيسبوك
                    </a>
                    <a href="{{ $twitterUrl }}" target="_blank" class="btn btn-sm" style="background:#1DA1F2; color:white; border-radius:10px; padding:9px 18px; font-size:0.9rem; text-decoration:none;">
                        <i class="fa-brands fa-twitter me-1"></i> تويتر
                    </a>
                    <a href="https://www.instagram.com/" target="_blank" class="btn btn-sm" style="background:linear-gradient(45deg,#f09433,#e6683c,#dc2743,#cc2366,#bc1888); color:white; border-radius:10px; padding:9px 18px; font-size:0.9rem; text-decoration:none;">
                        <i class="fa-brands fa-instagram me-1"></i> انستجرام
                    </a>
                </div>

                {{-- رفع صورة البوست من هنا --}}
                <div style="border-top: 1px solid var(--border-color); padding-top:16px; margin-top:4px;">
                    <p style="font-size:0.8rem; color:var(--text-secondary); margin-bottom:10px;">أو ارفع صورة جديدة للبوست:</p>
                    <button wire:click="openUploadPostImageModal({{ $postShareEvent->id }})" class="btn" style="background: rgba(156,39,176,0.12); border: 1px solid rgba(156,39,176,0.4); color: #9C27B0; border-radius:10px; padding:8px 20px;">
                        <i class="fa-solid fa-upload me-2"></i> رفع صورة البوست
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ==========================================
         MODAL: Upload Post Image
    ========================================== --}}
    @if($isUploadPostImageModalOpen)
        <div style="position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); z-index:1060; display:flex; justify-content:center; align-items:center; backdrop-filter: blur(6px);">
            <div class="card p-4" style="background:var(--surface-color); border:1px solid rgba(156,39,176,0.35); width:95%; max-width:480px; border-radius:20px; box-shadow: 0 24px 60px rgba(0,0,0,0.7);">
                <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
                    <h5 class="fw-bold m-0" style="color: #9C27B0;">
                        <i class="fa-solid fa-cloud-arrow-up me-2"></i> رفع صورة البوست
                    </h5>
                    <button type="button" style="font-size:1.3rem; background:none; border:none; color:var(--text-secondary); cursor:pointer;" wire:click="$set('isUploadPostImageModalOpen', false)">&times;</button>
                </div>

                <form wire:submit.prevent="savePostImage">
                    <div class="mb-4">
                        <label class="form-label fw-bold">اختر الصورة <span class="text-danger">*</span></label>
                        <input type="file" wire:model="postUploadImage" class="form-control" accept="image/*">
                        @error('postUploadImage') <span class="text-danger small">{{ $message }}</span> @enderror
                        <small style="color:var(--text-secondary); font-size:0.75rem;">الحجم الأقصى: 5MB</small>
                    </div>

                    {{-- Preview --}}
                    <div wire:loading wire:target="postUploadImage" class="text-center mb-3" style="color:var(--text-secondary); font-size:0.85rem;">
                        <i class="fa-solid fa-spinner fa-spin me-1"></i> جاري التحميل...
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                        <button type="button" wire:click="$set('isUploadPostImageModalOpen', false)" class="btn" style="background: var(--surface-hover); color: var(--text-primary); border:1px solid var(--border-color);">إلغاء</button>
                        <button type="submit" class="btn" style="background: linear-gradient(135deg, #9C27B0, #6A1B9A); color:white;" wire:loading.attr="disabled" wire:target="savePostImage">
                            <span wire:loading.remove wire:target="savePostImage"><i class="fa-solid fa-check me-1"></i> حفظ الصورة</span>
                            <span wire:loading wire:target="savePostImage"><i class="fa-solid fa-spinner fa-spin me-1"></i> جاري الحفظ...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ==========================================
         MODAL: Add Full Month of Available Days
    ========================================== --}}
    @if($isAddMonthModalOpen)
        <div style="position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); z-index:1060; display:flex; justify-content:center; align-items:center; backdrop-filter: blur(6px);">
            <div class="card p-4" style="background:var(--surface-color); border:1px solid rgba(255,152,0,0.35); width:95%; max-width:460px; border-radius:20px; box-shadow: 0 24px 60px rgba(0,0,0,0.7);">
                <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
                    <h5 class="fw-bold m-0" style="color: #ff9800;">
                        <i class="fa-solid fa-calendar-plus me-2"></i> إضافة شهر كامل (أيام متاحة)
                    </h5>
                    <button type="button" style="font-size:1.3rem; background:none; border:none; color:var(--text-secondary); cursor:pointer;" wire:click="$set('isAddMonthModalOpen', false)">&times;</button>
                </div>

                <form wire:submit.prevent="saveAddMonth">
                    <div class="row mb-3">
                        <div class="col-7">
                            <label class="form-label fw-bold">الشهر <span class="text-danger">*</span></label>
                            <select wire:model="add_month_number" class="form-control" required>
                                <option value="1">يناير (1)</option>
                                <option value="2">فبراير (2)</option>
                                <option value="3">مارس (3)</option>
                                <option value="4">أبريل (4)</option>
                                <option value="5">مايو (5)</option>
                                <option value="6">يونيو (6)</option>
                                <option value="7">يوليو (7)</option>
                                <option value="8">أغسطس (8)</option>
                                <option value="9">سبتمبر (9)</option>
                                <option value="10">أكتوبر (10)</option>
                                <option value="11">نوفمبر (11)</option>
                                <option value="12">ديسمبر (12)</option>
                            </select>
                            @error('add_month_number') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-5">
                            <label class="form-label fw-bold">السنة <span class="text-danger">*</span></label>
                            <input type="number" wire:model="add_month_year" class="form-control" min="2020" max="2040" required>
                            @error('add_month_year') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">العنوان الافتراضي للأيام <span class="text-danger">*</span></label>
                        <input type="text" wire:model="add_month_title" class="form-control" placeholder="مثال: حفلة متاحة" required>
                        @error('add_month_title') <span class="text-danger small">{{ $message }}</span> @enderror
                        <small style="color:var(--text-secondary); font-size:0.75rem;">
                            سيتم إنشاء بطاقة «متاح» خضراء لكل يوم في الشهر (مع التاج الذهبي لأيام الخميس والجمعة تلقائياً)، ولن تتأثر الأيام الموجودة مسبقاً.
                        </small>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                        <button type="button" wire:click="$set('isAddMonthModalOpen', false)" class="btn" style="background: var(--surface-hover); color: var(--text-primary); border:1px solid var(--border-color);">إلغاء</button>
                        <button type="submit" class="btn" style="background: linear-gradient(135deg, #ff9800, #f57c00); color:white; border:none;" wire:loading.attr="disabled" wire:target="saveAddMonth">
                            <span wire:loading.remove wire:target="saveAddMonth"><i class="fa-solid fa-check me-1"></i> إنشاء أيام الشهر</span>
                            <span wire:loading wire:target="saveAddMonth"><i class="fa-solid fa-spinner fa-spin me-1"></i> جاري الإنشاء...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('notify', (event) => {
                if (typeof showToast === 'function') {
                    showToast(event[0].message, event[0].type);
                } else {
                    alert(event[0].message);
                }
            });
        });

        window.shareImageToWhatsApp = async function(imageUrl, title, url) {
            const text = title;
            
            if (!imageUrl) {
                window.open('https://wa.me/?text=' + encodeURIComponent(text), '_blank');
                return;
            }

            try {
                const response = await fetch(imageUrl);
                const blob = await response.blob();
                const file = new File([blob], "share.jpg", { type: blob.type || "image/jpeg" });
                
                // Mobile: Web Share API
                if (navigator.canShare && navigator.canShare({ files: [file] })) {
                    await navigator.share({
                        title: title,
                        text: text,
                        files: [file]
                    });
                    return;
                }
                
                // Desktop: Clipboard API
                if (navigator.clipboard && window.ClipboardItem) {
                    await navigator.clipboard.write([
                        new ClipboardItem({
                            [blob.type]: blob
                        })
                    ]);
                    alert('تم نسخ الصورة إلى الحافظة. سيتم فتح واتساب، يمكنك لصق الصورة هناك وإرسالها.');
                }
            } catch (e) {
                console.error('Sharing failed', e);
            }
            
            // Open WhatsApp with text
            window.open('https://wa.me/?text=' + encodeURIComponent(text), '_blank');
        };
    </script>

</div>

