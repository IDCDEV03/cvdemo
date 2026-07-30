@section('title', 'บริษัทประกันภัย')
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
    #insTable td, #insTable th { font-size: 15px; vertical-align: middle; }
    #insTable thead th { font-size: 14px; font-weight: 700; color: #475569; }
</style>
@endpush

@section('content')
<div class="container-fluid">

    <div class="row">
        <div class="col-12">
            <div class="breadcrumb-main">
                <h5 class="breadcrumb-title">บริษัทประกันภัย</h5>
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
        <div class="tbl-header d-flex align-items-center justify-content-between">
            <span class="fw-bold text-white" style="font-size:16px;">
                <i class="uil uil-shield-check me-2"></i>รายชื่อบริษัทประกันภัย ({{ $companies->count() }} บริษัท)
            </span>
            <a href="{{ route('staff.insurance.create') }}" class="btn btn-sm btn-light fw-600">
                <i class="uil uil-plus me-1"></i> เพิ่มบริษัท
            </a>
        </div>
        <div class="card-body p-3">
            <div class="table-responsive">
                <table id="insTable" class="table table-hover mb-0" style="width:100%">
                    <thead class="table-light">
                        <tr>
                            <th style="width:44px">#</th>
                            <th>ชื่อบริษัทประกันภัย</th>
                            <th style="width:120px" class="text-center">สถานะ</th>
                            <th style="width:120px" class="text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($companies as $i => $item)
                        <tr>
                            <td class="text-muted text-center">{{ $i + 1 }}</td>
                            <td class="fw-600">{{ $item->name }}</td>
                            <td class="text-center">
                                @if ($item->is_active)
                                    <span class="dm-tag tag-success tag-transparented">ใช้งาน</span>
                                @else
                                    <span class="badge bg-secondary">ปิดใช้งาน</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex gap-1 justify-content-center">
                                    <a href="{{ route('staff.insurance.edit', $item->id) }}"
                                       class="btn btn-sm btn-outline-primary py-0 px-2" title="แก้ไข">
                                        <i class="uil uil-edit"></i>
                                    </a>
                                    <button class="btn btn-sm btn-outline-danger py-0 px-2" title="ลบ"
                                        onclick="confirmDelete('{{ $item->id }}','{{ addslashes($item->name) }}')">
                                        <i class="uil uil-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4" style="font-size:14px;">ไม่พบข้อมูลบริษัทประกันภัย</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

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
                <p class="mb-0">คุณต้องการลบบริษัท <strong id="deleteName"></strong> ใช่หรือไม่?</p>
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
    $('#insTable').DataTable({
        pageLength: 25,
        language: {
            search: 'ค้นหา:',
            lengthMenu: 'แสดง _MENU_ รายการ',
            info: 'แสดง _START_–_END_ จาก _TOTAL_ รายการ',
            paginate: { next: 'ถัดไป', previous: 'ก่อนหน้า' },
            emptyTable: 'ไม่พบข้อมูลบริษัทประกันภัย',
        },
        columnDefs: [{ orderable: false, targets: [3] }],
    });

    window.confirmDelete = function (id, name) {
        document.getElementById('deleteName').textContent = name;
        document.getElementById('deleteForm').action = '/staff/insurance/' + id;
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    };
});
</script>
@endpush