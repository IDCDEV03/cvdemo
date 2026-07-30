@section('title', 'รายการรถทั้งหมด')
@section('description', 'ID Drives')
@extends('layout.app')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
    <style>
        .search-card {
            background: #fff;
            border: 0.5px solid #dde0f0;
            border-radius: 14px;
            overflow: hidden;
            margin-bottom: 18px;
            box-shadow: 0 2px 10px rgba(88,64,255,.06);
        }

        .search-card-header {
            background: linear-gradient(180deg, #5840ff 0%, #7c66ff 100%);
            padding: 12px 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .search-card-header span {
            font-size: 15px;
            font-weight: 600;
            color: #fff;
            letter-spacing: .3px;
        }

        .search-card-header i {
            font-size: 18px;
            color: rgba(255,255,255,.85);
        }

        .search-card-body {
            padding: 18px 20px 16px;
        }

        .search-label {
            font-size: 16px;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: .5px;
            margin-bottom: 5px;
        }

        .search-divider {
            border-top: 1px solid #f1f3f9;
            margin: 14px 0 14px;
        }

        .btn-search {
            background: linear-gradient(135deg, #5840ff, #7c66ff);
            border: none;
            color: #fff;
            padding: 7px 22px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            letter-spacing: .3px;
            transition: opacity .15s;
        }

        .btn-search:hover { opacity: .88; color: #fff; }

        .btn-reset {
            background: #f1f3f9;
            border: none;
            color: #64748b;
            padding: 7px 16px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            transition: background .15s;
        }

        .btn-reset:hover { background: #e2e6f0; }

        /* Empty state before search */
        .pre-search-empty {
            text-align: center;
            padding: 50px 0;
            color: #94a3b8;
        }

        .pre-search-empty i {
            font-size: 48px;
            display: block;
            margin-bottom: 10px;
            opacity: .35;
        }

        .pre-search-empty p {
            font-size: 16px;
            margin: 0;
        }

        /* Select2 size fix */
        .select2-container--bootstrap-5 .select2-selection {
            font-size: 13px;
        }

        /* ===================== MODERN TABLE ===================== */
        #vehicleTable {
            border-collapse: separate;
            border-spacing: 0;
        }

        #vehicleTable thead th {
            background: #f4f3ff;
            color: #5840ff;
            font-weight: 700;
            font-size: 16px;
            letter-spacing: .3px;
            text-transform: uppercase;
            padding: 12px 16px;
            border-top: none;
            border-bottom: 2px solid #e4e1ff;
            white-space: nowrap;
        }

        #vehicleTable tbody tr {
            transition: background .12s;
        }

        #vehicleTable tbody tr:hover td {
            background: #f8f7ff;
        }

        #vehicleTable tbody td {
            padding: 12px 16px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f3f9;
            border-left: none;
            border-right: none;
            border-top: none;
            font-size: 14px;
            color: #404659;
        }

        .plate-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: #eeedfe;
            color: #3c3489;
            font-weight: 700;
            font-size: 15px;
            letter-spacing: 1.5px;
            padding: 5px 14px;
            border-radius: 8px;
            border: 1.5px solid #d4d0ff;
        }

        .type-pill {
            display: inline-block;
            background: #f0fdf4;
            color: #15803d;
            font-size: 13px;
            font-weight: 500;
            padding: 3px 11px;
            border-radius: 20px;
            border: 1px solid #bbf7d0;
        }

        .date-cell {
            font-size: 16px;
            color: #64748b;
        }

        /* Pagination modern */
        .dataTables_paginate .paginate_button {
            border-radius: 6px !important;
            padding: 4px 10px !important;
            font-size: 13px !important;
        }

        .dataTables_paginate .paginate_button.current {
            background: #5840ff !important;
            border-color: #5840ff !important;
            color: #fff !important;
        }

        .dataTables_info, .dataTables_length {
            font-size: 13px;
            color: #64748b;
        }
    </style>
@endpush

