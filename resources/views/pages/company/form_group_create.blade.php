@section('title', 'ระบบตรวจมาตรฐานรถ')
@section('description', 'ID Drives')
@extends('layout.app')
@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center mt-30 mb-25">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <span class="fw-bold fs-18">สร้างกลุ่มฟอร์มใหม่</span>
                    </div>
                    <div class="card-body">
                        <form action="#" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label">ชื่อกลุ่มฟอร์ม <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" required
                                    placeholder="เช่น ชุดตรวจรถขุด (สมบูรณ์)">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">รายละเอียด</label>
                                <textarea name="description" class="form-control" rows="3"></textarea>
                            </div>

                            <div class="border-top my-3"></div>
                            <div class="mb-3 form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="isSystemDefault"
                                    name="is_system_default" value="1">
                                <label class="form-check-label fw-bold text-success" for="isSystemDefault">เป็นฟอร์มส่วนกลาง
                                    (ให้ทุกบริษัทใช้งานได้)</label>
                            </div>

                            <div class="mb-3" id="companySelectDiv">
                                <label class="form-label">ระบุบริษัทที่ใช้งาน <span class="text-danger">*</span></label>
                                <select name="company_id" class="form-select" id="companySelect">
                                    <option value="">-- เลือกบริษัท --</option>
                                    @foreach ($companies as $company)
                                        <option value="{{ $company->company_id }}">{{ $company->company_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="border-top my-3"></div>
                            <h6 class="mb-3 text-primary">เลือกแม่แบบประกอบร่าง</h6>

                            <div class="mb-3">
                                <label class="form-label">1. แม่แบบก่อนตรวจ (Pre-inspection)</label>
                                <select name="pre_inspection_template_id" class="form-select">
                                    <option value="">-- ไม่ใช้งาน --</option>
                                    @foreach ($preInspections as $pre)
                                        <option value="{{ $pre->company_id }}">{{ $pre->template_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">2. ฟอร์มตรวจหลัก (Check Items) <span
                                        class="text-danger">*</span></label>
                                <select name="check_item_group_id" class="form-select" required>
                                    <option value="">-- เลือกฟอร์มตรวจหลัก --</option>
                                    @foreach ($checkItems as $check)
                                        <option value="{{ $check->user_id }}">{{ $check->form_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">3. แม่แบบรายงาน (Report Template)</label>
                                <select name="report_template_id" class="form-select">
                                    <option value="">-- ไม่ใช้งาน --</option>
                                    @foreach ($reports as $report)
                                        <option value="{{ $report->company_id }}">{{ $report->template_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mt-4 d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="uil uil-save"></i> บันทึกกลุ่มฟอร์ม
                                </button>
                                <a href="{{ route('company.form-groups.index') }}" class="btn btn-light">
                                    ยกเลิก
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // สคริปต์ซ่อน/แสดงช่องเลือกบริษัท
        document.getElementById('isSystemDefault').addEventListener('change', function() {
            const companyDiv = document.getElementById('companySelectDiv');
            const companySelect = document.getElementById('companySelect');
            if (this.checked) {
                companyDiv.style.display = 'none';
                companySelect.removeAttribute('required');
                companySelect.value = '';
            } else {
                companyDiv.style.display = 'block';
                companySelect.setAttribute('required', 'required');
            }
        });
    </script>
@endsection
