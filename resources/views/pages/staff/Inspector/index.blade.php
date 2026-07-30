@section('title', 'รายการรถ')
@section('description', 'ID Drives')
@extends('layout.app')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<style>
    .inspector-table th {
        font-size: 16px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #6c757d;
        background: #f8f9fa;
        border-bottom: 2px solid #e9ecef;
        white-space: nowrap;
        vertical-align: middle;
    }
    .inspector-table td {
        vertical-align: middle;
        font-size: 14px;
    }
    .inspector-table tbody tr:hover {
        background-color: #f0f6ff;
    }
    .ins-name {
        font-weight: 600;
        color: #1a1a2e;
        line-height: 1.3;
    }
    .ins-id {
        font-family: monospace;
        font-size: 11px;
        color: #aaa;
    }
    .type-badge {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
    }
    .type-company  { background: #e8f4fd; color: #1677ff; }
    .type-supply   { background: #e6fff0; color: #12b76a; }
    .type-outsource{ background: #fff7e6; color: #fa8c16; }
    .action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        border: none;
        background: transparent;
        transition: background 0.15s;
        cursor: pointer;
        text-decoration: none;
        color: inherit;
    }
    .action-btn:hover { background: #f0f0f0; }
    .action-btn.danger:hover { background: #fff0f0; }
    .action-btn i { font-size: 17px; }
</style>
@endpush

@section('content')
@php use App\Enums\Role; $role = Auth::user()->role; @endphp
<div class="container-fluid">

    <div class="row mb-3">
        <div class="col-12">
            <div class="breadcrumb-main user-member justify-content-sm-between">
                <div class="d-flex align-items-center user-member__title">
                    <h4 class="text-capitalize fw-500 breadcrumb-title">จัดการข้อมูลช่างตรวจ</h4>
                </div>
                <a href="{{ route('staff.inspectors.create') }}" class="btn btn-primary">
                    <i class="uil uil-plus me-1"></i> เพิ่มช่างใหม่
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
        <strong>สำเร็จ!</strong> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
        <strong>เกิดข้อผิดพลาด!</strong> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card" style="border-radius:14px; box-shadow:0 2px 12px rgba(0,0,0,0.07); border:none;">
        <div class="card-body p-3">
            <div class="table-responsive">
                <table class="table inspector-table mb-0" id="inspector-table">
                    <thead>
                        <tr>
                            <th class="text-center ps-3" style="width:50px;">#</th>
                            <th>ชื่อ - นามสกุล</th>
                            <th>เบอร์โทร</th>
                            @if($role !== Role::Company)
                            <th>บริษัทฯว่าจ้าง</th>
                            @endif
                            <th>ประเภทช่าง / สังกัด</th>
                            <th class="text-center">สถานะ</th>
                            <th class="text-center pe-3" style="width:100px;">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($inspectors as $key => $ins)
                        <tr>
                            <td class="text-center ps-3 text-muted">{{ $key + 1 }}</td>

                            <td>
                                <div class="ins-name">{{ $ins->ins_prefix }}{{ $ins->ins_name }} {{ $ins->ins_lastname }}</div>
                            </td>

                            <td>
                                @if($ins->ins_phone)
                                    <a href="tel:{{ $ins->ins_phone }}" class="text-decoration-none text-dark">
                                        <i class="uil uil-phone text-muted me-1"></i>{{ $ins->ins_phone }}
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            @if($role !== Role::Company)
                            <td>
                                @if($ins->company_name)
                                    <i class="uil uil-building text-muted me-1"></i>{{ $ins->company_name }}
                                @else
                                    <span class="text-muted">ไม่ระบุ</span>
                                @endif
                            </td>
                            @endif

                            <td>
                                @if($ins->inspector_type == '1')
                                    <span class="type-badge type-company">
                                        <i class="uil uil-building me-1"></i>ช่างประจำบริษัทฯ
                                    </span>
                                @elseif($ins->inspector_type == '2')
                                    <span class="type-badge type-supply">
                                        <i class="uil uil-truck me-1"></i>ช่างประจำ Supply
                                    </span>
                                    @if($ins->supply_name)
                                        <div class="mt-1" style="font-size:12px; color:#555;">
                                            <i class="uil uil-map-marker me-1"></i>{{ $ins->supply_name }}
                                        </div>
                                    @endif
                                @elseif($ins->inspector_type == '3')
                                    <span class="type-badge type-outsource">
                                        <i class="uil uil-user-arrows me-1"></i>ช่างนอกสังกัด (รับจ้าง)
                                    </span>
                                @else
                                    <span class="text-muted">ไม่ระบุ</span>
                                @endif
                            </td>

                            <td class="text-center">
                                @if($ins->ins_status == '1')
                                    <span class="badge rounded-pill bg-success bg-opacity-10 text-white px-3 py-2" style="font-size:12px;">
                                        <i class="uil uil-check-circle me-1"></i>ใช้งาน
                                    </span>
                                @else
                                    <span class="badge rounded-pill bg-danger bg-opacity-10 text-white px-3 py-2" style="font-size:12px;">
                                        <i class="uil uil-ban me-1"></i>ปิดการใช้
                                    </span>
                                @endif
                            </td>

                            <td class="text-center pe-3">
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="{{ route('staff.inspectors.edit', $ins->id) }}" class="action-btn" title="แก้ไข">
                                        <i class="uil uil-edit text-primary"></i>
                                    </a>
                                    @if($ins->ins_status == '1')
                                    <form action="{{ route('staff.inspectors.destroy', $ins->id) }}" method="POST"
                                        onsubmit="return confirm('ยืนยันการปิดใช้งานช่างตรวจ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-btn danger" title="ปิดใช้งาน">
                                            <i class="uil uil-trash-alt text-danger"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ $role === Role::Company ? 6 : 7 }}" class="text-center py-5 text-muted">
                                <i class="uil uil-user-slash fs-1 d-block mb-2"></i>
                                ไม่พบข้อมูลช่างตรวจในระบบ
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
        $('#inspector-table').DataTable({
            responsive: true,
            pageLength: 25,
            columnDefs: [
                { orderable: false, targets: -1 }
            ],
            language: {
                search: "ค้นหา:",
                lengthMenu: "แสดง _MENU_ รายการ",
                info: "แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ",
                infoEmpty: "ไม่มีข้อมูล",
                zeroRecords: "ไม่พบข้อมูลที่ค้นหา",
                paginate: { next: "ถัดไป", previous: "ก่อนหน้า" }
            }
        });
    });
</script>
@endpush