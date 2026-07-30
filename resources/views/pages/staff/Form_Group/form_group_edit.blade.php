@section('title', 'แก้ไขกลุ่มฟอร์ม')
@section('description', 'ID Drives')
@extends('layout.app')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center mt-30 mb-25">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header  d-flex justify-content-between align-items-center">
                        <span class="fw-bold fs-18 text-warning"><i class="uil uil-edit"></i> แก้ไขกลุ่มฟอร์ม</span>
                        <div class="form-check form-switch form-switch-success">
                            <input class="form-check-input" type="checkbox" id="isActive" form="editForm" name="is_active"
                                value="1" {{ old('is_active', $formGroup->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="isActive">สถานะพร้อมใช้งาน</label>
                        </div>
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

                        <form action="{{ route('staff.form-group.update', $formGroup->form_group_id) }}" method="POST">
                            @csrf
                            @method('PUT')

                        <input type="hidden" name="is_active" value="0">

                        <div class="row mb-3">
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-bold">รหัสฟอร์ม <span class="text-danger">*</span></label>
                                <input type="text" name="form_group_id" class="form-control ih-medium ip-gray radius-xs"
                                    readonly value="{{ old('form_group_id', $formGroup->form_group_id) }}">
                            </div>
                            <div class="col-md-9 mb-3">
                                <label class="form-label fw-bold">ชื่อกลุ่มฟอร์ม <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control ih-medium ip-gray radius-xs"
                                    required value="{{ old('name', $formGroup->form_group_name) }}">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">รายละเอียด</label>
                                <textarea name="description" class="form-control ip-gray radius-xs" rows="3">{{ old('description', $formGroup->description) }}</textarea>
                            </div>
                        </div>

                        <div class="border-top my-3"></div>

                        <div class="mb-3 form-check form-switch p-3  rounded radius-xs">
                            <input class="form-check-input ms-0 me-2" type="checkbox" id="isSystemDefault"
                                name="is_system_default" value="1"
                                {{ old('is_system_default', $formGroup->is_system_default) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold text-success" for="isSystemDefault">
                                เป็นฟอร์มส่วนกลาง (System Default)
                            </label>
                        </div>

                        <div class="mb-4" id="companySelectDiv">
                            <label class="form-label fw-bold">ระบุบริษัทที่ใช้งาน <span class="text-danger">*</span></label>
                            <select name="company_id" class="form-select ih-medium ip-gray radius-xs" id="companySelect">
                                <option value="">-- เลือกบริษัท --</option>
                                @foreach ($companies as $company)
                                    <option value="{{ $company->company_id }}"
                                        {{ old('company_id', $formGroup->company_id) == $company->company_id ? 'selected' : '' }}>
                                        {{ $company->company_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="border-top my-4"></div>
                        <h6 class="mb-3 text-dark fw-bold">เลือกแม่แบบประกอบร่าง (Mix & Match)</h6>

                        <div class="row p-3  rounded radius-xs mb-4">
                            <div class="col-md-4 mb-3">
                                <label class="form-label text-info">1. แม่แบบก่อนตรวจ</label>
                                <select name="pre_inspection_template_id" class="form-select ih-medium ip-gray radius-xs">
                                    <option value="">-- ไม่ใช้งาน --</option>
                                    @foreach ($preInspections as $pre)
                                        <option value="{{ $pre->id }}"
                                            {{ old('pre_inspection_template_id', $formGroup->pre_inspection_template_id) == $pre->id ? 'selected' : '' }}>
                                            {{ $pre->template_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label text-primary">2. ฟอร์มตรวจหลัก <span
                                        class="text-danger">*</span></label>
                                <select name="check_item_form_id" class="form-select ih-medium ip-gray radius-xs" required>
                                    <option value="">-- เลือกฟอร์มตรวจหลัก --</option>
                                    @foreach ($checkItems as $check)
                                        <option value="{{ $check->form_id }}"
                                            {{ old('check_item_form_id', $formGroup->check_item_form_id) == $check->form_id ? 'selected' : '' }}>
                                            {{ $check->form_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label text-success">3. แม่แบบรายงาน</label>
                                <select name="report_template_id" class="form-select ih-medium ip-gray radius-xs">
                                    <option value="">-- ไม่ใช้งาน --</option>
                                    @foreach ($reports as $report)
                                        <option value="{{ $report->id }}"
                                            {{ old('report_template_id', $formGroup->report_template_id) == $report->id ? 'selected' : '' }}>
                                            {{ $report->template_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mt-4 d-flex justify-content-end gap-2">
                            <a href="{{ route('staff.form-group.show', $formGroup->form_group_id) }}"
                                class="btn btn-light btn-default btn-squared">
                                ยกเลิก
                            </a>
                            <button type="submit" class="btn btn-warning btn-default btn-squared text-dark fw-bold">
                                <i class="uil uil-save me-1"></i> บันทึกการแก้ไข
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
                        // ไม่เคลียร์ค่า companySelect.value ตอนโหลดหน้า edit เผื่อ user กดเล่น
                    } else {
                        companyDiv.style.display = 'block';
                        companySelect.setAttribute('required', 'required');
                    }
                }

                toggleCompanySelect();
                isSystemDefault.addEventListener('change', toggleCompanySelect);
            });
        </script>
    @endpush
@endsection
