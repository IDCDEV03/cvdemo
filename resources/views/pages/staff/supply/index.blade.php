@section('title', 'จัดการ Supply')
@section('description', 'ID Drives')
@extends('layout.app')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<style>
    .tbl-header {
        background: linear-gradient(135deg, #5840ff 0%, #7c66ff 100%);
        padding: 14px 20px;
        border-radius: 10px 10px 0 0;
    }
    .status-dot { width:9px; height:9px; border-radius:50%; display:inline-block; margin-right:5px; }
    .dot-active   { background:#22c55e; }
    .dot-inactive { background:#e2e8f0; }
    .company-chip {
        display:inline-block; background:#ede9fe; color:#5840ff;
        font-size:12px; font-weight:600; padding:2px 10px; border-radius:20px;
    }
    .sup-name { font-weight:600; color:#1e293b; }
</style>
@endpush

@section('content')
<div class="container-fluid">

    <div class="row ">
        <div class="col-12">
            <div class="breadcrumb-main">
                <h5 class="breadcrumb-title">จัดการ Supply</h5>
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
            <span class="fw-bold text-white fs-16"><i class="uil uil-building me-2"></i>รายการ Supply ทั้งหมด ({{ $supplies->count() }} รายการ)</span>
            <a href="{{ route('staff.supplies.create') }}" class="btn btn-sm btn-light fw-600">
                <i class="uil uil-plus me-1"></i> เพิ่ม Supply
            </a>
        </div>
        <div class="card-body p-3">
            <div class="table-responsive">
                <table id="supplyTable" class="table table-hover mb-0" style="width:100%">
                    <thead class="table-light">
                        <tr>
                            <th style="width:40px">#</th>
                            <th>ชื่อ Supply</th>
                            <th>บริษัทแม่</th>                       
                            <th>สถานะ</th>
                            <th style="width:120px">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($supplies as $i => $s)
                        <tr>
                            <td class="text-muted text-center">{{ $i + 1 }}</td>
                            <td>
                                <a href="{{ route('staff.supplies.show', $s->sup_id) }}"
                                   class="sup-name text-decoration-none d-inline-flex align-items-center gap-2 px-3 py-1 rounded"
                                   style="background:#F0FDF4; color:#15803D; border:1px solid #BBF7D0; font-size:14px; transition:background 0.15s ease, color 0.15s ease;"
                                   onmouseover="this.style.background='#DCFCE7';this.style.color='#166534';"
                                   onmouseout="this.style.background='#F0FDF4';this.style.color='#15803D';">
                                    {{ $s->supply_name }}
                                </a>
                            </td>
                            <td><span class="company-chip">{{ $s->company_name }}</span></td>
                         
                            <td>
                                @if ($s->supply_status == '1')
                                    <span class="status-dot dot-active"></span><span class="text-success fw-600 fs-13">ใช้งาน</span>
                                @else
                                    <span class="status-dot dot-inactive"></span><span class="text-muted fs-13">ปิดใช้งาน</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('staff.supplies.edit', $s->sup_id) }}"
                                       class="btn btn-sm btn-outline-primary py-0 px-2">แก้ไข</a>
                                    <button class="btn btn-sm btn-outline-danger py-0 px-2"
                                        onclick="confirmDelete('{{ $s->sup_id }}','{{ addslashes($s->supply_name) }}')">ลบ</button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
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
                <h6 class="modal-title fw-bold text-danger"><i class="uil uil-trash-alt me-1"></i>ยืนยันการลบ</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-1">คุณต้องการลบ Supply <strong id="deleteName"></strong> ใช่หรือไม่?</p>
                <p class="text-danger fs-13 mb-0"><i class="uil uil-exclamation-triangle me-1"></i>ไม่สามารถลบได้หากมีรถผูกอยู่กับ Supply นี้</p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">ลบ</button>
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
    $('#supplyTable').DataTable({
        order: [[2, 'asc'], [1, 'asc']],
        pageLength: 25,
        language: {
            search: 'ค้นหา:', lengthMenu: 'แสดง _MENU_ รายการ',
            info: 'แสดง _START_–_END_ จาก _TOTAL_ รายการ',
            paginate: { next: 'ถัดไป', previous: 'ก่อนหน้า' },
            emptyTable: 'ไม่พบข้อมูล Supply',
        },
        columnDefs: [{ orderable: false, targets: [4] }],
    });

    let deleteId = null;
    window.confirmDelete = function (id, name) {
        deleteId = id;
        $('#deleteName').text(name);
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    };

    $('#confirmDeleteBtn').on('click', function () {
        if (!deleteId) return;
        const btn = $(this).prop('disabled', true).text('กำลังลบ...');
        $.ajax({
            url: '/staff/supplies/' + deleteId,
            type: 'DELETE',
            data: { _token: '{{ csrf_token() }}' },
            success: function (res) {
                location.reload();
            },
            error: function (xhr) {
                bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();
                const msg = xhr.responseJSON?.message ?? 'เกิดข้อผิดพลาด';
                alert(msg);
                btn.prop('disabled', false).text('ลบ');
            }
        });
    });
});
</script>
@endpush