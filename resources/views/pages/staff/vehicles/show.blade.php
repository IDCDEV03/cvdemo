@section('title', 'รายละเอียดรถ')
@section('description', 'ID Drives')
@extends('layout.app')

@push('styles')
    <style>
        .hero-card {
            background: linear-gradient(135deg, #f0f4ff 0%, #fafaff 100%);
            border: 0.5px solid #e0e4ff;
            border-radius: var(--border-radius-lg, 12px);
            padding: 20px;
            margin-bottom: 20px;
        }

        .vehicle-thumb {
            width: 100px;
            height: 100px;
            border-radius: 10px;
            object-fit: cover;
            border: 0.5px solid #e0e4ff;
        }

        .vehicle-thumb-placeholder {
            width: 100px;
            height: 100px;
            border-radius: 10px;
            background: #eeecff;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 0.5px solid #e0e4ff;
            flex-shrink: 0;
        }

        .vehicle-thumb-placeholder i {
            font-size: 36px;
            color: #5840ff;
            opacity: 0.5;
        }

        .info-card {
            background: var(--color-bg-container, #fff);
            border: 0.5px solid #e3e6ef;
            border-radius: 10px;
            padding: 16px;
            height: 100%;
        }

        .info-card-title {
            font-size: 16px;
            font-weight: 600;
            color: #5840ff;
            border-bottom: 2px solid #f0f4ff;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }

        .info-row {
            display: flex;
            padding: 6px 0;
            border-bottom: 1px dashed #f1f2f6;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            flex: 0 0 45%;
            font-size: 16px;
            font-weight: bold;
            color: #64748b;
        }

        .info-value {
            flex: 1;
            font-size: 16px;
            font-weight: 500;
            color: #404659;
            word-break: break-word;
        }


        .doc-card-active {
            background: #fef9e7;
            border-left: 4px solid #f5c842;
            border-radius: 0 8px 8px 0;
            padding: 12px 16px;
            cursor: pointer;
            transition: background 0.2s;
        }

        .doc-card-active:hover {
            background: #fef3c7;
        }

        .doc-card-history {
            background: #f8f9fb;
            border-left: 4px solid #cbd5e1;
            border-radius: 0 8px 8px 0;
            padding: 10px 14px;
            margin-bottom: 8px;
            cursor: pointer;
            transition: background 0.2s;
        }

        .doc-card-history:hover {
            background: #f1f5f9;
        }

        .doc-card-history.docx-type {
            cursor: default;
        }

        .evaluate-1 {
            color: #065f46;
            background: #d1fae5;
        }

        .evaluate-2 {
            color: #92400e;
            background: #fef3c7;
        }

        .evaluate-3 {
            color: #991b1b;
            background: #fee2e2;
        }

        .date-expired {
            color: #dc2626;
            font-weight: 500;
        }

        .date-warning {
            color: #d97706;
            font-weight: 500;
        }

        .date-ok {
            color: #404659;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">

        {{-- Breadcrumb --}}
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb-main">
                    {{-- Left: Title --}}
                    <span class="fs-24 fw-bold breadcrumb-title">รายละเอียดรถ</span>

                    {{-- Center: Action buttons (PC only) --}}
                    <div class="d-none d-md-flex align-items-center">
                        <div class="btn-group">
                            <a href="{{ route('staff.veh_list') }}" class="btn btn-outline-dark btn-sm" title="ย้อนกลับ">
                                <i class="uil uil-arrow-left"></i> ย้อนกลับ
                            </a>
                            <a href="{{ route('staff.vehicles.edit', $vehicle->car_id) }}" class="btn btn-primary btn-sm"
                                title="แก้ไข">
                                <i class="uil uil-edit"></i> แก้ไข
                            </a>
                            <div class="btn-group">
                                <button type="button" class="btn btn-outline-secondary btn-sm dropdown-toggle"
                                    data-bs-toggle="dropdown">
                                    <i class="uil uil-toggle-on"></i> เปลี่ยนสถานะ
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item status-option" href="#" data-status="1"
                                            data-label="เปิดการใช้งาน">
                                            <i class="las la-check-circle me-2 text-success"></i> เปิดการใช้งาน
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item status-option" href="#" data-status="0"
                                            data-label="ปิดการใช้งาน">
                                            <i class="las la-minus-circle me-2 text-secondary"></i> ปิดการใช้งาน
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item status-option text-danger" href="#" data-status="2"
                                            data-label="ห้ามใช้งาน">
                                            <i class="las la-ban me-2"></i> ห้ามใช้งาน
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    {{-- Right: Breadcrumb + Mobile buttons --}}
                    <div class="breadcrumb-action justify-content-center flex-wrap">

                        {{-- Mobile only buttons --}}
                        <div class="d-flex d-md-none me-2">
                            <div class="btn-group">
                                <a href="{{ route('staff.vehicles.index') }}" class="btn btn-outline-dark btn-sm"
                                    title="ย้อนกลับ">
                                    <i class="uil uil-arrow-left"></i>
                                </a>
                                <a href="{{ route('staff.vehicles.edit', $vehicle->car_id) }}"
                                    class="btn btn-primary btn-sm" title="แก้ไข">
                                    <i class="uil uil-edit"></i>
                                </a>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-outline-secondary btn-sm dropdown-toggle"
                                        data-bs-toggle="dropdown">
                                        <i class="uil uil-toggle-on"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item status-option" href="#" data-status="1"
                                                data-label="เปิดการใช้งาน">
                                                <i class="las la-check-circle me-2 text-success"></i> เปิดการใช้งาน
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item status-option" href="#" data-status="0"
                                                data-label="ปิดการใช้งาน">
                                                <i class="las la-minus-circle me-2 text-secondary"></i> ปิดการใช้งาน
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item status-option text-danger" href="#"
                                                data-status="2" data-label="ห้ามใช้งาน">
                                                <i class="las la-ban me-2"></i> ห้ามใช้งาน
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        {{-- Breadcrumb --}}
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('staff.vehicles.index') }}">รายการรถ</a>
                                </li>
                                <li class="breadcrumb-item active">{{ $vehicle->car_plate }}</li>
                            </ol>
                        </nav>

                    </div>
                </div>
            </div>
        </div>

        {{-- Flash messages --}}
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        {{-- ============================================ --}}
        {{-- HERO HEADER CARD                              --}}
        {{-- ============================================ --}}
        <div class="hero-card">
            <div class="d-flex gap-3 align-items-start">

                {{-- Vehicle thumbnail --}}
                @if ($vehicle->car_image)
                    <img src="{{ asset($vehicle->car_image) }}" class="vehicle-thumb" alt="รูปรถ">
                @else
                    <div class="vehicle-thumb-placeholder">
                        <i class="uil uil-truck"></i>
                    </div>
                @endif

                {{-- Main info --}}
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                        <span class="fs-20 fw-bold text-dark">{{ $vehicle->car_plate }}</span>
                        <span class="status-badge status-{{ $vehicle->status }}" id="status_badge">
                            @if ($vehicle->status == '1')
                                <span class="dm-tag tag-success tag-transparented fs-18"> เปิดการใช้งาน </span>
                            @elseif ($vehicle->status == '0')
                                <span class="dm-tag tag-warning tag-transparented fs-18"> ปิดการใช้งาน </span>
                            @else
                                <span class="dm-tag tag-danger tag-transparented fs-18"> ห้ามใช้งาน </span>
                            @endif
                        </span>
                    </div>
                    <div class="text-muted fs-14 mb-2">
                        {{ $vehicle->car_brand }} {{ $vehicle->car_model }} ·
                        {{ $vehicle->vehicle_type_name ?? '-' }}
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="dm-tag tag-primary tag-transparented fs-18">
                            {{ $vehicle->company_name ?? '-' }}
                        </span>
                        <span class="dm-tag tag-info tag-transparented fs-18">
                            {{ $vehicle->supply_name ?? '-' }}
                        </span>
                    </div>
                </div>


            </div>
        </div>

        {{-- ============================================ --}}
        {{-- DETAIL CARDS (2 columns)                      --}}
        {{-- ============================================ --}}
        <div class="row g-3 mb-3">

            {{-- LEFT COLUMN --}}
            <div class="col-md-6 d-flex flex-column gap-3">

                {{-- Card: ข้อมูลรถ --}}
                <div class="info-card">
                    <div class="info-card-title">
                        <i class="uil uil-truck"></i> ข้อมูลรถ
                    </div>
                    <div class="info-row">
                        <div class="info-label">ยี่ห้อ</div>
                        <div class="info-value">{{ $vehicle->car_brand ?? '-' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">รุ่น</div>
                        <div class="info-value">{{ $vehicle->car_model ?? '-' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">ประเภทรถ</div>
                        <div class="info-value">{{ $vehicle->vehicle_type_name ?? '-' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">หมายเลขรถ</div>
                        <div class="info-value">{{ $vehicle->car_number_record ?? '-' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">ทะเบียนหาง</div>
                        <div class="info-value">{{ $vehicle->car_trailer_plate ?? '-' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">ปีจดทะเบียน</div>
                        <div class="info-value">{{ $vehicle->car_age ?? '-' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">เลขไมล์</div>
                        <div class="info-value">
                            {{ $vehicle->car_mileage ? number_format($vehicle->car_mileage) . ' กม.' : '-' }}
                        </div>
                    </div>
                </div>

                {{-- Card: ข้อมูลเทคนิค --}}
                <div class="info-card">
                    <div class="info-card-title">
                        <i class="uil uil-setting"></i> ข้อมูลเทคนิค
                    </div>
                    <div class="info-row">
                        <div class="info-label">เชื้อเพลิง</div>
                        <div class="info-value">{{ $vehicle->car_fuel_type ?? '-' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">น้ำหนักรถเปล่า</div>
                        <div class="info-value">
                            {{ $vehicle->car_weight ? number_format($vehicle->car_weight) . ' T' : '-' }}
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">น้ำหนักรถรวม</div>
                        <div class="info-value">
                            {{ $vehicle->car_total_weight ? number_format($vehicle->car_total_weight) . ' T' : '-' }}
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">ชนิดสินค้า</div>
                        <div class="info-value">{{ $vehicle->car_product ?? '-' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">บริษัทประกัน</div>
                        <div class="info-value">{{ $vehicle->insurance_company_name ?? '-' }}</div>
                    </div>
                </div>

            </div>

            {{-- RIGHT COLUMN --}}
            <div class="col-md-6 d-flex flex-column gap-3">

                {{-- Card: วันที่สำคัญ --}}
                <div class="info-card">
                    <div class="info-card-title">
                        <i class="uil uil-calendar-alt"></i> วันที่สำคัญ
                    </div>
                    @php
                        $today = now()->startOfDay();

                        // Helper: return CSS class based on expiry
                        $dateClass = function ($dateStr) use ($today) {
                            if (!$dateStr) {
                                return '';
                            }
                            $date = \Carbon\Carbon::parse($dateStr)->startOfDay();
                            $diff = $today->diffInDays($date, false);
                            if ($diff < 0) {
                                return 'date-expired';
                            }
                            if ($diff <= 30) {
                                return 'date-warning';
                            }
                            return 'date-ok';
                        };

                        // Helper: format date to Thai พ.ศ.
                        $thaiDate = function ($dateStr) {
                            if (!$dateStr) {
                                return '-';
                            }
                            $date = \Carbon\Carbon::parse($dateStr);
                            return $date->format('d/m/') . ($date->year + 543);
                        };
                    @endphp

                    <div class="info-row">
                        <div class="info-label">วันจดทะเบียน</div>
                        <div class="info-value">{{ thai_date($vehicle->car_register_date) }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">ภาษีหมดอายุ</div>
                        <div class="info-value {{ $dateClass($vehicle->car_tax) }}">
                            {{ thai_date($vehicle->car_tax) }}
                            @if ($vehicle->car_tax)
                                @php $diff = $today->diffInDays(\Carbon\Carbon::parse($vehicle->car_tax)->startOfDay(), false); @endphp
                                @if ($diff < 0)
                                    <span class="dm-tag tag-danger ms-1" style="font-size:14px;">หมดอายุแล้ว</span>
                                @elseif ($diff <= 30)
                                    <span class="dm-tag tag-warning ms-1" style="font-size:14px;">ใกล้หมด</span>
                                @endif
                            @endif
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">ประกันเริ่ม</div>
                        <div class="info-value">{{ thai_date($vehicle->car_insurance_start) }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">ประกันหมดอายุ</div>
                        <div class="info-value {{ $dateClass($vehicle->car_insurance_expire) }}">
                            {{ thai_date($vehicle->car_insurance_expire) }}
                            @if ($vehicle->car_insurance_expire)
                                @php $diff2 = $today->diffInDays(\Carbon\Carbon::parse($vehicle->car_insurance_expire)->startOfDay(), false); @endphp
                                @if ($diff2 < 0)
                                    <span class="dm-tag tag-danger ms-1" style="font-size:14px;">หมดอายุแล้ว</span>
                                @elseif ($diff2 <= 30)
                                    <span class="dm-tag tag-warning ms-1" style="font-size:14px;">ใกล้หมด</span>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Card: Document --}}
                {{-- Card: Document --}}
                <div class="info-card">
                    <div class="info-card-title d-flex justify-content-between align-items-center">
                        <span><i class="uil uil-file-alt"></i> เอกสาร</span>
                        <button class="btn btn-sm btn-outline-primary py-0 px-2" style="font-size:14px;"
                            data-bs-toggle="modal" data-bs-target="#uploadDocModal">
                            <i class="uil uil-upload-alt"></i> อัปโหลดใหม่
                        </button>
                    </div>

                    @if ($activeDocument)
                        {{-- Active document --}}
                        <div class="doc-card-active mb-2"
                            onclick="previewDocument('{{ asset($activeDocument->file_path) }}', '{{ $activeDocument->file_extension }}', '{{ $activeDocument->file_original_name }}')"
                            title="คลิกเพื่อดูเอกสาร">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-bold text-dark" style="font-size:16px;">
                                        <i
                                            class="uil {{ $activeDocument->file_extension === 'pdf' ? 'uil-file-pdf-alt text-danger' : 'uil-file-alt text-primary' }}"></i>
                                        {{ $activeDocument->doc_name }}
                                    </div>
                                    <div class="text-muted" style="font-size:12px;">
                                        {{ number_format($activeDocument->file_size / 1024, 1) }} KB ·
                                        {{ thai_date($activeDocument->created_at) }} ·
                                        {{ $activeDocUploader ?? '-' }}
                                    </div>
                                </div>
                                <div class="d-flex gap-2 align-items-center">
                                    {{-- Preview icon (PDF only) --}}
                                    @if ($activeDocument->file_extension === 'pdf')
                                        <span title="คลิกดูเอกสาร"
                                            style="width:30px; height:30px; border-radius:50%;
                                   background:rgba(32,201,151,0.12);
                                   display:inline-flex; align-items:center; justify-content:center;
                                   cursor:pointer;">
                                            <i class="uil uil-eye" style="font-size:15px; color:#20c997;"></i>
                                        </span>
                                    @endif

                                    {{-- Download icon --}}
                                    <a href="{{ route('staff.vehicles.documents.download', [$vehicle->car_id, $activeDocument->id]) }}"
                                        onclick="event.stopPropagation()" title="ดาวน์โหลดเอกสาร"
                                        style="width:30px; height:30px; border-radius:50%;
                               background:rgba(88,64,255,0.10);
                               display:inline-flex; align-items:center; justify-content:center;
                               text-decoration:none;">
                                        <i class="uil uil-download-alt" style="font-size:15px; color:#5840ff;"></i>
                                    </a>

                                    {{-- Delete icon --}}
                                    <span title="ลบเอกสาร"
                                        onclick="event.stopPropagation(); confirmDeleteDoc({{ $activeDocument->id }}, '{{ addslashes($activeDocument->doc_name) }}')"
                                        style="width:30px; height:30px; border-radius:50%;
                               background:rgba(220,38,38,0.10);
                               display:inline-flex; align-items:center; justify-content:center;
                               cursor:pointer;">
                                        <i class="uil uil-trash-alt" style="font-size:15px; color:#dc2626;"></i>
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Document history (collapse) --}}
                        @if ($documentHistory->count() > 0)
                            <div>
                                <button class="btn btn-sm btn-light p-0 text-muted"
                                    style="font-size:14px; text-decoration:none;" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#docHistoryCollapse">
                                    &nbsp;<i class="uil uil-history"></i>
                                    ประวัติเอกสารเก่า ({{ $documentHistory->count() }} รายการ)
                                    <i class="uil uil-angle-down"></i>
                                </button>
                                <div class="collapse mt-2" id="docHistoryCollapse">
                                    @foreach ($documentHistory as $doc)
                                        <div class="doc-card-history {{ $doc->file_extension !== 'pdf' ? 'docx-type' : '' }}"
                                            @if ($doc->file_extension === 'pdf') onclick="previewDocument('{{ asset($doc->file_path) }}', '{{ $doc->file_extension }}', '{{ $doc->file_original_name }}')"
                                title="คลิกเพื่อดู PDF" @endif>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <div style="font-size:12px; font-weight:500; color:#64748b;">
                                                        <i
                                                            class="uil {{ $doc->file_extension === 'pdf' ? 'uil-file-pdf-alt' : 'uil-file-alt' }}"></i>
                                                        {{ $doc->doc_name }}
                                                    </div>
                                                    <div style="font-size:10px; color:#94a3b8;">
                                                        {{ thai_date($doc->created_at) }} ·
                                                        {{ $doc->uploader_name ?? '-' }}
                                                    </div>
                                                </div>
                                                <div class="d-flex gap-2 align-items-center">
                                                    {{-- Download --}}
                                                    <a href="{{ route('staff.vehicles.documents.download', [$vehicle->car_id, $doc->id]) }}"
                                                        class="dm-tag tag-secondary tag-transparented"
                                                        style="font-size:12px; padding:5px 7px;"
                                                        onclick="event.stopPropagation()" title="ดาวน์โหลด">
                                                        Download
                                                    </a>

                                                    {{-- Delete --}}
                                                    <span
                                                        onclick="event.stopPropagation(); confirmDeleteDoc({{ $doc->id }}, '{{ addslashes($doc->doc_name) }}')"
                                                        class="dm-tag tag-danger tag-transparented"
                                                        style="font-size:12px; padding:5px 7px; cursor:pointer;"
                                                        title="ลบเอกสาร">
                                                        ลบ
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="text-center text-muted py-3" style="font-size:13px;">
                            <i class="uil uil-folder-open" style="font-size:32px; opacity:0.3; display:block;"></i>
                            ยังไม่มีเอกสาร
                            <div style="font-size:12px;">คลิก "อัปโหลดใหม่" เพื่อเพิ่มเอกสาร</div>
                        </div>
                    @endif
                </div>

            </div>
        </div>

        {{-- ============================================ --}}
        {{-- INSPECTION HISTORY TABLE                      --}}
        {{-- ============================================ --}}
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-sm mb-25">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <i class="uil uil-clipboard-alt"></i> ประวัติการตรวจรถ
                        </h6>
                        @if ($inspectionRecords->count() > 0)
                            <span class="dm-tag tag-transparented tag-primary fs-18">
                                ทั้งหมด {{ $inspectionRecords->count() }} ครั้ง
                            </span>
                        @endif
                    </div>
                    <div class="card-body">
                        @if ($inspectionRecords->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered mb-0" id="inspection-table">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>

                                            <th>วันที่ตรวจ</th>
                                            <th>ผลการตรวจ</th>
                                            <th>ตรวจโดย</th>
                                            <th>รายงาน</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($inspectionRecords as $record)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>

                                                <td>{{ thai_date($record->inspect_date) }}</td>
                                                <td>
                                                    @if ($record->evaluate_status == 1)
                                                        <span class="text-success fw-bold ">
                                                            ผ่าน
                                                        </span>
                                                    @elseif ($record->evaluate_status == 2)
                                                        <span class="text-warning fw-bold fst-italic">
                                                            ไม่ผ่าน แต่สามารถใช้งานได้
                                                        </span>
                                                    @elseif ($record->evaluate_status == 3)
                                                        <span class="text-danger fw-bold fst-italic">
                                                            ไม่ผ่าน ไม่อนุญาตให้ใช้งาน
                                                        </span>
                                                    @else
                                                        <span class="text-info fw-bold">
                                                            ยังไม่ประเมิน
                                                        </span>
                                                    @endif
                                                </td>

                                                <td>{{ $record->inspector_name ?? '-' }}</td>
                                                <td>
                                                    <a href="{{ route('inspection.report', $record->record_id) }}"
                                                        class="btn btn-sm btn-outline-primary py-0 px-2" target="_blank">
                                                        <i class="uil uil-file-search-alt"></i> ดูรายงาน
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center text-muted py-4">
                                <i class="uil uil-clipboard-alt" style="font-size:36px; opacity:0.3; display:block;"></i>
                                <span style="font-size:13px;">ยังไม่มีประวัติการตรวจรถ</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- ============================================ --}}
    {{-- PDF PREVIEW MODAL                             --}}
    {{-- ============================================ --}}
    <div class="modal fade" id="pdfPreviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header py-2">
                    <h6 class="modal-title mb-0">
                        <i class="uil uil-file-pdf-alt text-danger"></i>
                        <span id="pdfPreviewTitle">เอกสาร</span>
                    </h6>
                    <div class="d-flex gap-2 align-items-center">
                        <a id="pdfDownloadBtn" href="#" class="btn btn-sm btn-outline-primary py-0"
                            style="font-size:12px;">
                            <i class="uil uil-download-alt"></i> ดาวน์โหลด
                        </a>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                </div>
                <div class="modal-body p-0" style="height: 80vh;">
                    <iframe id="pdfPreviewFrame" src="" style="width:100%; height:100%; border:none;"
                        title="PDF Preview">
                    </iframe>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- UPLOAD DOCUMENT MODAL                         --}}
    {{-- ============================================ --}}
    <div class="modal fade" id="uploadDocModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">
                        <i class="uil uil-upload-alt"></i> อัพโหลดเอกสารใหม่
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('staff.vehicles.documents.upload', $vehicle->car_id) }}" method="POST"
                    enctype="multipart/form-data" id="uploadDocForm">
                    @csrf
                    <div class="modal-body">

                        <div class="mb-3">
                            <label class="form-label">ชื่อเอกสาร <span class="text-danger">*</span></label>
                            <input type="text" name="doc_name" class="form-control"
                                value="เอกสาร {{ $vehicle->car_plate }} ประจำปี {{ now()->year + 543 }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">ไฟล์เอกสาร <span class="text-danger">*</span></label>
                            <input type="file" name="vehicle_document" id="uploadDocFile" class="form-control"
                                accept=".pdf,.docx" required>
                            <small class="text-muted">PDF หรือ DOCX เท่านั้น (สูงสุด 10 MB)</small>
                        </div>

                        <div id="uploadDocInfo" class="d-none">
                            <span class="dm-tag tag-info tag-transparented" id="uploadDocFilename"></span>
                            <span class="text-muted ms-2" id="uploadDocFilesize"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">ยกเลิก</button>
                        <button type="submit" id="btnUploadDoc" class="btn btn-primary">
                            <i class="uil uil-upload-alt"></i> Upload
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).ready(function() {

            // ============================================
            // SweetAlert2: Show success message (after store/upload)
            // ============================================
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'สำเร็จ!',
                    text: '{{ session('success') }}',
                    confirmButtonText: 'ตกลง',
                    confirmButtonColor: '#5840ff',
                    timer: 3000,
                    timerProgressBar: true,
                });
            @endif

            // ============================================
            // Status Change via AJAX
            // ============================================
            const statusBadgeClasses = {
                '0': 'tag-warning',
                '1': 'tag-success',
                '2': 'tag-danger',
            };
            const statusLabels = {
                '0': 'ปิดการใช้งาน',
                '1': 'เปิดการใช้งาน',
                '2': 'ห้ามใช้งาน',
            };

            $('.status-option').on('click', function(e) {
                e.preventDefault();
                const newStatus = $(this).data('status').toString();
                const newLabel = $(this).data('label');

                Swal.fire({
                    title: 'เปลี่ยนสถานะรถ?',
                    text: `เปลี่ยนเป็น "${newLabel}"`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'ยืนยัน',
                    cancelButtonText: 'ยกเลิก',
                    confirmButtonColor: '#5840ff',
                }).then((result) => {
                    if (!result.isConfirmed) return;

                    $.ajax({
                        url: "{{ route('staff.vehicles.ajax.change_status', $vehicle->car_id) }}",
                        method: 'POST',
                        data: {
                            status: newStatus
                        },
                        success: function(response) {
                            if (response.success) {
                                // Update badge
                                const badge = $('#status_badge');
                                badge.removeClass('tag-success tag-default tag-danger')
                                    .addClass(statusBadgeClasses[newStatus])
                                    .text(statusLabels[newStatus]);

                                Swal.fire({
                                    icon: 'success',
                                    title: 'เปลี่ยนสถานะสำเร็จ',
                                    text: response.message,
                                    timer: 2000,
                                    showConfirmButton: false,
                                });
                            } else {
                                Swal.fire('เกิดข้อผิดพลาด', response.message, 'error');
                            }
                        },
                        error: function(xhr) {
                            Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถเปลี่ยนสถานะได้',
                                'error');
                            console.error(xhr);
                        }
                    });
                });
            });

            // ============================================
            // Upload document: file info preview
            // ============================================
            $('#uploadDocFile').on('change', function() {
                const file = this.files[0];
                if (!file) {
                    $('#uploadDocInfo').addClass('d-none');
                    return;
                }

                const ext = file.name.split('.').pop().toLowerCase();
                if (!['pdf', 'docx'].includes(ext)) {
                    alert('รองรับเฉพาะ PDF หรือ DOCX');
                    $(this).val('');
                    $('#uploadDocInfo').addClass('d-none');
                    return;
                }
                if (file.size > 10 * 1024 * 1024) {
                    alert('ขนาดไฟล์ต้องไม่เกิน 10 MB');
                    $(this).val('');
                    $('#uploadDocInfo').addClass('d-none');
                    return;
                }

                const sizeMB = (file.size / (1024 * 1024)).toFixed(2);
                $('#uploadDocFilename').text(file.name);
                $('#uploadDocFilesize').text('(' + sizeMB + ' MB)');
                $('#uploadDocInfo').removeClass('d-none');
            });

            // ============================================
            // Upload document form submit
            // ============================================
            $('#uploadDocForm').on('submit', function() {
                $('#btnUploadDoc').prop('disabled', true)
                    .html('<span class="spinner-border spinner-border-sm"></span> กำลัง Upload...');
            });

        });

        // ============================================
        // Preview Document (PDF = iframe modal, DOCX = download)
        // ============================================
        function previewDocument(fileUrl, ext, filename) {
            if (ext === 'pdf') {
                // Show PDF preview modal
                $('#pdfPreviewTitle').text(filename);
                $('#pdfPreviewFrame').attr('src', fileUrl);
                $('#pdfDownloadBtn').attr('href', fileUrl);
                const modal = new bootstrap.Modal(document.getElementById('pdfPreviewModal'));
                modal.show();

                // Clear iframe on modal close (prevent background loading)
                document.getElementById('pdfPreviewModal').addEventListener('hidden.bs.modal', function() {
                    $('#pdfPreviewFrame').attr('src', '');
                }, {
                    once: true
                });

            } else {
                // DOCX: cannot preview → trigger download
                window.location.href = fileUrl;
            }
        }
 // ============================================
// Delete Document via AJAX
// ============================================
function confirmDeleteDoc(docId, docName) {
    Swal.fire({
        title: 'ลบเอกสาร?',
        html: `ต้องการลบ <strong>"${docName}"</strong> ใช่ไหม?<br>
               <span class="text-danger" style="font-size:13px;">ไฟล์จะถูกลบถาวร ไม่สามารถกู้คืนได้</span>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'ยืนยันการลบ',
        cancelButtonText: 'ยกเลิก',
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
    }).then((result) => {
        if (!result.isConfirmed) return;
 
        const url = '{{ route('staff.vehicles.documents.delete', [$vehicle->car_id, '__DOC_ID__']) }}'
                        .replace('__DOC_ID__', docId);

        $.ajax({
            url: url,
            method: 'POST',
            data: { _method: 'DELETE' },
            success: function (res) {
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'ลบเรียบร้อย',
                        text: res.message,
                        timer: 2000,
                        showConfirmButton: false,
                    }).then(() => {
                        // Reload page เพื่ออัปเดต active/history
                        location.reload();
                    });
                } else {
                    Swal.fire('เกิดข้อผิดพลาด', res.message, 'error');
                }
            },
            error: function (xhr) {
                const msg = xhr.responseJSON?.message || 'ลบไม่สำเร็จ';
                Swal.fire('เกิดข้อผิดพลาด', msg, 'error');
            }
        });
    });
}
    </script>

    <script>
        // DataTable: inspection history
        @if ($inspectionRecords->count() > 0)
            $('#inspection-table').DataTable({
                responsive: true,
                pageLength: 10,
                order: [
                    [1, 'desc']
                ],
                language: {
                    search: "ค้นหา:",
                    lengthMenu: "แสดง _MENU_ รายการ",
                    info: "แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ",
                    paginate: {
                        next: "ถัดไป",
                        previous: "ก่อนหน้า"
                    },
                    emptyTable: "ไม่มีข้อมูล"
                }
            });
        @endif
    </script>
@endpush
