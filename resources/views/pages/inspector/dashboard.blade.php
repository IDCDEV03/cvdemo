@section('title', 'ระบบตรวจมาตรฐานรถ')
@section('description', 'ID Drives')
@extends('layout.app')
@push('styles')
    <style>
        .btn-inspect {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 16px 28px;
            border-radius: 14px;
            background: linear-gradient(135deg, #1677ff 0%, #0ea5e9 100%);
            box-shadow: 0 4px 16px rgba(22, 119, 255, 0.35);
            color: #fff;
            font-size: 18px;
            font-weight: 700;
            text-decoration: none;
            transition: transform 0.15s, box-shadow 0.15s;
        }

        .btn-inspect:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(22, 119, 255, 0.4);
            color: #fff;
        }

        .btn-inspect i {
            font-size: 22px;
            line-height: 1;
        }

        .btn-register {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 16px 28px;
            border-radius: 14px;
            background: linear-gradient(135deg, #12b76a 0%, #059669 100%);
            box-shadow: 0 4px 16px rgba(18, 183, 106, 0.35);
            color: #fff;
            font-size: 18px;
            font-weight: 700;
            text-decoration: none;
            transition: transform 0.15s, box-shadow 0.15s;
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(18, 183, 106, 0.4);
            color: #fff;
        }

        .btn-register i {
            font-size: 22px;
            line-height: 1;
        }
    </style>
    <style>
    .hover-shadow:hover {
        transform: translateY(-2px);
        transition: all 0.2s ease-in-out;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1) !important;
    }

    /* ===== Vehicle Table ===== */
    .veh-table thead tr {
        background: linear-gradient(90deg, #1e1b4b 0%, #3730a3 60%, #4f46e5 100%);
    }
    .veh-table thead th {
        color: #fff;
        font-size: 16px;
        font-weight: 600;
        letter-spacing: .3px;
        border: none;
        padding: 13px 16px;
        white-space: nowrap;
    }
    .veh-table tbody tr {
        transition: background .15s;
    }
    .veh-table tbody tr:hover {
        background: #f5f3ff;
    }
    .veh-table tbody td {
        padding: 12px 16px;
        vertical-align: middle;
        border-color: #eff0f6;
        font-size: 14px;
    }
    .plate-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #f0f4ff;
        border: 1px solid #c7caff;
        border-radius: 8px;
        padding: 4px 12px;
        font-weight: 700;
        font-size: 15px;
        color: #3730a3;
        letter-spacing: .5px;
    }
    .type-chip {
        display: inline-block;
        background: #f1f5f9;
        border-radius: 20px;
        padding: 2px 10px;
        font-size: 14px;
        color: #64748b;
        margin-top: 4px;
    }
    .eval-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        border-radius: 20px;
        padding: 3px 12px;
        font-size: 14px;
        font-weight: 600;
    }
    .eval-pass   { background:#dcfce7; color:#16a34a; }
    .eval-warn   { background:#fef9c3; color:#b45309; }
    .eval-fail   { background:#fee2e2; color:#dc2626; }
    .eval-draft  { background:#f1f5f9; color:#64748b; }
    .busy-badge  {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #fff7ed;
        border: 1px solid #fdba74;
        border-radius: 8px;
        padding: 5px 12px;
        font-size: 13px;
        color: #c2410c;
        font-weight: 600;
    }
    .seq-num {
        width: 32px;
        height: 32px;
        background: #ede9fe;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        font-weight: 700;
        color: #5840ff;
    }
    </style>
@endpush
@section('content')
    <div class="container-fluid">
        <!-- Breadcrumb -->
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb-main">
                    <div class="breadcrumb-action justify-content-end flex-wrap ms-auto">
                        <nav aria-label="breadcrumb">
                            <div class="d-flex justify-content-end">
                                <div class="d-flex align-items-center gap-1 px-3 py-1"
                                    style="border-radius: 999px; border: 1px solid #dee2e6; background: #fff;">
                                    <span style="font-size: 14px; color: #8c9097;" id="clock-date"></span>
                                    <span style="color: #dee2e6; font-size: 13px;">·</span>
                                    <i class="uil uil-clock" style="font-size: 14px; line-height: 1; color: #8c9097;"></i>
                                    <span class="fw-600" style="font-size: 13px; font-variant-numeric: tabular-nums;"
                                        id="clock-time"></span>
                                </div>
                            </div>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-10 mb-20">
            <div class="col-md-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body d-flex gap-3">
                        <a href="{{ route('inspection.index') }}" class="btn-inspect flex-fill">
                            <i class="uil uil-truck"></i>
                            เริ่มตรวจรถ
                        </a>
                        <a href="{{ route('vehicles.create') }}" class="btn-register flex-fill">
                            <i class="uil uil-plus-circle"></i>
                            ลงทะเบียนรถ
                        </a>
                    </div>
                </div>
            </div>
        </div>


<div class="row">

    <div class="col-xxl-3 col-sm-6 mb-20">
        <a href="{{ request()->fullUrlWithQuery(['filter' => 'passed']) }}" class="text-decoration-none">     
            <div class="ap-po-details ap-po-details--2 p-25 radius-xl bg-white d-flex justify-content-between shadow-sm hover-shadow {{ $filter == 'passed' ? 'border border-success border-2' : '' }}">
                <div class="overview-content w-100">
                    <div class="ap-po-details-content d-flex flex-wrap justify-content-between">
                        <div class="ap-po-details__titlebar">
                            <h2 class="color-success">{{ $passedInspections ?? '0' }}</h2>
                            <span class="fs-16 fw-bold color-gray mt-1 mb-0">ตรวจผ่านแล้ว</span>
                        </div>
                        <div class="ap-po-details__icon-area">
                            <div class="svg-icon order-bg-opacity-success color-success d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; border-radius: 10px;">
                                <i class="uil uil-check-circle fs-24"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

      <div class="col-xxl-3 col-sm-6 mb-20">
        <a href="{{ request()->fullUrlWithQuery(['filter' => 'waiting']) }}" class="text-decoration-none">
            <div class="ap-po-details ap-po-details--2 p-25 radius-xl bg-white d-flex justify-content-between shadow-sm hover-shadow {{ $filter == 'waiting' ? 'border border-warning border-2' : '' }}">
                <div class="overview-content w-100">
                    <div class="ap-po-details-content d-flex flex-wrap justify-content-between">
                        <div class="ap-po-details__titlebar">
                            <h2 class="color-warning">{{ $waitingInspections ?? '0' }}</h2>
                            <span class="fs-16 fw-bold color-gray mt-1 mb-0">รอตรวจครั้งที่ 2</span>
                        </div>
                        <div class="ap-po-details__icon-area">
                            <div class="svg-icon order-bg-opacity-warning color-warning d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; border-radius: 10px;">
                                <i class="uil uil-clock fs-24"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-xxl-3 col-sm-6 mb-20">
        <a href="{{ request()->fullUrlWithQuery(['filter' => 'failed']) }}" class="text-decoration-none">
            <div class="ap-po-details ap-po-details--2 p-25 radius-xl bg-white d-flex justify-content-between shadow-sm hover-shadow {{ $filter == 'failed' ? 'border border-danger border-2' : '' }}">
                <div class="overview-content w-100">
                    <div class="ap-po-details-content d-flex flex-wrap justify-content-between">
                        <div class="ap-po-details__titlebar">
                            <h2 class="color-danger">{{ $failedInspections ?? '0' }}</h2>
                            <span class="fs-16 fw-bold color-gray mt-1 mb-0">ไม่ผ่านการตรวจ</span>
                        </div>
                        <div class="ap-po-details__icon-area">
                            <div class="svg-icon order-bg-opacity-danger color-danger d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; border-radius: 10px;">
                                <i class="uil uil-times-circle fs-24"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-xxl-3 col-sm-6 mb-20">
        <a href="{{ request()->fullUrlWithQuery(['filter' => 'all']) }}" class="text-decoration-none">
            <div class="ap-po-details ap-po-details--2 p-25 radius-xl bg-white d-flex justify-content-between shadow-sm hover-shadow {{ $filter == 'all' ? 'border border-primary border-2' : '' }}">
                <div class="overview-content w-100">
                    <div class="ap-po-details-content d-flex flex-wrap justify-content-between">
                        <div class="ap-po-details__titlebar">
                            <h2 class="color-primary">{{ $totalInspections ?? '0' }}</h2>
                            <span class="fs-16 fw-bold color-gray mt-1 mb-0">ประวัติการตรวจทั้งหมด</span>
                        </div>
                        <div class="ap-po-details__icon-area">
                            <div class="svg-icon order-bg-opacity-primary color-primary d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; border-radius: 10px;">
                                <i class="uil uil-clipboard-notes fs-24"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 mb-25 shadow-sm">

                    {{-- Header --}}
                    <div style="background:linear-gradient(90deg,#1e1b4b 0%,#3730a3 60%,#4f46e5 100%);
                                padding:14px 22px; border-radius:12px 12px 0 0;
                                display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px;">
                        <div style="display:flex; align-items:center; gap:10px;">
                            <i class="uil uil-list-ul" style="font-size:20px; color:rgba(255,255,255,.8);"></i>
                            <span style="font-size:16px; font-weight:700; color:#fff; letter-spacing:.3px;">
                                รายการรถที่ตรวจ
                            </span>
                            <span style="background:rgba(255,255,255,.15); color:#fff; font-size:13px;
                                         padding:3px 12px; border-radius:20px;">
                                {{ count($vehicles) }} คัน
                            </span>
                        </div>
                        <a href="{{ route('vehicles.index') }}"
                           style="display:inline-flex; align-items:center; gap:6px;
                                  background:rgba(255,255,255,.18); color:#fff; font-size:13px; font-weight:600;
                                  padding:5px 14px; border-radius:20px; text-decoration:none;
                                  border:1px solid rgba(255,255,255,.3); transition:background .15s;"
                           onmouseover="this.style.background='rgba(255,255,255,.28)'"
                           onmouseout="this.style.background='rgba(255,255,255,.18)'">
                            <i class="uil uil-car"></i> ดูรถทั้งหมด
                        </a>
                    </div>

                    <div class="card-body p-10">
                        @if(count($vehicles) === 0)
                            <div class="text-center py-5 text-muted">
                                <i class="uil uil-car" style="font-size:48px; opacity:.3;"></i>
                                <p class="mt-2 mb-0">ไม่พบรายการรถ</p>
                            </div>
                        @else
                        <div class="table-responsive">
                            <table class="table veh-table mb-0" id="table-one">
                                <thead>
                                    <tr>
                                        <th style="width:48px;">#</th>
                                        <th>ทะเบียนรถ</th>
                                        <th>ผลการตรวจล่าสุด</th>
                                        <th>ประวัติการตรวจ</th>
                                        <th class="text-center">การดำเนินการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($vehicles as $item)
                                    <tr>
                                        {{-- ลำดับ --}}
                                        <td>
                                            <span class="seq-num">{{ $loop->iteration }}</span>
                                        </td>

                                        {{-- ทะเบียนรถ + ประเภท --}}
                                        <td>
                                            <a href="{{ route('vehicles.show', $item->car_id) }}" class="text-decoration-none">
                                                <div class="plate-pill">
                                                    {{ $item->car_plate }}
                                                </div>
                                            </a>
                                            @if($item->vehicle_type)
                                                <div><span class="type-chip">{{ $item->vehicle_type }}</span></div>
                                            @endif
                                        </td>

                                        {{-- ผลการตรวจล่าสุด --}}
                                        <td>
                                            @if($item->in_progress_by_other)
                                                <div class="busy-badge">
                                                    <i class="uil uil-lock-alt"></i>
                                                    กำลังตรวจโดย {{ $item->in_progress_by_other->inspector_name }}
                                                </div>
                                            @elseif($item->inspect_count === 0)
                                                <span class="text-muted" style="font-size:14px;">ยังไม่มีประวัติ</span>
                                            @elseif($item->latest_record->chk_status == '0')
                                                <span class="eval-badge eval-draft">
                                                    <i class="uil uil-clock"></i> อยู่ระหว่างการตรวจ
                                                </span>
                                            @elseif($item->latest_record->chk_status == '2')
                                                <span class="eval-badge eval-draft">
                                                    <i class="uil uil-edit-alt"></i> อยู่ระหว่างการตรวจ
                                                </span>
                                            @elseif($item->latest_record->evaluate_status == 1)
                                                <span class="eval-badge eval-pass">
                                                    <i class="uil uil-check-circle"></i> ปกติ อนุญาตให้ใช้งาน
                                                </span>
                                            @elseif($item->latest_record->evaluate_status == 2)
                                                <span class="eval-badge eval-warn">
                                                    <i class="uil uil-exclamation-triangle"></i> ไม่ปกติ แต่ใช้งานได้
                                                </span>
                                            @elseif($item->latest_record->evaluate_status == 3)
                                                <span class="eval-badge eval-fail">
                                                    <i class="uil uil-times-circle"></i> ไม่ปกติ ห้ามใช้งาน
                                                </span>
                                                @if($item->latest_record->next_inspect_date)
                                                    <div style="font-size:12px; color:#dc2626; margin-top:4px;">
                                                        <i class="uil uil-calendar-alt"></i>
                                                        ตรวจซ้ำ: {{ thai_date(\Carbon\Carbon::parse($item->latest_record->next_inspect_date)) }}
                                                    </div>
                                                @endif
                                            @endif
                                        </td>

                                        {{-- ประวัติ --}}
                                        <td>
                                            @if($item->inspect_count > 0)
                                                <div class="d-flex flex-column gap-1">
                                                    @foreach($item->history as $index => $record)
                                                        @if($record->chk_status == '1')
                                                            <div style="font-size:14px; color:#64748b;">
                                                                <span style="font-weight:600;">ครั้งที่ {{ $item->inspect_count - $index }}</span>
                                                                @if($record->evaluate_status == 1)
                                                                    <span style="color:#16a34a;">· ผ่าน</span>
                                                                @elseif($record->evaluate_status == 2)
                                                                    <span style="color:#b45309;">· ไม่ปกติ แต่สามารถปฏิบัติงานได้</span>
                                                                @elseif($record->evaluate_status == 3)
                                                                    <span style="color:#dc2626;">· ไม่ผ่าน</span>
                                                                @endif
                                                            </div>
                                                        
                                                        @elseif($record->chk_status == '0')
                                                            <div style="font-size:14px; color:#94a3b8;">
                                                                <span style="font-weight:600;">ครั้งที่ {{ $item->inspect_count - $index }}</span>
                                                                · กำลังตรวจ..
                                                            </div>
                                                        @elseif($record->chk_status == '2')
                                                            <div style="font-size:14px; color:#94a3b8;">
                                                                <span style="font-weight:600;">ครั้งที่ {{ $item->inspect_count - $index }}</span>
                                                                · กำลังตรวจ...
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @else
                                                <span style="font-size:12px; color:#cbd5e1;">-</span>
                                            @endif
                                        </td>

                                        {{-- การดำเนินการ --}}
                                        <td class="text-center">
                                            @if($item->in_progress_by_other)
                                                <span style="font-size:14px; color:#94a3b8;">ไม่สามารถดำเนินการได้ขณะนี้</span>
                                            @elseif($item->inspect_count === 0)
                                                <span style="font-size:14px; color:#cbd5e1;">-</span>
                                            @else
                                                <div class="d-flex flex-column gap-1 align-items-center">
                                                    {{-- ปุ่มตรวจอีกครั้ง: แสดงเมื่อ record ล่าสุด evaluate_status = 2 หรือ 3 --}}
                                                    @if($item->latest_record &&
                                                        $item->latest_record->chk_status == '1' &&
                                                        in_array($item->latest_record->evaluate_status, [2, 3]))
                                                        <a href="{{ route('inspection.retest', $item->latest_record->record_id) }}"
                                                           class="btn btn-xs"
                                                           style="background:#fef3c7;color:#92400e;font-size:14px;padding:5px 12px;border-radius:6px;white-space:nowrap;border:1px solid #fcd34d;font-weight:600;">
                                                            <i class="uil uil-redo"></i> ตรวจอีกครั้ง
                                                        </a>
                                                    @endif
                                                    @foreach($item->history as $index => $record)
                                                        @if($record->chk_status == '1')
                                                            <a href="{{ route('inspection.report', $record->record_id) }}"
                                                               class="btn btn-xs"
                                                               style="background:#e0e7ff;color:#3730a3;font-size:14px;padding:4px 10px;border-radius:6px;white-space:nowrap;">
                                                                <i class="uil uil-file-alt"></i>
                                                                Report ครั้งที่ {{ $item->inspect_count - $index }}
                                                            </a>
                                                        @elseif($record->chk_status == '0')
                                                            <a href="{{ route('inspection.step2', $record->record_id) }}"
                                                               class="btn btn-xs"
                                                               style="background:#fef9c3;color:#b45309;font-size:16px;padding:4px 10px;border-radius:6px;white-space:nowrap;">
                                                                <i class="uil uil-play-circle"></i> ตรวจต่อ
                                                            </a>
                                                        @elseif($record->chk_status == '2')
                                                            <a href="{{ route('inspection.step3', [$record->record_id]) }}"
                                                               class="btn btn-xs"
                                                               style="background:#fef9c3;color:#b45309;font-size:16px;padding:4px 10px;border-radius:6px;white-space:nowrap;">
                                                                <i class="uil uil-play-circle"></i> ตรวจต่อ
                                                            </a>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- DataTables  -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        const thaiMonths = ['มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
            'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'
        ];

        function pad(n) {
            return String(n).padStart(2, '0');
        }

        function updateClock() {
            const now = new Date();
            const d = now.getDate();
            const m = now.getMonth();
            const y = now.getFullYear() + 543;
            const h = pad(now.getHours());
            const min = pad(now.getMinutes());
            const s = pad(now.getSeconds());

            document.getElementById('clock-time').textContent = `${h}:${min}:${s}`;
            document.getElementById('clock-date').textContent = `${d} ${thaiMonths[m]} ${y}`;
        }

        updateClock();
        setInterval(updateClock, 1000);
    </script>

    <script>
        $(document).ready(function() {
            $('#table-one').DataTable({
                responsive: true,
                pageLength: 25,
                language: {
                    search: "ค้นหา:",
                    lengthMenu: "แสดง _MENU_ รายการ",
                    info: "แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ",
                    paginate: {
                        next: "ถัดไป",
                        previous: "ก่อนหน้า"
                    }
                }
            });
        });
    </script>
@endpush
