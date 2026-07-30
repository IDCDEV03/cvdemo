@section('title', 'รายละเอียดกลุ่มฟอร์ม')
@section('description', 'ID Drives')
@extends('layout.app')

@section('content')
<div class="container-fluid">
    <div class="row mt-4 mb-25">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="text-dark fw-bold mb-0">
                    <i class="uil uil-file-info-alt text-primary"></i> รายละเอียดกลุ่มฟอร์ม
                </h4>
                <div class="d-flex gap-2">
                    <a href="{{ route('staff.form-group.index') }}" class="btn btn-primary btn-default btn-squared btn-sm">
                        <i class="uil uil-arrow-left"></i> ย้อนกลับ
                    </a>
                    <a href="{{route('staff.form-group.edit',['id'=>$formGroup->form_group_id])}}" class="btn btn-warning btn-default btn-squared btn-sm">
                        <i class="uil uil-edit"></i> แก้ไขข้อมูล
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-5 mb-4">
                    <div class="card shadow-sm h-100 radius-xs">
                        <div class="card-header ">
                            <h6 class="mb-0 text-dark fw-bold">ข้อมูลทั่วไป</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <span class="d-block text-muted small mb-1">รหัสกลุ่มฟอร์ม </span>
                                <span class="fw-bold fs-16">{{ $formGroup->form_group_id ?? '-' }}</span>
                            </div>
                            
                            <div class="mb-3">
                                <span class="d-block text-muted small mb-1">ชื่อกลุ่มฟอร์ม</span>
                                <span class="fw-bold text-dark">{{ $formGroup->form_group_name }}</span>
                            </div>

                            <div class="mb-3">
                                <span class="d-block text-muted small mb-1">รายละเอียด</span>
                                <p class="mb-0 text-dark">{{ $formGroup->description ?: '-' }}</p>
                            </div>

                            <div class="border-top my-3"></div>

                            <div class="row">
                                <div class="col-6 mb-3">
                                    <span class="d-block text-muted small mb-1">สิทธิ์การใช้งาน</span>
                                    @if($formGroup->is_system_default)
                                        <span class="badge badge-round badge-success badge-lg"><i class="uil uil-globe"></i> ฟอร์มส่วนกลาง</span>
                                    @else
                                        <span class="badge badge-round badge-info badge-lg"><i class="uil uil-building"></i> เฉพาะบริษัท</span>
                                        <div class="mt-1 small fw-bold text-dark">{{ $formGroup->company_name }}</div>
                                    @endif
                                </div>
                                <div class="col-6 mb-3">
                                    <span class="d-block text-muted small mb-1">สถานะระบบ</span>
                                    @if($formGroup->is_active)
                                        <span class="badge badge-round badge-success badge-lg">เปิดใช้งาน</span>
                                    @else
                                        <span class="badge badge-round badge-danger badge-lg">ปิดใช้งาน</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-7 mb-4">
                    <div class="card shadow-sm h-100 radius-xs">
                        <div class="card-header ">
                            <h6 class="mb-0 text-dark fw-bold">ส่วนประกอบฟอร์ม </h6>
                        </div>
                        <div class="card-body">
                            
                            <div class="d-flex align-items-start mb-4 p-3 border rounded bg-white shadow-none">
                                <div class="icon bg-info-transparent text-info rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px;">
                                    <i class="uil uil-camera fs-20"></i>
                                </div>
                                <div>
                                    <span class="d-block text-muted small fw-bold">ส่วนที่ 1: ข้อมูลก่อนตรวจ </span>
                                    @if($formGroup->pre_inspection_template_id)
                                        <span class="fs-15 fw-bold text-dark">{{ $formGroup->pre_name }}</span>
                                    @else
                                        <span class="text-danger small"><i class="uil uil-times-circle"></i> ไม่มีการตั้งค่า (ข้ามส่วนนี้)</span>
                                    @endif
                                </div>
                            </div>

                            <div class="d-flex align-items-start mb-4 p-3 border rounded bg-white shadow-none">
                                <div class="icon bg-primary-transparent text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px;">
                                    <i class="uil uil-list-ul fs-20"></i>
                                </div>
                                <div>
                                    <span class="d-block text-muted small fw-bold">ส่วนที่ 2: ฟอร์มข้อตรวจหลัก </span>
                                    @if($formGroup->check_item_form_id)
                                        <span class="fs-15 fw-bold text-dark">{{ $formGroup->check_name }}</span>
                                        @else
                                        <span class="text-danger small"><i class="uil uil-exclamation-triangle"></i> ข้อมูลไม่สมบูรณ์</span>
                                    @endif
                                </div>
                            </div>

                            <div class="d-flex align-items-start p-3 border rounded bg-white shadow-none">
                                <div class="icon bg-success-transparent text-success rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px;">
                                    <i class="uil uil-file-check-alt fs-20"></i>
                                </div>
                                <div>
                                    <span class="d-block text-muted small fw-bold">ส่วนที่ 3: แม่แบบรายงาน </span>
                                    @if($formGroup->report_template_id)
                                        <span class="fs-15 fw-bold text-dark">{{ $formGroup->report_name }}</span>
                                    @else
                                        <span class="text-danger small"><i class="uil uil-exclamation-triangle"></i> ข้อมูลไม่สมบูรณ์</span>
                                    @endif
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>
@endsection