@section('content')
    @php $userRole = Auth::user()->role->value; @endphp
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
        {{-- SUMMARY CARD                                  --}}
        {{-- ============================================ --}}
        @php
            $total  = $summary->total_inspected ?? 0;
            $passed = $summary->passed ?? 0;
            $failed = $summary->failed ?? 0;
        @endphp
        <div style="background:#fff; border:0.5px solid #dde0f0; border-radius:14px;
                    overflow:hidden; margin-bottom:18px; box-shadow:0 2px 10px rgba(88,64,255,.06);">

            {{-- Header --}}
            <div style="background:linear-gradient(90deg,#1e1b4b 0%,#3730a3 60%,#4f46e5 100%);
                        padding:13px 22px; display:flex; align-items:center; justify-content:space-between;">
                <div style="display:flex; align-items:center; gap:10px;">
                    <i class="uil uil-chart-bar" style="font-size:20px; color:rgba(255,255,255,.8);"></i>
                    <span style="font-size:16px; font-weight:700; color:#fff; letter-spacing:.3px;">
                        สรุปผลการตรวจ
                    </span>
                </div>
                <span style="background:rgba(255,255,255,.15); color:#fff; font-size:16px; font-weight:600;
                             padding:3px 12px; border-radius:20px; letter-spacing:.5px;">
                    ปี พ.ศ. {{ $currentYearBE }}
                </span>
            </div>

            {{-- Stats --}}
            <div style="padding:18px 22px 16px;">
                <div class="row g-3">

                    {{-- รถที่ตรวจแล้ว --}}
                    <div class="col-6 col-md-4">
                        <div style="background:#f5f3ff; border-radius:12px; padding:16px 18px;
                                    border-left:4px solid #6d28d9;">
                            <div style="font-size:16px; font-weight:600; color:#6d28d9;
                                        text-transform:uppercase; letter-spacing:.5px; margin-bottom:6px;">
                                <i class="uil uil-car"></i> รถที่ตรวจแล้ว
                            </div>
                            <div style="font-size:28px; font-weight:800; color:#3b0764; line-height:1;">
                                {{ number_format($total) }}
                            </div>
                           
                        </div>
                    </div>

                    {{-- ผ่าน --}}
                    <div class="col-6 col-md-4">
                        <div style="background:#f0fdf4; border-radius:12px; padding:16px 18px;
                                    border-left:4px solid #16a34a;">
                            <div style="font-size:16px; font-weight:600; color:#16a34a;
                                        text-transform:uppercase; letter-spacing:.5px; margin-bottom:6px;">
                                <i class="uil uil-check-circle"></i> ผ่าน
                            </div>
                            <div style="font-size:28px; font-weight:800; color:#14532d; line-height:1;">
                                {{ number_format($passed) }}
                            </div>                       
                        </div>
                    </div>

                    {{-- ไม่ผ่าน --}}
                    <div class="col-6 col-md-4">
                        <div style="background:#fff1f2; border-radius:12px; padding:16px 18px;
                                    border-left:4px solid #dc2626;">
                            <div style="font-size:16px; font-weight:600; color:#dc2626;
                                        text-transform:uppercase; letter-spacing:.5px; margin-bottom:6px;">
                                <i class="uil uil-times-circle"></i> ไม่ผ่าน
                            </div>
                            <div style="font-size:28px; font-weight:800; color:#7f1d1d; line-height:1;">
                                {{ number_format($failed) }}
                            </div>
                        </div>
                    </div>

                </div>


            </div>
        </div>

        {{-- ============================================ --}}
        {{-- SEARCH CARD                                   --}}
        {{-- ============================================ --}}
        <div class="search-card">

            <div class="search-card-header">
                <i class="uil uil-filter"></i>
                <span>ค้นหาและกรองข้อมูลรถ</span>
            </div>

            <div class="search-card-body">
                <div class="row g-3">

                    {{-- ทะเบียนรถ: ทุก role --}}
                    <div class="col-md-2">
                        <div class="search-label">
                            <i class="uil uil-search" style="color:#5840ff;"></i> ทะเบียนรถ
                        </div>
                        <input type="text" id="searchPlate" class="form-control"
                               placeholder="พิมพ์ทะเบียน..." autocomplete="off"
                               style="border-color:#c7caff; background:#fafaff;">
                    </div>

                    @if ($showCompanyFilter)
                        {{-- staff/admin/manager: text search --}}
                        <div class="col-md-2">
                            <div class="search-label">ค้นหา</div>
                            <input type="text" id="searchInput" class="form-control"
                                   placeholder="ทะเบียน หรือ Supply...">
                        </div>
                        {{-- staff/admin/manager: บริษัท --}}
                        <div class="col-md-3">
                            <div class="search-label">บริษัท</div>
                            <select id="filterCompany" class="form-select s2">
                                <option value="">ทั้งหมด</option>
                                @foreach ($companies as $company)
                                    <option value="{{ $company->company_id }}">{{ $company->company_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    @if ($showSupplyFilter)
                        <div class="col-md-3">
                            <div class="search-label">Supply</div>
                            <select id="filterSupply" class="form-select s2">
                                <option value="">ทั้งหมด</option>
                                @foreach ($supplies as $sup)
                                    <option value="{{ $sup->sup_id }}">{{ $sup->supply_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    {{-- ประเภทรถ: ทุก role --}}
                    <div class="col-md-2">
                        <div class="search-label">ประเภทรถ</div>
                        <select id="filterType" class="form-select s2">
                            <option value="">ทั้งหมด</option>
                            @foreach ($vehicleTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->vehicle_type }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- ผลการตรวจ: ทุก role --}}
                    <div class="col-md-2">
                        <div class="search-label">ผลการตรวจ</div>
                        <select id="filterInspect" class="form-select s2">
                            <option value="">ทั้งหมด</option>
                            <option value="1">ผ่าน</option>
                            <option value="2">ไม่ปกติ แต่ใช้งานได้</option>
                            <option value="3">ไม่ปกติ ห้ามใช้งาน</option>
                            <option value="0">ไม่พบข้อมูล</option>
                        </select>
                    </div>

                </div>

                <div class="search-divider"></div>

                <div class="d-flex justify-content-end gap-2">
                    <button class="btn-reset" onclick="resetSearch()">
                        <i class="uil uil-redo"></i> ล้าง
                    </button>
                    <button class="btn-search" onclick="doSearch()">
                        <i class="uil uil-search"></i> ค้นหา
                    </button>
                </div>
            </div>

        </div>

        {{-- ============================================ --}}
        {{-- TABLE CARD                                    --}}
        {{-- ============================================ --}}
        <div class="card shadow-sm mb-25">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <span class="fs-18 fw-bold mb-0"><i class="uil uil-list-ul"></i> รายการรถ</span>
                @if ($userRole !== 'company')
                    <a href="{{ route('vehicles.create') }}" class="btn btn-primary btn-sm">
                        <i class="uil uil-plus"></i> เพิ่มรถใหม่
                    </a>
                @endif
            </div>
            <div class="card-body p-0">

                {{-- Pre-search placeholder --}}
                <div id="preSearchMsg" class="pre-search-empty">
                    <i class="uil uil-search-alt"></i>
                    <p>กรุณากรอกเงื่อนไขและกด <strong>ค้นหา</strong> เพื่อแสดงรายการรถ</p>
                </div>

                {{-- DataTable (hidden until first search) --}}
                <div id="tableWrapper" class="d-none p-3">
                    <div class="table-responsive">
                        <table id="vehicleTable" class="table mb-0" style="width:100%">
                            <thead>
                                <tr>
                                    <th>ทะเบียนรถ</th>
                                    <th>ประเภทรถ</th>
                                    <th>วันที่ตรวจล่าสุด</th>
                                    <th>ผลการตรวจล่าสุด</th>
                                    <th style="width:90px;">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
    $(function () {

        // ============================================
        // Select2 init
        // ============================================
        $('.s2').select2({
            theme       : 'bootstrap-5',
            width       : '100%',
            placeholder : 'ทั้งหมด',
            allowClear  : true,
        });

        // ============================================
        // State
        // ============================================
        let hasSearched = false;

        // ============================================
        // DataTable init (ไม่โหลดข้อมูลก่อนค้นหา)
        // ============================================
        const table = $('#vehicleTable').DataTable({
            processing : true,
            serverSide : true,
            ajax: function (data, callback) {
                if (!hasSearched) {
                    callback({ draw: data.draw, recordsTotal: 0, recordsFiltered: 0, data: [] });
                    return;
                }
                data.search_plate   = $('#searchPlate').val();
                data.filter_inspect = $('#filterInspect').val();
                data.filter_type    = $('#filterType').val();
                @if ($showCompanyFilter)
                data.filter_company = $('#filterCompany').val();
                data.search_text    = $('#searchInput').val();
                @endif
                @if ($showSupplyFilter)
                data.filter_supply  = $('#filterSupply').val();
                @endif

                $.ajax({
                    url     : "{{ route('vehicles.ajax.index') }}",
                    type    : 'GET',
                    data    : data,
                    success : callback,
                    error   : function (xhr) {
                        console.error('Vehicle AJAX error', xhr.status, xhr.responseText);
                        callback({ draw: data.draw, recordsTotal: 0, recordsFiltered: 0, data: [] });
                    }
                });
            },
            columns: [
                { data: 0, orderable: true },
                { data: 1, orderable: true },
                { data: 2, orderable: true },
                { data: 3, orderable: false },
                { data: 4, orderable: false, searchable: false },
            ],
            columnDefs: [
                {
                    targets: 0,
                    render: (d) => `<span class="plate-badge">${d}</span>`,
                },
                {
                    targets: 1,
                    render: (d) => d !== '-' ? `<span class="type-pill">${d}</span>` : '<span class="text-muted">-</span>',
                },
                {
                    targets: 2,
                    render: (d) => d.includes('ไม่พบ') ? d : `<span class="date-cell"><i class="uil uil-calendar-alt" style="opacity:.5;"></i> ${d}</span>`,
                },
            ],
            order    : [[2, 'desc']],
            pageLength: 25,
            language : {
                lengthMenu  : 'แสดง _MENU_ รายการ',
                info        : 'แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ คัน',
                infoFiltered: '(กรองจาก _MAX_ รายการ)',
                paginate    : { next: 'ถัดไป', previous: 'ก่อนหน้า' },
                emptyTable  : 'ไม่พบข้อมูลรถ',
                zeroRecords : 'ไม่พบรายการที่ค้นหา',
                processing  : '<div class="spinner-border spinner-border-sm text-primary"></div> กำลังโหลด...',
            },
            dom: '<"d-flex justify-content-between align-items-center mb-2"li>rt<"d-flex justify-content-between align-items-center mt-2"ip>',
        });

        // ============================================
        // ค้นหา
        // ============================================
        window.doSearch = function () {
            if (!hasSearched) {
                hasSearched = true;
                $('#preSearchMsg').addClass('d-none');
                $('#tableWrapper').removeClass('d-none');
            }
            table.ajax.reload();
        };

        // ============================================
        // ล้างค่า
        // ============================================
        window.resetSearch = function () {
            hasSearched = false;
            $('#searchPlate').val('');
            $('#filterInspect, #filterType').val(null).trigger('change');
            @if ($showCompanyFilter)
            $('#filterCompany').val(null).trigger('change');
            $('#searchInput').val('');
            @endif
            @if ($showSupplyFilter)
            $('#filterSupply').val(null).trigger('change');
            @endif
            $('#preSearchMsg').removeClass('d-none');
            $('#tableWrapper').addClass('d-none');
        };

        // ทะเบียนรถ: auto-search ทุก role (debounce 400ms)
        let plateTimer;
        $('#searchPlate').on('input', function () {
            clearTimeout(plateTimer);
            plateTimer = setTimeout(doSearch, 400);
        });

        @if ($showCompanyFilter)
        // staff/admin/manager: text search — Enter หรือพิมพ์ >= 2 ตัว
        let searchTimer;
        $('#searchInput').on('keydown', function (e) {
            if (e.key === 'Enter') { doSearch(); return; }
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => { if ($(this).val().length >= 2) doSearch(); }, 600);
        });
        @endif

    });
    </script>
@endpush
