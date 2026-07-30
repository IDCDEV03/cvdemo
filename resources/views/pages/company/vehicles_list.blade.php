@section('title', 'รายการรถ')
@section('description', 'ID Drives')
@extends('layout.app')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<style>
    .page-header-card {
        background: linear-gradient(135deg, #1677ff 0%, #0ea5e9 100%);
        border-radius: 16px;
        border: none;
        padding: 24px 28px;
        color: #fff;
        margin-bottom: 24px;
    }
    .page-header-card h4 { color: #fff; font-size: 20px; font-weight: 700; margin: 0; }
    .page-header-card .sub { font-size: 13px; opacity: .75; margin-top: 2px; }

    .stat-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(255,255,255,0.18);
        border-radius: 20px;
        padding: 4px 14px;
        font-size: 13px;
        font-weight: 600;
    }

    .table-card {
        border-radius: 16px;
        box-shadow: 0 2px 16px rgba(0,0,0,0.06);
        border: none;
        overflow: hidden;
    }

    /* DataTables toolbar override */
    .dataTables_wrapper .dataTables_filter input {
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 6px 12px;
        font-size: 13px;
        outline: none;
        transition: border .15s;
    }
    .dataTables_wrapper .dataTables_filter input:focus { border-color: #1677ff; }
    .dataTables_wrapper .dataTables_length select {
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 4px 8px;
        font-size: 13px;
    }
    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_paginate { font-size: 13px; padding: 12px 20px; }
    .dataTables_wrapper .paginate_button.current,
    .dataTables_wrapper .paginate_button.current:hover {
        background: #1677ff !important;
        border-color: #1677ff !important;
        color: #fff !important;
        border-radius: 8px !important;
    }
    .dataTables_wrapper .paginate_button:hover {
        background: #f0f6ff !important;
        border-color: #d0e4ff !important;
        color: #1677ff !important;
        border-radius: 8px !important;
    }

    /* Table */
    #veh-table thead tr {
        background: #f8faff;
    }
    #veh-table thead th {
        font-size: 18px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #94a3b8;
        border-bottom: 1px solid #e9ecef;
        border-top: none;
        padding: 14px 16px;
        white-space: nowrap;
        vertical-align: middle;
    }
    #veh-table tbody td {
        padding: 14px 16px;
        vertical-align: middle;
        border-color: #f1f5f9;
        font-size: 16px;
    }
    #veh-table tbody tr { transition: background .12s; }
    #veh-table tbody tr:hover { background: #f0f6ff; }

    .plate-chip {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        background: #f0f6ff;
        border: 1px solid #d0e4ff;
        border-radius: 8px;
        padding: 5px 12px;
        font-weight: 700;
        font-size: 15px;
        color: #1677ff;
        text-decoration: none;
        letter-spacing: 0.04em;
        transition: background .15s;
    }
    .plate-chip:hover { background: #dbeafe; color: #1677ff; }

    .car-type-tag {
        display: inline-block;
        background: #f1f5f9;
        color: #475569;
        border-radius: 6px;
        padding: 2px 9px;
        font-size: 15px;
        font-weight: 600;
    }
    .car-model-sub { font-size: 14px; color: #b0b8c4; margin-top: 3px; }

    .supply-chip {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        color: #16a34a;
        border-radius: 8px;
        padding: 4px 10px;
        font-size: 15px;
        font-weight: 500;
    }

    .status-dot {
        width: 8px; height: 8px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 6px;
        flex-shrink: 0;
    }
    .s-ok    { background: #22c55e; box-shadow: 0 0 0 3px rgba(34,197,94,.2); }
    .s-wait  { background: #f59e0b; box-shadow: 0 0 0 3px rgba(245,158,11,.2); }
    .s-ban   { background: #ef4444; box-shadow: 0 0 0 3px rgba(239,68,68,.2); }

    .status-badge {
        display: inline-flex;
        align-items: center;
        border-radius: 20px;
        padding: 5px 13px;
        font-size: 14px;
        font-weight: 600;
        white-space: nowrap;
    }
    .status-ok   { background:#f0fdf4; color:#15803d; }
    .status-wait { background:#fffbeb; color:#b45309; }
    .status-ban  { background:#fef2f2; color:#b91c1c; }

    .row-num {
        width: 36px; height: 36px;
        border-radius: 50%;
        background: #f1f5f9;
        color: #94a3b8;
        font-size: 12px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
</style>
@endpush

@section('content')
@php use App\Enums\Role; $role = Auth::user()->role; @endphp
<div class="container-fluid">

    {{-- Page Header --}}
    <div class="page-header-card d-flex align-items-center justify-content-between flex-wrap gap-2 mt-20">
        <div>
            <h4><i class="uil uil-car me-2"></i>รายการรถ</h4>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <span class="stat-pill">
                <i class="uil uil-list-ul"></i>
                ทั้งหมด {{ count($veh_list) }} คัน
            </span>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="card table-card mt-20 mb-20">
        <div class="card-body mt-10 p-10">
            <div class="table-responsive">
                <table class="table mb-0" id="veh-table">
                    <thead>
                        <tr>
                            <th class="text-center" style="width:56px;">#</th>
                            <th>ทะเบียนรถ</th>
                            <th>ประเภท / รุ่น</th>
                            @if($role !== Role::Company)
                            <th>บริษัทฯว่าจ้าง</th>
                            @endif
                            <th>Supply</th>
                            <th class="text-center">สถานะ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($veh_list as $item)
                        <tr>
                            <td class="text-center">
                                <span class="row-num">{{ $loop->iteration }}</span>
                            </td>

                            <td>
                                <a href="{{ route('vehicles.show', ['veh_id' => $item->car_id]) }}" class="plate-chip">
                                 
                                    {{ $item->car_plate }}
                                </a>
                            </td>

                            <td>
                                @if($item->vehicle_type)
                                    <span class="car-type-tag">{{ $item->vehicle_type }}</span>
                                @endif
                                <div class="car-model-sub">{{ $item->car_brand }} {{ $item->car_model }}</div>
                            </td>

                            @if($role !== Role::Company)
                            <td>
                                @if($item->name)
                                    <div class="d-flex align-items-center gap-1">
                                        <span>{{ $item->name }}</span>
                                    </div>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            @endif

                            <td>
                                @if($item->supply_name)
                                    <span class="supply-chip">
                                        {{ $item->supply_name }}
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>

                            <td class="text-center">
                                @if($item->status == '1')
                                    <span class="status-badge status-ok">
                                        <span class="status-dot s-ok"></span>ปกติ
                                    </span>
                                @elseif($item->status == '2')
                                    <span class="status-badge status-wait">
                                        <span class="status-dot s-wait"></span>รอซ่อม
                                    </span>
                                @elseif($item->status == '0')
                                    <span class="status-badge status-ban">
                                        <span class="status-dot s-ban"></span>งดใช้งาน
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ $role === Role::Company ? 5 : 6 }}" class="text-center py-5">
                                <div style="color:#cbd5e1;">
                                    <i class="uil uil-car-slash" style="font-size:48px; display:block; margin-bottom:8px;"></i>
                                    <div style="font-size:15px; font-weight:600; color:#94a3b8;">ไม่พบข้อมูลรถในระบบ</div>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function () {
        $('#veh-table').DataTable({
            responsive: true,
            pageLength: 25,
            language: {
                search: "ค้นหา:",
                searchPlaceholder: "พิมพ์เพื่อค้นหา...",
                lengthMenu: "แสดง _MENU_ รายการ",
                info: "แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ คัน",
                infoEmpty: "ไม่มีข้อมูล",
                zeroRecords: "ไม่พบข้อมูลที่ค้นหา",
                paginate: { next: "ถัดไป →", previous: "← ก่อนหน้า" }
            }
        });
    });
</script>
@endpush