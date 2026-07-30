@section('title', 'รายการรถทั้งหมด')
@section('description', 'ID Drives')
@extends('layout.app')
@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <style>
        .kpi-section-label {
            font-size: 18px;
            font-weight: 600;
            color: #444a52;
            letter-spacing: .4px;
            margin-bottom: 8px;
            margin-top: 0px;
        }

        .kpi-card {
            background: #fff;
            border: 0.5px solid #e3e6ef;
            border-radius: 10px;
            padding: 14px 16px;
            cursor: pointer;
            border-top: 3px solid transparent;
            transition: border-color .15s, box-shadow .15s;
        }

        .kpi-card:hover {
            box-shadow: 0 2px 8px rgba(88,64,255,.08);
        }

        .kpi-card.active {
            border-top-color: #5840ff;
            background: #f8f7ff;
        }

        .kpi-card.active-green  { border-top-color: #22c55e; background: #f0fdf4; }
        .kpi-card.active-yellow { border-top-color: #f59e0b; background: #fffbeb; }
        .kpi-card.active-red    { border-top-color: #ef4444; background: #fef2f2; }
        .kpi-card.active-gray   { border-top-color: #94a3b8; background: #f8fafc; }

        .kpi-label {
            font-size: 14px;
            color: #5c5c5c;
            margin-bottom: 4px;
        }

        .kpi-num {
            font-size: 28px;
            font-weight: 600;
            color: #404659;
            line-height: 1;
        }

        .kpi-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 4px;
            vertical-align: middle;
        }

        .filter-active-tag {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 12px;
            background: #eeedfe;
            color: #3c3489;
            margin-right: 6px;
            margin-bottom: 8px;
        }

        .filter-active-tag .remove {
            cursor: pointer;
            font-size: 11px;
            opacity: .7;
        }

        .filter-active-tag .remove:hover { opacity: 1; }
    </style>
@endpush

@section('content')
    <div class="container-fluid">

        {{-- Breadcrumb --}}
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb-main">
                    <span class="fs-24 fw-bold breadcrumb-title">รายการรถ</span>
                    <div class="breadcrumb-action justify-content-center flex-wrap">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
                                <li class="breadcrumb-item active">รายการรถ</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================ --}}
        {{-- KPI ROW 1: สถานะรถ                          --}}
        {{-- ============================================ --}}
        <div class="kpi-section-label">
            <i class="uil uil-truck"></i> สถานะรถ
        </div>
        <div class="row g-2 mb-2">
            <div class="col-6 col-md-3">
                <div class="kpi-card active" id="kpi-status-all"
                     onclick="setStatusFilter('all', this)">
                    <div class="kpi-label">
                        <i class="uil uil-truck"></i> รถทั้งหมด
                    </div>
                    <div class="kpi-num">{{ number_format($kpiStatus->total) }}</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="kpi-card" id="kpi-status-1"
                     onclick="setStatusFilter('1', this, 'active-green')">
                    <div class="kpi-label">
                        <span class="kpi-dot" style="background:#22c55e"></span> เปิดการใช้
                    </div>
                    <div class="kpi-num" style="color:#16a34a">{{ number_format($kpiStatus->active) }}</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="kpi-card" id="kpi-status-0"
                     onclick="setStatusFilter('0', this, 'active-yellow')">
                    <div class="kpi-label">
                        <span class="kpi-dot" style="background:#f59e0b"></span> ปิดการใช้งาน
                    </div>
                    <div class="kpi-num" style="color:#d97706">{{ number_format($kpiStatus->inactive) }}</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="kpi-card" id="kpi-status-2"
                     onclick="setStatusFilter('2', this, 'active-red')">
                    <div class="kpi-label">
                        <span class="kpi-dot" style="background:#ef4444"></span> ห้ามใช้งาน
                    </div>
                    <div class="kpi-num" style="color:#dc2626">{{ number_format($kpiStatus->banned) }}</div>
                </div>
            </div>
        </div>

        {{-- ============================================ --}}
        {{-- KPI ROW 2: ผลการตรวจ                        --}}
        {{-- ============================================ --}}
        <div class="kpi-section-label mt-2">
            <i class="uil uil-clipboard-alt"></i> ผลการตรวจล่าสุด
        </div>
        <div class="row g-2 mb-3">
            <div class="col-6 col-md-3">
                <div class="kpi-card" id="kpi-insp-1"
                     onclick="setInspFilter('1', this, 'active-green')">
                    <div class="kpi-label">
                        <span class="kpi-dot" style="background:#22c55e"></span> ปกติ
                    </div>
                    <div class="kpi-num" style="color:#16a34a">{{ number_format($kpiInspect->passed) }}</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="kpi-card" id="kpi-insp-2"
                     onclick="setInspFilter('2', this, 'active-yellow')">
                    <div class="kpi-label">
                        <span class="kpi-dot" style="background:#f59e0b"></span> ไม่ปกติ แต่ใช้งานได้
                    </div>
                    <div class="kpi-num" style="color:#d97706">{{ number_format($kpiInspect->warning) }}</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="kpi-card" id="kpi-insp-3"
                     onclick="setInspFilter('3', this, 'active-red')">
                    <div class="kpi-label">
                        <span class="kpi-dot" style="background:#ef4444"></span> ไม่ปกติ ห้ามใช้งาน
                    </div>
                    <div class="kpi-num" style="color:#dc2626">{{ number_format($kpiInspect->failed) }}</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="kpi-card" id="kpi-insp-0"
                     onclick="setInspFilter('0', this, 'active-gray')">
                    <div class="kpi-label">
                        <span class="kpi-dot" style="background:#94a3b8"></span> ไม่พบข้อมูล
                    </div>
                    <div class="kpi-num" style="color:#64748b">{{ number_format($kpiInspect->not_inspected) }}</div>
                </div>
            </div>
        </div>

        {{-- ============================================ --}}
        {{-- TABLE CARD                                    --}}
        {{-- ============================================ --}}
        <div class="card shadow-sm mb-25">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h6 class="mb-0"><i class="uil uil-list-ul"></i> รายการรถทั้งหมด</h6>
                <a href="{{ route('staff.vehicles.create') }}" class="btn btn-primary btn-sm">
                    <i class="uil uil-plus"></i> เพิ่มรถใหม่
                </a>
            </div>

            <div class="card-body">

                {{-- Active filter tags --}}
                <div id="activeFilterTags" class="mb-2" style="min-height:28px"></div>

                {{-- Filter bar --}}
                <div class="row g-2 mb-3">
                    <div class="col-md-5">
                        <input type="text" id="searchInput" class="form-control form-control-sm"
                               placeholder="ค้นหาทะเบียน, ประเภท, บริษัท, Supply...">
                    </div>
                    <div class="col-md-3">
                        <select id="filterCompany" class="form-select form-select-sm">
                            <option value="">บริษัททั้งหมด</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->company_id }}">{{ $company->company_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select id="filterType" class="form-select form-select-sm">
                            <option value="">ประเภทรถทั้งหมด</option>
                            @foreach($vehicleTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->vehicle_type }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-1">
                        <button class="btn btn-outline-secondary btn-sm w-100" onclick="resetAllFilters()" title="ล้าง filter ทั้งหมด">
                            <i class="uil uil-redo"></i>
                        </button>
                    </div>
                </div>

                {{-- DataTable --}}
                <div class="table-responsive">
                    <table id="vehicleTable" class="table table-bordered table-hover mb-0" style="width:100%">
                        <thead class="table-light">
                            <tr>
                                <th>ทะเบียน</th>
                                <th>ประเภทรถ</th>
                                <th>บริษัท</th>
                                <th>Supply</th>
                                <th>วันตรวจล่าสุด</th>
                                <th>ผลการตรวจ</th>
                                <th>สถานะรถ</th>
                                <th>จัดการ</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
    $(function () {

        // ============================================
        // State: active KPI filters
        // ============================================
        let activeStatusFilter = 'all';
        let activeInspFilter   = 'all';

        // ============================================
        // DataTable init
        // ============================================
        const table = $('#vehicleTable').DataTable({
            processing : true,
            serverSide : true,
            ajax: {
                url  : "{{ route('staff.vehicles.ajax.index') }}",
                type : 'GET',
                data : function (d) {
                    d.filter_status  = activeStatusFilter;
                    d.filter_inspect = activeInspFilter;
                    d.filter_company = $('#filterCompany').val();
                    d.filter_type    = $('#filterType').val();
                }
            },
            columns: [
                { data: 0, title: 'ทะเบียน' },
                { data: 1, title: 'ประเภทรถ' },
                { data: 2, title: 'บริษัท' },
                { data: 3, title: 'Supply' },
                { data: 4, title: 'วันตรวจล่าสุด', orderable: true },
                { data: 5, title: 'ผลการตรวจ',    orderable: false },
                { data: 6, title: 'สถานะรถ',       orderable: false },
                { data: 7, title: 'จัดการ',         orderable: false, searchable: false },
            ],
            order    : [[4, 'desc']],
            pageLength: 25,
            language : {
                search      : '',
                searchPlaceholder: 'ค้นหา...',
                lengthMenu  : 'แสดง _MENU_ รายการ',
                info        : 'แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ คัน',
                infoFiltered: '(กรองจาก _MAX_ รายการ)',
                paginate    : { next: 'ถัดไป', previous: 'ก่อนหน้า' },
                emptyTable  : 'ไม่พบข้อมูลรถ',
                zeroRecords : 'ไม่พบรายการที่ค้นหา',
                processing  : '<div class="spinner-border spinner-border-sm text-primary"></div> กำลังโหลด...',
            },
            // Disable DataTable's built-in search box (ใช้ custom แทน)
            dom: '<"d-flex justify-content-between align-items-center mb-2"li>rt<"d-flex justify-content-between align-items-center mt-2"ip>',
        });

        // ============================================
        // Custom search box → DataTable search
        // ============================================
        let searchTimer;
        $('#searchInput').on('input', function () {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => {
                table.search($(this).val()).draw();
            }, 400);
        });

        // ============================================
        // Filter bar dropdowns → reload DataTable
        // ============================================
        $('#filterCompany, #filterType').on('change', function () {
            table.ajax.reload();
            renderFilterTags();
        });

        // ============================================
        // KPI click: สถานะรถ
        // ============================================
        window.setStatusFilter = function (val, el, activeClass) {
            activeStatusFilter = val;
            // Reset all status KPI cards
            document.querySelectorAll('[id^="kpi-status-"]').forEach(k => {
                k.className = 'kpi-card';
            });
            el.classList.add(activeClass ?? 'active');
            table.ajax.reload();
            renderFilterTags();
        };

        // ============================================
        // KPI click: ผลการตรวจ
        // ============================================
        window.setInspFilter = function (val, el, activeClass) {
            // Toggle: คลิก card เดิม = ยกเลิก filter
            if (activeInspFilter === val) {
                activeInspFilter = 'all';
                el.className = 'kpi-card';
            } else {
                activeInspFilter = val;
                document.querySelectorAll('[id^="kpi-insp-"]').forEach(k => {
                    k.className = 'kpi-card';
                });
                el.classList.add(activeClass);
            }
            table.ajax.reload();
            renderFilterTags();
        };

        // ============================================
        // Reset all filters
        // ============================================
        window.resetAllFilters = function () {
            activeStatusFilter = 'all';
            activeInspFilter   = 'all';
            $('#searchInput').val('');
            $('#filterCompany').val('');
            $('#filterType').val('');
            // Reset KPI cards
            document.querySelectorAll('[id^="kpi-status-"]').forEach(k => k.className = 'kpi-card');
            document.getElementById('kpi-status-all').classList.add('active');
            document.querySelectorAll('[id^="kpi-insp-"]').forEach(k => k.className = 'kpi-card');
            table.search('').ajax.reload();
            renderFilterTags();
        };

        // ============================================
        // Render active filter tags
        // ============================================
        const statusLabels = { '1':'ใช้งานได้', '0':'ปิดการใช้งาน', '2':'ห้ามใช้งาน' };
        const inspLabels   = { '1':'ผ่าน', '2':'ไม่ผ่าน (ใช้ได้)', '3':'ไม่ผ่าน', '0':'ยังไม่ตรวจ' };

        function renderFilterTags() {
            let html = '';
            if (activeStatusFilter !== 'all') {
                html += `<span class="filter-active-tag">
                    <i class="uil uil-truck"></i> ${statusLabels[activeStatusFilter]}
                    <span class="remove" onclick="resetStatusFilter()">✕</span>
                </span>`;
            }
            if (activeInspFilter !== 'all') {
                html += `<span class="filter-active-tag">
                    <i class="uil uil-clipboard-alt"></i> ${inspLabels[activeInspFilter]}
                    <span class="remove" onclick="resetInspFilter()">✕</span>
                </span>`;
            }
            const co = $('#filterCompany option:selected').text();
            if ($('#filterCompany').val()) {
                html += `<span class="filter-active-tag">
                    <i class="uil uil-building"></i> ${co}
                    <span class="remove" onclick="$('#filterCompany').val('').trigger('change')">✕</span>
                </span>`;
            }
            const ty = $('#filterType option:selected').text();
            if ($('#filterType').val()) {
                html += `<span class="filter-active-tag">
                    <i class="uil uil-car-sideview"></i> ${ty}
                    <span class="remove" onclick="$('#filterType').val('').trigger('change')">✕</span>
                </span>`;
            }
            $('#activeFilterTags').html(html);
        }

        window.resetStatusFilter = function () {
            activeStatusFilter = 'all';
            document.querySelectorAll('[id^="kpi-status-"]').forEach(k => k.className = 'kpi-card');
            document.getElementById('kpi-status-all').classList.add('active');
            table.ajax.reload();
            renderFilterTags();
        };

        window.resetInspFilter = function () {
            activeInspFilter = 'all';
            document.querySelectorAll('[id^="kpi-insp-"]').forEach(k => k.className = 'kpi-card');
            table.ajax.reload();
            renderFilterTags();
        };

    });
    </script>
@endpush