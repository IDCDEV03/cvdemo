@section('title', 'ระบบตรวจมาตรฐานรถ')
@section('description', 'ID Drives')
@extends('layout.app')
@section('content')

<div class="container-fluid">
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="breadcrumb-main d-flex justify-content-between align-items-center mb-3">
                <h4 class="text-capitalize breadcrumb-title">จัดการเทมเพลตก่อนตรวจรถ</h4>
                <a href="{{ route('staff.pre_inspection.create') }}" class="btn btn-primary btn-default btn-squared">
                    <i class="fas fa-plus"></i> สร้างเทมเพลตใหม่
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-25">
                <div class="card-header">
                    <h6>รายการเทมเพลตทั้งหมด</h6>
                </div>
                <div class="card-body">
                    
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered mb-0" id="templates-table">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th><span class="fs-14">#</span></th>
                                    <th><span class="fs-14">ชื่อเทมเพลต</span></th>
                                    <th><span class="fs-14">จำนวนหัวข้อ</span></th>
                                    <th><span class="fs-14">สถานะ</span></th>
                                    <th><span class="fs-14">จัดการ</span></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($templates as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td><strong>{{ $item->template_name }}</strong></td>
                                        <td>{{ $item->field_count }} หัวข้อ</td>
                                        <td>
                                            @if($item->is_active == 1)
                                                <span class="badge bg-success rounded-pill">เปิดใช้งาน</span>
                                            @else
                                                <span class="badge bg-danger rounded-pill">ปิดใช้งาน</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('staff.pre_inspection.show', $item->id) }}" class="btn btn-info btn-sm btn-squared">
                                                <i class="fas fa-eye"></i> ดูรายละเอียด
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">ยังไม่มีข้อมูลแม่แบบ กรุณาสร้างใหม่</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection