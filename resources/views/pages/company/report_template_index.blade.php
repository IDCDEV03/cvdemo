@section('title', 'ระบบตรวจมาตรฐานรถ : รายการแบบรายงาน')
@section('description', 'ID Drives')
@extends('layout.app')

@section('content')
<div class="container-fluid">
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="breadcrumb-main d-flex justify-content-between align-items-center mb-3">
                <h4 class="text-capitalize breadcrumb-title">จัดการแบบรายงาน (Report Templates)</h4>
                <a href="{{ route('company.report_template.create') }}" class="btn btn-primary btn-default btn-squared">
                    <i class="fas fa-plus"></i> สร้างแบบรายงาน
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-25">
                <div class="card-header">
                    <span class="fs-18">รายการแบบรายงานทั้งหมด</span>
                </div>
                <div class="card-body">
                    
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <div class="alert-content">
                                {{ session('success') }}
                            </div>
                            <button type="button" class="btn-close text-capitalize" data-bs-dismiss="alert" aria-label="Close">
                                <img src="{{ asset('img/svg/x.svg') }}" alt="x" class="svg" aria-hidden="true">
                            </button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <div class="alert-content">
                                {{ session('error') }}
                            </div>
                            <button type="button" class="btn-close text-capitalize" data-bs-dismiss="alert" aria-label="Close">
                                <img src="{{ asset('img/svg/x.svg') }}" alt="x" class="svg" aria-hidden="true">
                            </button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered mb-0" id="report-templates-table">
                            <thead>
                                <tr class="userDatatable-header bg-light">
                                    <th width="5%" class="text-center"><span class="fs-14">#</span></th>
                                    <th width="35%"><span class="fs-14">ชื่อแบบรายงาน</span></th>
                                    <th width="15%" class="text-center"><span class="fs-14">ตัวแปรกำหนดเอง</span></th>
                                    <th width="15%" class="text-center"><span class="fs-14">ส่วนประกอบ</span></th>
                                    <th width="15%" class="text-center"><span class="fs-14">สถานะ</span></th>
                                    <th width="15%" class="text-center"><span class="fs-14">จัดการ</span></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($templates as $item)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td>
                                            <strong>{{ $item->template_name }}</strong>
                                            <div class="text-muted small mt-1">
                                                สร้างเมื่อ: {{ \Carbon\Carbon::parse($item->created_at)->addYears(543)->format('d/m/Y H:i') }}
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            @if($item->field_count > 0)
                                               {{ $item->field_count }} ฟิลด์
                                            @else
                                                <span class="text-muted small">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if(!empty($item->header_html)) 
                                                <span class="text-success mx-1" title="มีหัวรายงาน">ส่วนหัว</span> 
                                            @endif
                                            @if(!empty($item->footer_html)) 
                                                <span class="text-info mx-1" title="มีท้ายรายงาน">ส่วนท้าย</span> 
                                            @endif
                                            @if(empty($item->header_html) && empty($item->footer_html))
                                                <span class="text-muted small">ไม่มี</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($item->is_active == 1)
                                                <span class="badge bg-success rounded-pill">เปิดใช้งาน</span>
                                            @else
                                                <span class="badge bg-danger rounded-pill">ปิดใช้งาน</span>
                                            @endif
                                        </td>
                                        <td>
                                            <ul class="orderEdit-wrap d-flex justify-content-center mb-0">
                                                <li>
                                                    <a href="{{ route('company.report_template.show', $item->id) }}" class="view" title="ดูรายละเอียด">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </li>
                                                <li class="mx-2">
                                                    <a href="#" class="edit" title="แก้ไข (ยังไม่เปิดใช้งาน)">
                                                        <i class="fas fa-edit text-muted"></i>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#" class="remove" title="ลบ (ยังไม่เปิดใช้งาน)">
                                                        <i class="fas fa-trash-alt text-muted"></i>
                                                    </a>
                                                </li>
                                            </ul>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-5">
                                            <i class="fas fa-file-alt text-light mb-3" style="font-size: 40px;"></i><br>
                                            ยังไม่มีข้อมูลแบบรายงาน<br>
                                         
                                        </td>
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