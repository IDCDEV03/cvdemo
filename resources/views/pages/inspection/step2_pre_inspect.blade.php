@section('title', 'บันทึกข้อมูลก่อนตรวจ')
@section('description', 'ID Drives - ระบบตรวจมาตรฐานรถ')
@extends('layout.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/custom-mobile.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
    <style>
        .photo-card {
            border: 2px dashed #E3E6EF !important;
            transition: all 0.2s;
        }

        .photo-card.has-photo {
            border-style: solid !important;
            border-color: ข้อมูลก่อนตรวจรถs #20C997 !important;
            background: #fff !important;
        }
    </style>
@endpush

{{-- PDF preview modal (shared across all document fields) --}}
<div class="modal fade" id="pdfPreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content" style="height:90vh;">
            <div class="modal-header py-2 px-3">
                <h6 class="modal-title mb-0 text-truncate" id="pdfPreviewTitle"></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0" style="flex:1; overflow:hidden;">
                <iframe id="pdfPreviewFrame" src="" style="width:100%; height:100%; border:none;"></iframe>
            </div>
        </div>
    </div>
</div>

@section('content')
    <div class="container-fluid py-3">
        <div class="row justify-content-center mt-30 mb-25">
            <div class="col-12 col-md-8 col-lg-6">

                <div class="d-flex align-items-center mb-4 mt-2">
                    <div class="bg-info text-white rounded-circle d-flex justify-content-center align-items-center me-3 shadow-sm"
                        style="width: 45px; height: 45px;">
                        <i class="uil uil-clipboard-notes fs-20"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold text-dark">ข้อมูลก่อนตรวจรถ</h5>
                        <span class="small text-muted">ขั้นตอนที่ 2 : กรอกข้อมูลก่อนตรวจรถ</span>
                    </div>
                </div>

                <form action="{{ route('inspection.storeStep2', $record->record_id) }}" method="POST"
                    enctype="multipart/form-data" id="step2Form">
                    @csrf
                    @if($previousPreRecordId)
                        <input type="hidden" name="previous_record_id" value="{{ $previousPreRecordId }}">
                    @endif

                    {{-- ============================================== --}}
                    {{-- Card 1: Update vehicle info (tax / register / insurance) --}}                   
                    {{-- ============================================== --}}
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div>
                                    <span style="font-size: 22px; font-weight: 700;">
                                        ทะเบียนรถ: <strong class="text-primary">{{ $vehicle->car_plate ?? '-' }}</strong>
                                    </span>
                                </div>
                            </div>
   <div class="border-top mb-2"></div>
                            {{-- Trailer plate for this inspection (optional, independent from vehicle master data) --}}
                            <div class="mb-3">
                                <label class="form-label fw-bold text-dark mb-1">
                                    ทะเบียนหาง <span class="text-muted fw-normal">(ถ้ามี)</span>
                                </label>
                                <input type="text"
                                    name="trailer_plate"
                                    class="form-control radius-xs"                                 
                                    value="{{ $record->trailer_plate ?? ($vehicle->car_trailer_plate ?? '') }}"
                                    maxlength="50">
                                <small class="text-muted">ทะเบียนหางที่ใช้ในการตรวจครั้งนี้</small>
                            </div>
                            <div class="border-top mb-3"></div>

                            <div class="row g-3">
                                {{--  Car brand --}}
                                <div class="col-12">
                                    <label class="form-label fw-bold text-dark mb-1">ยี่ห้อรถ</label>
                                    <select name="vehicle_info[car_brand]" id="step2_car_brand" class="form-control">
                                        <option value="">-- ไม่ระบุ --</option>
                                        @foreach ($carBrands as $brand)
                                            <option value="{{ $brand->brand_name }}"
                                                {{ ($vehicle->car_brand ?? '') === $brand->brand_name ? 'selected' : '' }}>
                                                {{ $brand->brand_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">
                                        ข้อมูลล่าสุด: <strong class="text-dark">{{ $vehicle->car_brand ?? 'ไม่มีข้อมูล' }}</strong>
                                    </small>
                                </div>
<div class="border-top"></div>
                                {{-- Car model  --}}
                                <div class="col-12">
                                    <label class="form-label fw-bold text-dark mb-1">รุ่นรถ</label>
                                    <input type="text"
                                        name="vehicle_info[car_model]"
                                        class="form-control radius-xs"
                                        placeholder="เช่น Actros, FH500, 6x4"
                                        value="{{ old('vehicle_info.car_model', $vehicle->car_model ?? '') }}"
                                        maxlength="100">
                                    <small class="text-muted">
                                        ข้อมูลล่าสุด: <strong class="text-dark">{{ $vehicle->car_model ?? 'ไม่มีข้อมูล' }}</strong>
                                    </small>
                                </div>
<div class="border-top"></div>
                                {{-- Field 1: Car tax expire date --}}
                                <div class="col-12">
                                    <label class="form-label fw-bold text-dark mb-1">
                                       วันหมดอายุภาษี
                                    </label>
                                    <input type="text"
                                        class="form-control date-th-input radius-xs"
                                        data-hidden-id="real_car_tax"
                                        placeholder="วว/ดด/ปปปป (พ.ศ.) เช่น 11/02/2570"
                                        inputmode="numeric"
                                        maxlength="10">
                                    <input type="hidden" name="vehicle_info[car_tax]" id="real_car_tax">
                                    <div class="d-flex justify-content-between align-items-center mt-1">
                                        <small class="text-muted">
                                            ข้อมูลล่าสุด:
                                            <strong class="text-dark">
                                                {{ $vehicle->car_tax ? thai_date($vehicle->car_tax) : 'ไม่มีข้อมูล' }}
                                            </strong>
                                        </small>
                                        <div class="fs-12 date-feedback"></div>
                                    </div>
                                </div>
<div class="border-top"></div>
                                {{-- Field 2: Vehicle register date --}}
                                <div class="col-12">
                                    <label class="form-label fw-bold text-dark mb-1">
                                       วันที่จดทะเบียน
                                    </label>
                                    <input type="text"
                                        class="form-control date-th-input radius-xs"
                                        data-hidden-id="real_car_register_date"
                                        placeholder="วว/ดด/ปปปป (พ.ศ.) เช่น 15/06/2560"
                                        inputmode="numeric"
                                        maxlength="10">
                                    <input type="hidden" name="vehicle_info[car_register_date]" id="real_car_register_date">
                                    <div class="d-flex justify-content-between align-items-center mt-1">
                                        <small class="text-muted">
                                            ข้อมูลล่าสุด:
                                            <strong class="text-dark">
                                                {{ $vehicle->car_register_date ? thai_date($vehicle->car_register_date) : 'ไม่มีข้อมูล' }}
                                            </strong>
                                        </small>
                                        <div class="fs-12 date-feedback"></div>
                                    </div>
                                </div>
<div class="border-top"></div>
                                {{-- Field 3: Insurance company --}}
                                <div class="col-12">
                                    <label class="form-label fw-bold text-dark mb-1">
                                        บริษัทประกันภัย
                                    </label>
                                    <select name="vehicle_info[car_insurance_id]" class="form-select radius-xs">
                                        <option value="">-- ไม่ระบุ --</option>
                                        @foreach ($insuranceCompanies as $ins)
                                            <option value="{{ $ins->id }}"
                                                {{ $vehicle->car_insurance_id == $ins->id ? 'selected' : '' }}>
                                                {{ $ins->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="d-flex align-items-center mt-1">
                                        <small class="text-muted">
                                            ข้อมูลล่าสุด:
                                            <strong class="text-dark">
                                                {{ $vehicle->insurance_company_name ?? 'ไม่มีข้อมูล' }}
                                            </strong>
                                        </small>
                                    </div>
                                </div>
<div class="border-top"></div>
                                {{-- Field 4: Insurance expire date --}}
                                <div class="col-12">
                                    <label class="form-label fw-bold text-dark mb-1">
                                       วันที่ประกันหมดอายุ
                                    </label>
                                    <input type="text"
                                        class="form-control date-th-input radius-xs"
                                        data-hidden-id="real_car_insurance_expire"
                                        placeholder="วว/ดด/ปปปป (พ.ศ.) เช่น 20/12/2569"
                                        inputmode="numeric"
                                        maxlength="10">
                                    <input type="hidden" name="vehicle_info[car_insurance_expire]" id="real_car_insurance_expire">
                                    <div class="d-flex justify-content-between align-items-center mt-1">
                                        <small class="text-muted">
                                            ข้อมูลล่าสุด:
                                            <strong class="text-dark">
                                                {{ $vehicle->car_insurance_expire ? thai_date($vehicle->car_insurance_expire) : 'ไม่มีข้อมูล' }}
                                            </strong>
                                        </small>
                                        <div class="fs-12 date-feedback"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ============================================== --}}
                    {{-- Card 2: Pre-inspection fields (existing - unchanged) --}}
                    {{-- ============================================== --}}
                    <div class="card">
                        <div class="card-body">
                            <div class="row g-3 mb-4">

                                @php $imgIndex = 1; @endphp

                                @foreach ($preFields as $field)
                                    @php $prevResult = $previousPreData->get($field->id); @endphp

                                    @if ($field->field_type == 'image')
                                        @if ($imgIndex <= 8)
                                            @php
                                                $hasPrevImg = $prevResult && $prevResult->field_value;
                                                $prevImgUrl = $hasPrevImg ? asset($prevResult->field_value) : null;
                                            @endphp
                                            <div class="col-6">
                                                <div class="card shadow-none radius-xs h-100 photo-card {{ $hasPrevImg ? 'has-photo' : '' }}"
                                                    id="card_{{ $field->id }}">
                                                    <div class="card-body p-2 text-center position-relative d-flex flex-column align-items-center justify-content-center"
                                                        style="min-height: 140px;">
                                                        <span
                                                            class="badge bg-success position-absolute top-0 end-0 mt-2 me-2 {{ $hasPrevImg ? '' : 'd-none' }}"
                                                            id="badge_{{ $field->id }}"><i class="uil uil-check"></i></span>
                                                        <label for="input_{{ $field->id }}" class="w-100 h-100 m-0">
                                                            <img id="preview_{{ $field->id }}"
                                                                src="{{ $prevImgUrl ?? '' }}"
                                                                class="img-fluid rounded {{ $hasPrevImg ? '' : 'd-none' }}"
                                                                style="width: 100%; height: 120px; object-fit: cover;">
                                                            <div id="content_{{ $field->id }}" class="{{ $hasPrevImg ? 'd-none' : '' }}">
                                                                <i class="uil uil-camera-plus fs-32 text-primary"></i>
                                                                <span class="fw-bold text-dark fs-14 d-block">{{ $field->field_label }}</span>
                                                            </div>
                                                        </label>
                                                        @if($hasPrevImg)
                                                            <small class="text-muted mt-1" style="font-size:11px;">
                                                                <i class="uil uil-history"></i> รูปจากรอบก่อน (กดเพื่อเปลี่ยน)
                                                            </small>
                                                            <input type="hidden" name="keep_photos[{{ $field->id }}]" value="1" id="keep_photo_{{ $field->id }}">
                                                        @endif
                                                        <input type="file" name="photos[{{ $field->id }}]"
                                                            id="input_{{ $field->id }}" class="d-none file-input"
                                                            accept="image/*" capture="environment"
                                                            data-id="{{ $field->id }}"
                                                            {{ (!$hasPrevImg && $field->is_required) ? 'required' : '' }}>
                                                    </div>
                                                </div>
                                            </div>
                                            @php $imgIndex++; @endphp
                                        @endif

                                    @elseif($field->field_type == 'text')
                                        <div class="col-12 mt-3">
                                            <label class="form-label fw-bold text-dark mb-1">{{ $field->field_label }}
                                                {!! $field->is_required ? '<span class="text-danger">*</span>' : '' !!}</label>
                                            <input type="text" name="fields[{{ $field->id }}]"
                                                class="form-control radius-xs"
                                                placeholder="กรอก{{ $field->field_label }}..."
                                                value="{{ $prevResult->field_value ?? '' }}"
                                                {{ $field->is_required ? 'required' : '' }}>
                                        </div>

                                    @elseif($field->field_type == 'gps')
                                        <div class="col-12 mt-3">
                                            <label class="form-label fw-bold text-dark mb-2">
                                                {{ $field->field_label }} {!! $field->is_required ? '<span class="text-danger">*</span>' : '' !!}
                                            </label>
                                            <button
                                                class="btn btn-outline-primary get-gps-btn radius-xs w-100 mb-2 py-2 shadow-none"
                                                type="button" data-id="{{ $field->id }}">
                                                <i class="uil uil-map-marker"></i> กดเพื่อบันทึกพิกัดปัจจุบัน
                                            </button>
                                            <input type="text" name="fields[{{ $field->id }}]"
                                                id="gps_{{ $field->id }}"
                                                class="form-control radius-xs bg-light border-0" readonly
                                                placeholder="พิกัดจะปรากฏที่นี่อัตโนมัติ..."
                                                value="{{ $prevResult->field_value ?? '' }}"
                                                {{ $field->is_required ? 'required' : '' }}>
                                        </div>

                                    @elseif($field->field_type == 'document')
                                        @php
                                            $hasPrevDoc = $prevResult && $prevResult->field_value;
                                            $prevDocName = $hasPrevDoc ? basename($prevResult->field_value) : null;
                                        @endphp
                                        <div class="col-12 mt-3">
                                            <label class="form-label fw-bold text-dark mb-1">
                                                {{ $field->field_label }} {!! $field->is_required ? '<span class="text-danger">*</span>' : '' !!}
                                            </label>
                                            @if($hasPrevDoc)
                                                <div class="d-flex align-items-center gap-2 p-2 mb-2 rounded"
                                                     style="background:#f0fdf4; border:1px solid #86efac;">
                                                    <i class="uil uil-file-check-alt text-success fs-18"></i>
                                                    <div class="flex-fill" style="min-width:0;">
                                                        <div class="fw-bold text-success" style="font-size:13px;">ไฟล์จากรอบก่อน</div>
                                                        <div class="text-muted text-truncate" style="font-size:12px;">{{ $prevDocName }}</div>
                                                    </div>
                                                    <button type="button"
                                                       class="btn btn-xs btn-outline-success pdf-preview-btn"
                                                       style="font-size:12px; white-space:nowrap;"
                                                       data-pdf-url="{{ asset($prevResult->field_value) }}"
                                                       data-pdf-name="{{ $prevDocName }}">
                                                        <i class="uil uil-eye"></i> ดู
                                                    </button>
                                                    <input type="hidden" name="keep_docs[{{ $field->id }}]" value="1" id="keep_doc_{{ $field->id }}">
                                                </div>
                                                <label class="form-label text-muted mb-1" style="font-size:13px;">อัปโหลดไฟล์ใหม่เพื่อแทนที่ (ไม่บังคับ)</label>
                                            @endif
                                            <input type="file" name="docs[{{ $field->id }}]"
                                                id="doc_{{ $field->id }}"
                                                class="form-control radius-xs"
                                                accept=".pdf"
                                                {{ (!$hasPrevDoc && $field->is_required) ? 'required' : '' }}>
                                            <small class="text-muted">รองรับเฉพาะ PDF (ไม่เกิน 10 MB)</small>
                                        </div>
                                    @endif
                                @endforeach
                            </div>

                            <div class="border-top my-3"></div>
                            <div class="d-flex gap-2">
                                <button type="submit" id="btnNextStep"
                                    class="btn btn-info text-white btn-lg w-100 py-3 fw-bold radius-xs shadow-sm">
                                    ถัดไป <i class="uil uil-arrow-right ms-1"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                </form>

            </div>
        </div>
    </div>
@endsection
   @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script>
            $(document).ready(function () {
                $('#step2_car_brand').select2({
                    theme: 'bootstrap-5',
                    width: '100%',
                    placeholder: '-- เลือกยี่ห้อรถ --',
                    allowClear: true,
                    language: {
                        noResults: function () { return 'ไม่พบข้อมูล'; },
                        searching: function () { return 'กำลังค้นหา...'; }
                    }
                });
            });
        </script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // ==========================================
                // Image preview handler (existing - unchanged)
                // ==========================================
                document.querySelectorAll('.file-input').forEach(input => {
                    input.addEventListener('change', function(e) {
                        const id = this.getAttribute('data-id');
                        if (this.files[0]) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                document.getElementById('content_' + id).classList.add('d-none');
                                document.getElementById('preview_' + id).src = e.target.result;
                                document.getElementById('preview_' + id).classList.remove('d-none');
                                document.getElementById('badge_' + id).classList.remove('d-none');
                                document.getElementById('card_' + id).classList.add('has-photo');
                            }
                            reader.readAsDataURL(this.files[0]);
                            // ถ้าเลือกไฟล์ใหม่ → ไม่ต้อง keep รูปเดิมแล้ว
                            const keepInput = document.getElementById('keep_photo_' + id);
                            if (keepInput) keepInput.disabled = true;
                        }
                    });
                });

                // ==========================================
                // GPS coordinate fetcher (existing - unchanged)
                // ==========================================
                document.querySelectorAll('.get-gps-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const id = this.getAttribute('data-id');
                        const input = document.getElementById('gps_' + id);

                        this.innerHTML = '<i class="uil uil-spinner fa-spin"></i> รอพิกัด...';

                        if (navigator.geolocation) {
                            navigator.geolocation.getCurrentPosition(function(position) {
                                input.value = position.coords.latitude + ',' + position.coords.longitude;
                                btn.innerHTML = '<i class="uil uil-check"></i> สำเร็จ';
                                btn.classList.replace('btn-outline-primary', 'btn-success');
                            }, function(error) {
                                alert('ไม่สามารถดึงตำแหน่งได้ กรุณาเปิด Location Service ของมือถือครับ');
                                btn.innerHTML = '<i class="uil uil-map-marker"></i> ลองใหม่';
                            });
                        } else {
                            alert('เบราว์เซอร์นี้ไม่รองรับ GPS');
                        }
                    });
                });
            });
        </script>

        <script>
            // ==========================================
            // Thai Buddhist Date Input Handler
            // Format: dd/mm/yyyy (Buddhist Era) -> auto-converted to Y-m-d (CE) for DB
            // ==========================================
            $(document).ready(function() {

                // Helper: validate if date is real (handles Feb, leap year, 30/31 days)
                function isValidDate(day, month, yearCE) {
                    const d = new Date(yearCE, month - 1, day);
                    return d.getFullYear() === yearCE
                        && (d.getMonth() + 1) === month
                        && d.getDate() === day;
                }

                $('.date-th-input').on('input', function(e) {
                    let input = $(this).val().replace(/[^0-9]/g, '');
                    let formattedDate = '';

                    // Auto-insert slashes
                    if (input.length > 2) {
                        formattedDate += input.substring(0, 2) + '/';
                        if (input.length > 4) {
                            formattedDate += input.substring(2, 4) + '/';
                            formattedDate += input.substring(4, 8);
                        } else {
                            formattedDate += input.substring(2);
                        }
                    } else {
                        formattedDate = input;
                    }

                    $(this).val(formattedDate);

                    // Find paired feedback element + hidden input
                    let feedback = $(this).closest('.col-12').find('.date-feedback');
                    let hiddenInputId = $(this).data('hidden-id');
                    let hiddenInput = $('#' + hiddenInputId);

                    // Empty input = clear (this field is optional)
                    if (formattedDate.length === 0) {
                        hiddenInput.val('');
                        feedback.html('');
                        $(this).removeClass('is-valid is-invalid');
                        toggleSubmitButton();
                        return;
                    }

                    // Full date entered (10 chars: dd/mm/yyyy)
                    if (formattedDate.length === 10) {
                        let parts = formattedDate.split('/');
                        let day = parseInt(parts[0]);
                        let month = parseInt(parts[1]);
                        let yearBE = parseInt(parts[2]);

                        // Validate: year must be Buddhist era (> 2400 to be safe)
                        if (yearBE < 2400 || yearBE > 2700) {
                            hiddenInput.val('');
                            feedback.html('<span class="text-danger"><i class="uil uil-times-circle"></i> ใส่ปี พ.ศ. ให้ถูกต้อง (เช่น 2570)</span>');
                            $(this).addClass('is-invalid').removeClass('is-valid');
                            toggleSubmitButton();
                            return;
                        }

                        // Convert BE -> CE
                        let yearCE = yearBE - 543;

                        // Validate real date (catches 31/02, 30/02, 32/13, etc.)
                        if (!isValidDate(day, month, yearCE)) {
                            hiddenInput.val('');
                            feedback.html('<span class="text-danger"><i class="uil uil-times-circle"></i> วันที่ไม่ถูกต้อง</span>');
                            $(this).addClass('is-invalid').removeClass('is-valid');
                            toggleSubmitButton();
                            return;
                        }

                        // All checks passed - format for DB (Y-m-d)
                        let dayStr   = String(day).padStart(2, '0');
                        let monthStr = String(month).padStart(2, '0');
                        let finalDateForDB = yearCE + '-' + monthStr + '-' + dayStr;

                        hiddenInput.val(finalDateForDB);
                        feedback.html('<span class="text-success"><i class="uil uil-check-circle"></i> รูปแบบวันที่ถูกต้อง</span>');
                        $(this).addClass('is-valid').removeClass('is-invalid');
                    } else {
                        // Incomplete input (less than 10 chars but not empty)
                        hiddenInput.val('');
                        feedback.html('');
                        $(this).removeClass('is-valid is-invalid');
                    }

                    toggleSubmitButton();
                });

                // Disable submit button if any date input is invalid
                function toggleSubmitButton() {
                    if ($('.date-th-input.is-invalid').length > 0) {
                        $('#btnNextStep').attr('disabled', true);
                    } else {
                        $('#btnNextStep').attr('disabled', false);
                    }
                }
            });
        </script>
    <script>
        // Open PDF preview modal on button click; clear iframe on close to stop loading
        document.addEventListener('DOMContentLoaded', function () {
            const modal = document.getElementById('pdfPreviewModal');
            const frame = document.getElementById('pdfPreviewFrame');
            const title = document.getElementById('pdfPreviewTitle');

            document.querySelectorAll('.pdf-preview-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    frame.src = this.dataset.pdfUrl;
                    title.textContent = this.dataset.pdfName;
                    new bootstrap.Modal(modal).show();
                });
            });

            modal.addEventListener('hidden.bs.modal', function () {
                frame.src = '';
            });
        });
    </script>
    @endpush

