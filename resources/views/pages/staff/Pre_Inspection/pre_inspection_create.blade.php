@section('title', 'ระบบตรวจมาตรฐานรถ')
@section('description', 'ID Drives')
@extends('layout.app')
@section('content')

    <div class="container-fluid">
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card mb-25">
                    <div class="card-header">
                        <h6 class="card-title">สร้าง Templete ข้อมูลก่อนตรวจรถ</h6>
                    </div>
                    <div class="card-body">
                        @if (session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif
                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <form action="{{ route('staff.pre_inspection.store') }}" method="POST" id="templateForm">
                            @csrf

                            <div class="form-group row mb-4">
                                <div class="col-sm-3 d-flex align-items-center">
                                    <label class="col-form-label color-dark">ชื่อแบบ (Template) <span
                                            class="text-danger">*</span></label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control ih-medium ip-gray radius-xs"
                                        name="template_name" placeholder="เช่น รูปถ่ายประเมินรอบคัน" required>
                                </div>
                            </div>

                            <div class="border-top my-4"></div>
                            <h6 class="mb-3">กำหนดหัวข้อที่ต้องระบุ</h6>

                            <div id="fields_container">
                                <div class="row mb-3 field-row align-items-end">
                                    <div class="col-md-4">
                                        <label class="form-label">ชื่อหัวข้อ <span class="text-danger">*</span></label>
                                        <input type="text" name="fields[0][field_label]"
                                            class="form-control ih-medium ip-gray radius-xs"
                                            placeholder="เช่น ภาพหน้ารถ / ภาพผู้ตรวจรถ" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">ประเภทการกรอกข้อมูล</label>
                                        <select name="fields[0][field_type]" class="form-select ih-medium ip-gray radius-xs"
                                            required>
                                            <option value="text">ข้อความ</option>
                                            <option value="image">รูปภาพ</option>
                                            <option value="gps">พิกัด GPS</option>
                                            <option value="document">เอกสาร</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">การบังคับกรอก</label>
                                        <select name="fields[0][is_required]"
                                            class="form-select ih-medium ip-gray radius-xs">
                                            <option value="1">บังคับ</option>
                                            <option value="0">ไม่บังคับ</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">

                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3 mb-4">
                                <div class="col-md-12">
                                    <button type="button" id="add_field_btn" class="btn btn-info btn-sm">
                                        <i class="fas fa-plus"></i> เพิ่มหัวข้อ
                                    </button>
                                </div>
                            </div>
                            <div class="border-top my-4"></div>
                            <div class="layout-button mt-25">
                                <button type="submit" class="btn btn-lg btn-primary btn-default btn-squared">บันทึกแม่แบบ</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let fieldIndex = 1;
            const container = document.getElementById('fields_container');
            const addBtn = document.getElementById('add_field_btn');

            // ฟังก์ชันเพิ่มหัวข้อใหม่
            addBtn.addEventListener('click', function() {
                const newRow = `
                <div class="row mb-3 field-row align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">ชื่อหัวข้อ <span class="text-danger">*</span></label>
                        <input type="text" name="fields[${fieldIndex}][field_label]" class="form-control ih-medium ip-gray radius-xs" placeholder="เช่น ถ่ายรูปด้านหน้ารถ" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">ประเภทการกรอกข้อมูล</label>
                        <select name="fields[${fieldIndex}][field_type]" class="form-select ih-medium ip-gray radius-xs" required>
                            <option value="text">ข้อความ</option>
                            <option value="image">รูปภาพ</option>
                            <option value="gps">พิกัด GPS</option>
                            <option value="document">เอกสาร</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">การบังคับกรอก</label>
                        <select name="fields[${fieldIndex}][is_required]" class="form-select ih-medium ip-gray radius-xs">
                            <option value="1">บังคับ</option>
                            <option value="0">ไม่บังคับ</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                       <button type="button" class="btn btn-danger btn-default btn-squared btn-sm w-100 remove-row">
                            <i class="uil uil-trash-alt me-1"></i> ลบรายการ
                        </button>
                    </div>
                </div>
            `;
                container.insertAdjacentHTML('beforeend', newRow);
                fieldIndex++;
            });

            // ฟังก์ชันลบหัวข้อ (ใช้ Event Delegation)
            container.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-row')) {
                    // อนุญาตให้ลบได้เฉพาะถ้ามีมากกว่า 1 แถว
                    if (container.querySelectorAll('.field-row').length > 1) {
                        e.target.closest('.field-row').remove();
                    }
                }
            });
        });
    </script>
@endpush
