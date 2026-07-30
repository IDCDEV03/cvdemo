@section('title', 'ระบบตรวจมาตรฐานรถ')
@section('description', 'ID Drives')
@extends('layout.app')
@section('content')
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">

                <div class="card card-default card-md mb-4 mt-10">
                    <div class="card-header d-flex flex-wrap align-items-center justify-content-between">
                        <div>
                            <h4 class="mb-0">รายการบริษัทฯ ว่าจ้าง</h4>
                            <span class="text-muted">จัดการข้อมูลบริษัทว่าจ้างภายใต้การดูแลของคุณ</span>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ route('companies.create') }}" class="btn btn-primary">
                                <i class="las la-plus"></i> เพิ่มบริษัท
                            </a>
                        </div>
                    </div>

                    <div class="card-body">

                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @elseif(session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif


                        <form method="GET" action="{{ route('companies.index') }}" class="mb-4">
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <input type="text" name="keyword" class="form-control"
                                        value="{{ request('keyword') }}"
                                        placeholder="ค้นหาด้วยชื่อบริษัท / จังหวัด">
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-secondary w-100">
                                        <i class="las la-search"></i> ค้นหา
                                    </button>
                                </div>
                                <div class="col-md-2">
                                    <a href="{{ route('companies.index') }}" class="btn btn-light w-100 border">
                                        ล้างค่า
                                    </a>
                                </div>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="60">#</th>
                                        <th width="150">Logo</th>
                                        <th>ชื่อบริษัท</th>
                                        <th width="130">จังหวัด</th>
                                        <th width="120" class="text-center">Form Limit</th>
                                        <th width="130" class="text-center">สถานะ</th>
                                        <th width="120" class="text-center">เริ่มใช้งาน</th>
                                        <th width="120" class="text-center">หมดอายุ</th>
                                        <th width="180" class="text-center">จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($companies as $index => $row)
                                        <tr>
                                            <td>
                                                {{ $companies->firstItem() + $index }}
                                            </td>
                                            <td>
                                                @if ($row->company_logo)
                                                    <img src="{{ asset($row->company_logo) }}" alt="Logo"
                                                        style="max-height: 50px;">
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="fw-semibold">{{ $row->company_name }}</div>

                                            </td>
                                            <td>{{ $row->company_province ?? '-' }}</td>
                                            <td class="text-center">
                                                {{ $row->form_limit ?? 0 }}
                                            </td>
                                            <td class="text-center">
                                                @if ($row->require_user_approval == '1')
                                                    <span class="dm-tag tag-success tag-transparented fs-18">เปิด</span>
                                                @elseif($row->require_user_approval == '0')
                                                    <span
                                                        class="dm-tag tag-warning tag-transparented fs-18">รออนุมัติ</span>
                                                @elseif($row->require_user_approval == '2')
                                                    <span class="dm-tag tag-danger tag-transparented fs-18">ปิดการใช้</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                {{ $row->start_date ? date('d/m/Y', strtotime($row->start_date)) : '-' }}
                                            </td>
                                            <td class="text-center">
                                                {{ $row->expire_date ? date('d/m/Y', strtotime($row->expire_date)) : '-' }}
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-2">
                                                    <a href="{{ route('companies.edit', $row->company_id) }}"
                                                        class="btn btn-sm btn-warning">
                                                        <i class="las la-pen"></i> แก้ไข
                                                    </a>

                                                    <form action="{{ route('companies.destroy', $row->company_id) }}" method="POST" class="d-inline" onsubmit="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบบริษัทนี้? ข้อมูลผู้ใช้งานจะถูกลบไปด้วย');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger">
                                                            <i class="las la-trash"></i> ลบ
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center text-muted py-4">
                                                ยังไม่พบข้อมูลบริษัท
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if ($companies->hasPages())
                            <div class="mt-4">
                                {{ $companies->links() }}
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
        $(document).ready(function() {
            $('#forms-table').DataTable({
                responsive: true,
                pageLength: 10,
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
