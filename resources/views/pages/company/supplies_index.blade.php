@section('title', 'บริษัทในเครือ')
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
        color: #fff;
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
    #supplyTable thead tr { background: #f8faff; }
    #supplyTable thead th {
        font-size: 16px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.07em;
        color: #646e7a;
        border-bottom: 1px solid #e9ecef;
        border-top: none;
        padding: 14px 16px;
        white-space: nowrap;
        vertical-align: middle;
    }
    #supplyTable tbody td {
        padding: 14px 16px;
        vertical-align: middle;
        border-color: #f1f5f9;
        font-size: 14px;
    }
    #supplyTable tbody tr { transition: background .12s; }
    #supplyTable tbody tr:hover { background: #f0f6ff; }

    .supply-logo {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        object-fit: cover;
        border: 1px solid #e9ecef;
    }
    .supply-logo-placeholder {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        background: #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #cbd5e1;
        font-size: 22px;
    }
    .supply-name-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-weight: 700;
        font-size: 15px;
        color: #1677ff;
        text-decoration: none;
        border-bottom: 1.5px dashed #93c5fd;
        padding-bottom: 1px;
        transition: color .15s, border-color .15s;
    }
    .supply-name-link .link-arrow {
        font-size: 16px;
        transition: transform .2s;
        color: #93c5fd;
    }
    .supply-name-link:hover { color: #0958d9; border-bottom-color: #1677ff; }
    .supply-name-link:hover .link-arrow { transform: translateX(4px); color: #1677ff; }
   
    .veh-count-chip {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: #f0f6ff;
        border: 1px solid #d0e4ff;
        color: #1677ff;
        border-radius: 20px;
        padding: 4px 12px;
        font-size: 15px;
        font-weight: 600;
    }
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border-radius: 20px;
        padding: 5px 13px;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
    }
    .status-dot { width: 7px; height: 7px; border-radius: 50%; flex-shrink: 0; }
    .status-active { background: #f0fdf4; color: #15803d; }
    .status-active .status-dot { background: #22c55e; box-shadow: 0 0 0 3px rgba(34,197,94,.2); }
    .status-inactive { background: #fef2f2; color: #b91c1c; }
    .status-inactive .status-dot { background: #ef4444; box-shadow: 0 0 0 3px rgba(239,68,68,.2); }

    .action-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border-radius: 8px;
        padding: 6px 14px;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        border: 1px solid #d0e4ff;
        background: #f0f6ff;
        color: #1677ff;
        transition: background .15s;
    }
    .action-btn:hover { background: #dbeafe; color: #1677ff; }
</style>
@endpush

@section('content')
<div class="container-fluid">

    {{-- Page Header --}}
    <div class="page-header-card d-flex align-items-center justify-content-between flex-wrap gap-2 mt-20">
        <div>
            <h4><i class="uil uil-building me-2"></i>บริษัทในเครือ</h4>
            <div class="sub">รายการ Supply ทั้งหมด</div>
        </div>
        <span class="stat-pill">
            <i class="uil uil-list-ul"></i>
            ทั้งหมด {{ count($supplies) }} แห่ง
        </span>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
        <strong>สำเร็จ!</strong> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Table Card --}}
    <div class="card table-card mb-25">
        <div class="card-body mt-20 p-20">
            <div class="table-responsive">
                <table class="table mb-0" id="supplyTable">
                    <thead>
                        <tr>
                            <th class="text-center" style="width:80px;">โลโก้</th>
                            <th>ชื่อบริษัทในเครือ</th>
                            <th class="text-center">จำนวนรถ</th>
                            <th class="text-center">สถานะ</th>
                            <th class="text-center" style="width:100px;">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($supplies as $item)
                        <tr>
                            <td class="text-center">
                                @if($item->supply_logo)
                                    <img src="{{ asset($item->supply_logo) }}" class="supply-logo" alt="{{ $item->supply_name }}">
                                @else
                                    <div class="supply-logo-placeholder mx-auto">
                                        <i class="uil uil-building"></i>
                                    </div>
                                @endif
                            </td>

                            <td>
                                <a href="{{ route('company.supplies.show', $item->sup_id) }}" class="supply-name-link">
                                    {{ $item->supply_name }}
                                    <i class="uil uil-arrow-right link-arrow"></i>
                                </a>
                            </td>

                            <td class="text-center">
                                <span class="veh-count-chip">
                                    <i class="uil uil-car"></i>
                                    {{ $item->total_vehicles ?? 0 }} คัน
                                </span>
                            </td>

                            <td class="text-center">
                                @if($item->supply_status == '1')
                                    <span class="status-badge status-active">
                                        <span class="status-dot"></span>เปิดใช้งาน
                                    </span>
                                @else
                                    <span class="status-badge status-inactive">
                                        <span class="status-dot"></span>ปิดใช้งาน
                                    </span>
                                @endif
                            </td>

                            <td class="text-center">
                                <a href="{{ route('company.supplies.show', $item->sup_id) }}" class="action-btn">
                                    <i class="uil uil-eye"></i> ดูข้อมูล
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div style="color:#cbd5e1;">
                                    <i class="uil uil-building-slash" style="font-size:48px; display:block; margin-bottom:8px;"></i>
                                    <div style="font-size:15px; font-weight:600; color:#94a3b8;">ไม่พบข้อมูลบริษัทในเครือ</div>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function () {
        $('#supplyTable').DataTable({
            responsive: true,
            order: [[1, 'asc']],
            columnDefs: [
                { orderable: false, targets: [0, 4] }
            ],
            pageLength: 25,
            language: {
                search: "ค้นหา:",
                searchPlaceholder: "พิมพ์เพื่อค้นหา...",
                lengthMenu: "แสดง _MENU_ รายการ",
                info: "แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ แห่ง",
                infoEmpty: "ไม่มีข้อมูล",
                zeroRecords: "ไม่พบข้อมูลที่ค้นหา",
                paginate: { next: "ถัดไป →", previous: "← ก่อนหน้า" }
            }
        });
    });
</script>
@endpush