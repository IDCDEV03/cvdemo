@section('title', 'สร้างกลุ่มฟอร์ม')
@section('description', 'ID Drives')
@extends('layout.app')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center mt-30 mb-25">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header ">
                        <span class="fw-bold fs-18 text-primary"><i class="uil uil-layer-group"></i> สร้างกลุ่มฟอร์มใหม่</span>
                    </div>
                    <div class="card-body">
                        
                        @if ($errors->any())
                            <div class="alert alert-danger radius-xs">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('staff.form-group.store') }}" method="POST">
                            @csrf

                            <div class="row mb-3">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label fw-bold">ชื่อกลุ่มฟอร์ม <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control ih-medium ip-gray radius-xs" required
                                        placeholder="เช่น ชุดตรวจรถขุด (สมบูรณ์)" value="{{ old('name') }}">
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">รายละเอียด</label>
                                    <textarea name="description" class="form-control ip-gray radius-xs" rows="3">{{ old('description') }}</textarea>
                                </div>
                            </div>

                            <div class="border-top my-3"></div>
                            
                            <div class="mb-3 form-check form-switch p-3  rounded radius-xs">
                                <input class="form-check-input ms-0 me-2" type="checkbox" id="isSystemDefault"
                                    name="is_system_default" value="1" {{ old('is_system_default') ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold text-success" for="isSystemDefault">
                                    เป็นฟอร์มส่วนกลาง (ให้ทุกบริษัทสามารถดึงไปใช้งานได้)
                                </label>
                            </div>

                            <div class="mb-4" id="companySelectDiv">
                                <label class="form-label fw-bold">ระบุบริษัทที่ใช้งาน <span class="text-danger">*</span></label>
                                <select name="company_id" class="form-select ih-medium ip-gray radius-xs" id="companySelect" required>
                                    <option value="">-- เลือกบริษัท --</option>
                                    @foreach ($companies as $company)
                                        <option value="{{ $company->company_id }}" {{ old('company_id') == $company->company_id ? 'selected' : '' }}>
                                            {{ $company->company_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="border-top my-4"></div>
                            <h6 class="mb-3 text-dark fw-bold">เลือกแม่แบบประกอบร่าง (Mix & Match)</h6>

                            <div class="row p-3 rounded radius-xs mb-4">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label text-info">1. แม่แบบก่อนตรวจ (Pre-inspection)</label>
                                    <select name="pre_inspection_template_id" class="form-select ih-medium ip-gray radius-xs">
                                        <option value="">-- ไม่ใช้งาน --</option>
                                        @foreach ($preInspections as $pre)
                                            <option value="{{ $pre->id }}" {{ old('pre_inspection_template_id') == $pre->id ? 'selected' : '' }}>
                                                {{ $pre->template_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label text-primary">2. ฟอร์มตรวจหลัก <span class="text-danger">*</span></label>
                                    <select name="check_item_form_id" class="form-select ih-medium ip-gray radius-xs" required>
                                        <option value="">-- เลือกฟอร์มตรวจหลัก --</option>
                                        @foreach ($checkItems as $check)
                                            <option value="{{ $check->form_id }}" {{ old('check_item_form_id') == $check->id ? 'selected' : '' }}>
                                                {{ $check->form_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label text-success">3. แม่แบบรายงาน (Report)</label>
                                    <select name="report_template_id" class="form-select ih-medium ip-gray radius-xs">
                                        <option value="">-- ไม่ใช้งาน --</option>
                                        @foreach ($reports as $report)
                                            <option value="{{ $report->id }}" {{ old('report_template_id') == $report->id ? 'selected' : '' }}>
                                                {{ $report->template_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="mt-4 d-flex justify-content-end gap-2">
                                <a href="{{ route('staff.form-group.index') }}" class="btn btn-light btn-default btn-squared">
                                    ยกเลิก
                                </a>
                                <button type="submit" class="btn btn-primary btn-default btn-squared">
                                    <i class="uil uil-save me-1"></i> บันทึกกลุ่มฟอร์ม
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const isSystemDefault = document.getElementById('isSystemDefault');
            const companyDiv = document.getElementById('companySelectDiv');
            const companySelect = document.getElementById('companySelect');

            function toggleCompanySelect() {
                if (isSystemDefault.checked) {
                    companyDiv.style.display = 'none';
                    companySelect.removeAttribute('required');
                    companySelect.value = '';
                } else {
                    companyDiv.style.display = 'block';
                    companySelect.setAttribute('required', 'required');
                }
            }

            // ทำงานตอนโหลดหน้าเว็บ (เผื่อกรณีติด Old Value กลับมา)
            toggleCompanySelect();

            // ทำงานตอนคลิก Checkbox
            isSystemDefault.addEventListener('change', toggleCompanySelect);
        });
    </script>
    @endpush
@endsection