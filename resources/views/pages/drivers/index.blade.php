@php $userRole = Auth::user()->role->value; @endphp
@section('title', 'พนักงานขับรถ')
@section('description', 'ID Drives')
@extends('layout.app')
@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<style>
    .tbl-header {
        background: linear-gradient(135deg, #0ea5e9 0%, #38bdf8 100%);
        padding: 16px 22px;
        border-radius: 10px 10px 0 0;
    }
    .driver-name { font-weight: 700; color: #1e293b; font-size: 16px; }
    .supply-chip {
        display: inline-block; background: #ede9fe; color: #5840ff;
        font-size: 13px; font-weight: 600; padding: 2px 10px; border-radius: 20px;
    }
    .car-chip {
        display: inline-block; background: #e0f2fe; color: #0369a1;
        font-size: 13px; font-weight: 600; padding: 2px 10px; border-radius: 20px;
    }
    #driverTable td, #driverTable th { font-size: 16px; vertical-align: middle; }
    #driverTable thead th { font-size: 14px; font-weight: 700; color: #475569; }
</style>
@endpush

@section('content')
<div class="container-fluid">

    <div class="row">
        <div class="col-12">
            <div class="breadcrumb-main">
                <h5 class="breadcrumb-title">พนักงานขับรถ</h5>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="uil uil-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="uil uil-exclamation-triangle me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm mb-20">
        <div class="tbl-header d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="fw-bold text-white" style="font-size:16px;">
                    <i class="uil uil-user-md me-2"></i>
                    @if ($trainingFilter === 'passed')
                        ผ่านการอบรมปี {{ now()->year + 543 }}
                    @elseif ($trainingFilter === 'no_data')
                        ไม่มีข้อมูลการอบรมปี {{ now()->year + 543 }}
                    @else
                        รายชื่อพนักงานขับรถทั้งหมด
                    @endif
                    ({{ $drivers->count() }} คน)
                </span>
                @if ($trainingFilter === 'passed')
                    <span class="dm-tag tag-success" style="font-size:13px;">
                       ผ่านการอบรม
                    </span>
                    <a href="{{ route('drivers.index') }}" class="btn btn-sm btn-outline-dark py-0 px-2" style="font-size:13px;">
                        <i class="uil uil-times me-1"></i>ล้างตัวกรอง
                    </a>
                @elseif ($trainingFilter === 'no_data')
                    <span class="dm-tag tag-danger" style="font-size:13px;">
                       ไม่มีข้อมูล
                    </span>
                    <a href="{{ route('drivers.index') }}" class="btn btn-sm btn-outline-dark py-0 px-2" style="font-size:13px;">
                        <i class="uil uil-times me-1"></i>ล้างตัวกรอง
                    </a>
                @endif
            </div>
            @if ($userRole !== 'supply')
            <a href="{{ route('drivers.create') }}" class="btn btn-sm btn-light fw-600">
                <i class="uil uil-plus me-1"></i> เพิ่มพนักงานขับรถ
            </a>
            @endif
        </div>
        <div class="card-body p-3">
            <div class="table-responsive">
                <table id="driverTable" class="table table-hover mb-0" style="width:100%">
                    <thead class="table-light">
                        <tr>
                            <th style="width:44px">#</th>
                            <th>ชื่อ-นามสกุล</th>
                            <th>เลขที่บัตรประชาชน</th>
                            <th>สังกัด</th>
                            <th>เลขที่ใบขับขี่</th>
                            <th>ทะเบียนรถ</th>
                            <th style="width:130px" class="text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($drivers as $i => $item)
                        <tr>
                            <td class="text-muted text-center">{{ $i + 1 }}</td>
                            <td>
                                <span class="driver-name">{{ $item->prefix }}{{ $item->name }} {{ $item->lastname }}</span>
                            </td>
                            <td class="text-muted">{{ $item->id_card_no ?? '-' }}</td>
                            <td>
                                @if ($item->supply_name)
                                    <span class="supply-chip">{{ $item->supply_name }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-muted">{{ $item->driver_license_no ?? '-' }}</td>
                            <td>
                                @if ($item->assigned_car_id)
                                    <span class="car-chip"><i class="uil uil-car me-1"></i>{{ $item->assigned_car_id }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex gap-1 justify-content-center">
                                    <a href="{{ route('drivers.show', $item->driver_id) }}"
                                       class="btn btn-sm btn-outline-secondary py-0 px-2" title="รายละเอียด">
                                        <i class="uil uil-eye"></i>
                                    </a>
                                    @if ($userRole !== 'supply')
                                    <a href="{{ route('drivers.edit', $item->driver_id) }}"
                                       class="btn btn-sm btn-outline-primary py-0 px-2" title="แก้ไข">
                                        <i class="uil uil-edit"></i>
                                    </a>
                                    @endif
                                    @if ($userRole === 'staff')
                                    <button class="btn btn-sm btn-outline-danger py-0 px-2" title="ลบ"
                                        onclick="confirmDelete('{{ $item->driver_id }}','{{ addslashes($item->prefix . $item->name . ' ' . $item->lastname) }}')">
                                        <i class="uil uil-trash-alt"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4" style="font-size:14px;">ไม่พบข้อมูลพนักงานขับรถ</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

{{-- Delete modal --}}
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h6 class="modal-title fw-bold text-danger" style="font-size:15px;">
                    <i class="uil uil-trash-alt me-1"></i>ยืนยันการลบ
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="font-size:14px;">
                <p class="mb-0">คุณต้องการลบข้อมูลพนักงาน <strong id="deleteName"></strong> ใช่หรือไม่?</p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">ยกเลิก</button>
                <form id="deleteForm" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">ลบ</button>
                </form>
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
    @if ($drivers->count() === 0) return; @endif
    $('#driverTable').DataTable({
        pageLength: 25,
        language: {
            search: 'ค้นหา:',
            lengthMenu: 'แสดง _MENU_ รายการ',
            info: 'แสดง _START_–_END_ จาก _TOTAL_ รายการ',
            paginate: { next: 'ถัดไป', previous: 'ก่อนหน้า' },
            emptyTable: 'ไม่พบข้อมูลพนักงานขับรถ',
        },
        columnDefs: [{ orderable: false, targets: [6] }],
    });

    window.confirmDelete = function (id, name) {
        document.getElementById('deleteName').textContent = name;
        document.getElementById('deleteForm').action = '/drivers/' + id;
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    };
});
</script>
@endpush