@section('title', 'สรุปผลและลงนาม')
@section('description', 'ID Drives - ระบบตรวจมาตรฐานรถ')
@extends('layout.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/custom-mobile.css') }}">
@endpush

@section('content')
    @php
        $totalItems = $passCount + $failCount + $AlmostCount + $uncheckedCount;
        $checkedItems = $passCount + $failCount + $AlmostCount;
        $percent = $totalItems > 0 ? round(($checkedItems / $totalItems) * 100) : 0;
        $isCompleted = $uncheckedCount == 0;
        // Inspector signature from user profile (if exists)
        $hasProfileSign = !empty($inspectorProfileSign);
    @endphp

    <div class="container-fluid py-3">
        <div class="row justify-content-center mt-30 mb-25">
            <div class="col-12 col-md-10 col-lg-8">

                <div class="d-flex align-items-center mb-4 mt-2">
                    <div class="bg-success text-white rounded-circle d-flex justify-content-center align-items-center me-3 shadow-sm"
                        style="width: 55px; height: 55px;">
                        <i class="uil uil-file-check-alt fs-24"></i>
                    </div>
                    <div>
                        <h5 class="mb-1 fw-bold text-dark">สรุปผลการตรวจและลงนาม</h5>
                        <div class="small text-muted mb-1">
                            <span class="fw-bold text-primary">{{ $formGroup->form_group_name ?? 'แบบฟอร์มการตรวจ' }}</span>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mb-20 ">
                    <div class="card-header ">
                        <a href="{{ route('inspection.step3', $record->record_id) }}"
                            class="btn btn btn-primary radius-xs">
                            <i class="uil uil-arrow-left"></i> ย้อนกลับ
                        </a>
                    </div>

                    <div class="card-body" style="background-color: #d7e3f5; border-radius: 0 0 12px 12px;">
                        <h6 class="card-title">ทะเบียนรถ</h6>
                        <span class=" fs-20 text-secondary"><strong>
                                {{ $vehicle->car_plate ?? 'ไม่ระบุ' }}</strong>
                        </span>
                    </div>
                </div>

                <form id="submitForm" action="{{ route('inspection.submitInspection', $record->record_id) }}"
                    method="POST">
                    @csrf

                    <input type="hidden" name="submit_type" id="submit_type" value="final">
                    {{-- Flag: 1 = use profile signature, 0 = use canvas (live signed) --}}
                    <input type="hidden" name="use_profile_sign" id="use_profile_sign"
                        value="{{ $hasProfileSign ? '1' : '0' }}">

                    <div class="card border-0 shadow-sm radius-xs mb-4">
                        <div class="card-body p-3 p-md-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="fw-bold mb-0"><i class="uil uil-chart-pie me-1"></i> ภาพรวมการตรวจ</h6>
                            </div>

                            <div class="mb-4">
                                <label class="fw-bold">ความคืบหน้าการตรวจ: {{ $checkedItems }}/{{ $totalItems }} ข้อ
                                    ({{ $percent }}%)</label>
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar {{ $isCompleted ? 'bg-success' : 'bg-primary' }}"
                                        role="progressbar" style="width: {{ $percent }}%;"
                                        aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100">
                                        {{ $percent }}%
                                    </div>
                                </div>
                            </div>

                            <div class="row g-2 text-center">
                                <div class="col-4">
                                    <div class="bg-success-transparent  p-2 rounded radius-xs h-100">
                                        <div class="fs-24 fw-bold">{{ $passCount }}</div>
                                        <div class="fs-16 fw-bold text-success">ผ่าน / ปกติ</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="bg-warning-transparent  p-2 rounded radius-xs h-100">
                                        <div class="fs-24 fw-bold">{{ $AlmostCount }}</div>
                                        <div class="fs-16 fw-bold text-warning">ไม่ปกติ แต่ใช้งานได้</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="bg-danger-transparent p-2 rounded radius-xs h-100">
                                        <div class="fs-24 fw-bold">{{ $failCount }}</div>
                                        <div class="fs-16 fw-bold text-danger">ไม่ปกติ / ไม่ผ่าน</div>
                                    </div>
                                </div>
                            </div>

                            @if (!$isCompleted)
                                <div
                                    class="alert alert-danger mt-3 mb-0 py-2 px-3 radius-xs border-0 d-flex align-items-center">
                                    <i class="uil uil-info-circle fs-18 me-2"></i>
                                    <div class="fs-18">มีข้อยังไม่ได้ตรวจ <strong>{{ $uncheckedCount }}</strong> ข้อ</div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm radius-xs mb-4">
                        <div class="card-body p-3 p-md-4">
                            <h6 class="fw-bold mb-3"><i class="uil uil-calendar-alt me-1"></i> วันที่ตรวจ</h6>
                            <div>
                                <input type="date" class="form-control" name="inspect_date" id="inspect_date"
                                    value="{{ $record->inspect_date ?? date('Y-m-d') }}"
                                    max="{{ date('Y-m-d') }}">
                                <div class="form-text text-muted">ระบุวันที่ตรวจ หากไม่ระบุวัน ระบบจะบันทึกจากวันที่ปัจจุบัน</div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm radius-xs mb-4">
                        <div class="card-body p-3 p-md-4">
                            <h6 class="fw-bold mb-3"><i class="uil uil-check-circle me-1"></i> ผลการตรวจสอบสมรรถนะของรถ
                                <span class="text-danger">*</span>
                            </h6>
                            <div class="d-grid gap-3">
                                <input type="radio" class="btn-check" name="evaluate_status" id="eval_1"
                                    value="1">
                                <label
                                    class="btn btn-outline-success border-dark w-100 text-start p-3 radius-xs border d-flex align-items-center"
                                    for="eval_1">
                                    <i class="uil uil-check-circle me-3"
                                        style="font-size: 24px; line-height: 2; flex-shrink: 0;"></i>
                                    <span class="fs-18 fw-bold">ปกติ อนุญาตให้ใช้งานได้</span>
                                </label>

                                <input type="radio" class="btn-check eval-radio" name="evaluate_status" id="eval_2"
                                    value="2">
                                <label
                                    class="btn border-dark btn-outline-warning w-100 text-start px-3 py-2 radius-xs border d-flex align-items-center"
                                    for="eval_2">
                                    <i class="uil uil-exclamation-circle me-3"
                                        style="font-size: 24px; line-height: 2; flex-shrink: 0;"></i>
                                    <div class="d-flex flex-column" style="gap: 2; line-height: 1.5;">
                                        <span class="fs-15 fw-bold" style="line-height: 1.5;">ไม่ปกติ
                                            แต่สามารถปฏิบัติงานได้</span>
                                        <span class="small fw-normal text-dark"
                                            style=" line-height: 1.5; margin-top: 2px;">(ต้องนำรถไปซ่อมแซม
                                            และนำรถมาตรวจสภาพใหม่ ภายใน 7 วัน)</span>
                                    </div>
                                </label>

                                <input type="radio" class="btn-check eval-radio" name="evaluate_status" id="eval_3"
                                    value="3">
                                <label
                                    class="btn border-dark btn-outline-danger w-100 text-start p-3 radius-xs border d-flex align-items-center"
                                    for="eval_3">
                                    <i class="uil uil-times-circle me-3"
                                        style="font-size: 24px; line-height: 2; flex-shrink: 0;"></i>
                                    <span class="fs-18 fw-bold">ไม่ปกติ ไม่อนุญาตให้ใช้งาน</span>
                                </label>
                            </div>

                            <div id="next_inspect_container" class="mt-3 p-3 rounded border d-none"
                                style="background-color: #ffffff;">
                                <label class="small text-dark fw-bold mb-2">
                                    กำหนดระยะเวลาตรวจสภาพใหม่ วันที่ <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="next_inspect_date" id="next_inspect_date"
                                    class="form-control radius-xs border" min="{{ date('Y-m-d') }}">
                                <div class="small text-muted mt-1">
                                    กรุณาระบุวันที่ที่ต้องการให้รถคันนี้กลับมาตรวจสภาพอีกครั้ง</div>
                            </div>

                        </div>
                    </div>

                    <div class="card border-0 shadow-sm radius-xs mb-4">
                        <div class="card-body p-3 p-md-4">
                            <h6 class="fw-bold mb-3"><i class="uil uil-pen me-1"></i> ลงลายเซ็น</h6>

                            {{-- ========================================== --}}
                            {{-- Inspector signature block --}}
                            {{-- ========================================== --}}
                            <div class="mb-4">
                                <label class="small text-dark fw-bold mb-2">ลงชื่อผู้ตรวจ <span
                                        class="text-danger">*</span></label>

                                @if ($hasProfileSign)
                                    {{-- Profile signature exists: show image by default + toggle to canvas --}}
                                    <div id="inspectorProfileBox"
                                        class="border border-dark rounded bg-white position-relative d-flex justify-content-center align-items-center"
                                        style="height: 200px;">
                                        <img src="{{ asset($inspectorProfileSign) }}" alt="ลายเซ็นผู้ตรวจ"
                                            style="max-height: 180px; max-width: 100%; object-fit: contain;">
                                    </div>
                                    <div class="text-center mt-2">
                                        <button type="button" id="btnSwitchToCanvas"
                                            class="btn btn-link btn-sm fw-bold p-0">
                                            <i class="uil uil-edit me-1"></i> คลิกที่นี่หากต้องการเซ็นใหม่
                                        </button>
                                    </div>

                                    {{-- Canvas (hidden by default) --}}
                                    <div id="inspectorCanvasBox" class="d-none">
                                        <div class="border border-dark rounded bg-white position-relative"
                                            style="height: 200px;">
                                            <canvas id="inspectorCanvas" class="w-100 h-100"
                                                style="touch-action: none;"></canvas>
                                            <button type="button"
                                                class="btn btn-dark btn-transparent-dark btn-xs position-absolute bottom-0 end-0 m-2 shadow-sm"
                                                onclick="inspectorPad.clear()">ล้าง</button>
                                        </div>
                                        <div class="text-center mt-2">
                                            <button type="button" id="btnSwitchToProfile"
                                                class="btn btn-link btn-sm  fw-bold p-0">
                                                <i class="uil uil-arrow-left me-1"></i> ใช้ลายเซ็นจากภาพ
                                            </button>
                                        </div>
                                    </div>
                                @else
                                    {{-- No profile signature: live canvas only --}}
                                    <div id="inspectorCanvasBox">
                                        <div class="border border-dark rounded bg-white position-relative"
                                            style="height: 200px;">
                                            <canvas id="inspectorCanvas" class="w-100 h-100"
                                                style="touch-action: none;"></canvas>
                                            <button type="button"
                                                class="btn btn-dark btn-transparent-dark btn-xs position-absolute bottom-0 end-0 m-2 shadow-sm"
                                                onclick="inspectorPad.clear()">ล้าง</button>
                                        </div>
                                    </div>
                                @endif

                                <input type="hidden" name="inspector_sign_data" id="inspector_sign_data">
                            </div>

                            {{-- ========================================== --}}
                            {{-- Driver signature block (live canvas always) --}}
                            {{-- ========================================== --}}
                            <div>
                                <label class="small text-dark fw-bold mb-2">ลงชื่อผู้รับการตรวจ (ถ้ามี)</label>
                                <div class="border border-dark rounded bg-white position-relative" style="height: 200px;">
                                    <canvas id="driverCanvas" class="w-100 h-100" style="touch-action: none;"></canvas>
                                    <button type="button"
                                        class="btn btn-dark btn-transparent-dark btn-xs position-absolute bottom-0 end-0 m-2 shadow-sm"
                                        onclick="driverPad.clear()">ล้าง</button>
                                </div>
                                <input type="hidden" name="driver_sign_data" id="driver_sign_data">
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-column flex-md-row gap-2 mb-5">
                        <button type="button" id="btnDraft"
                            class="btn btn-outline-primary btn-lg py-3 fw-bold radius-xs btn-block">
                            <i class="uil uil-save me-1"></i> บันทึกแบบร่าง
                        </button>

                        <button type="button" id="btnSubmit"
                            class="btn btn-success btn-lg py-3 fw-bold radius-xs btn-block shadow-sm"
                            {{ $isCompleted ? '' : 'disabled' }}>
                            <i class="uil uil-check-circle me-1"></i> ยืนยันผลการตรวจ
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.5/dist/signature_pad.umd.min.js"></script>
        <script>
            // ==========================================
            // Toggle next-inspect-date box based on evaluate_status
            // ==========================================
            const evalRadios = document.querySelectorAll('.eval-radio');
            const nextInspectContainer = document.getElementById('next_inspect_container');
            const nextInspectInput = document.getElementById('next_inspect_date');

            function toggleNextInspectDate() {
                const selectedRadio = document.querySelector('.eval-radio:checked');
                if (selectedRadio && selectedRadio.value === '3') {
                    nextInspectContainer.classList.remove('d-none');
                    nextInspectInput.setAttribute('required', 'required');
                } else {
                    nextInspectContainer.classList.add('d-none');
                    nextInspectInput.removeAttribute('required');
                    nextInspectInput.value = '';
                }
            }
            evalRadios.forEach(radio => radio.addEventListener('change', toggleNextInspectDate));
            toggleNextInspectDate();
        </script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // ==========================================
                // Profile signature flag (from server)
                // ==========================================
                const hasProfileSign = {{ $hasProfileSign ? 'true' : 'false' }};
                const useProfileSignInput = document.getElementById('use_profile_sign');

                // ==========================================
                // Canvas resize helper
                // ==========================================
                function resizeCanvas(canvas) {
                    if (!canvas) return;
                    const ratio = Math.max(window.devicePixelRatio || 1, 1);
                    canvas.width = canvas.offsetWidth * ratio;
                    canvas.height = canvas.offsetHeight * ratio;
                    canvas.getContext("2d").scale(ratio, ratio);
                }

                const inspectorCanvas = document.getElementById('inspectorCanvas');
                const driverCanvas = document.getElementById('driverCanvas');

                // ==========================================
                // Init driver pad (always live canvas)
                // ==========================================
                resizeCanvas(driverCanvas);
                window.driverPad = new SignaturePad(driverCanvas, {
                    penColor: "rgb(0, 0, 0)"
                });

                // ==========================================
                // Init inspector pad
                // - If profile sign exists: pad initialized but hidden until user clicks "เซ็นใหม่"
                //   (resize is deferred until canvas becomes visible)
                // - If no profile sign: pad active from start
                // ==========================================
                let inspectorPadInitialized = false;

                function initInspectorPad() {
                    if (inspectorPadInitialized) return;
                    resizeCanvas(inspectorCanvas);
                    window.inspectorPad = new SignaturePad(inspectorCanvas, {
                        penColor: "rgb(0, 0, 0)"
                    });
                    inspectorPadInitialized = true;
                }

                if (!hasProfileSign) {
                    // No profile sign: init pad immediately
                    initInspectorPad();
                }

                // ==========================================
                // Toggle: switch from profile image to canvas
                // ==========================================
                const btnSwitchToCanvas = document.getElementById('btnSwitchToCanvas');
                const btnSwitchToProfile = document.getElementById('btnSwitchToProfile');
                const profileBox = document.getElementById('inspectorProfileBox');
                const canvasBox = document.getElementById('inspectorCanvasBox');

                if (btnSwitchToCanvas) {
                    btnSwitchToCanvas.addEventListener('click', function() {
                        profileBox.classList.add('d-none');
                        document.querySelector('.text-center.mt-2').classList.add('d-none'); // hide its own row
                        canvasBox.classList.remove('d-none');
                        useProfileSignInput.value = '0';
                        initInspectorPad();
                        // Resize after becoming visible (offsetWidth was 0 while hidden)
                        setTimeout(() => resizeCanvas(inspectorCanvas), 50);
                    });
                }

                if (btnSwitchToProfile) {
                    btnSwitchToProfile.addEventListener('click', function() {
                        canvasBox.classList.add('d-none');
                        profileBox.classList.remove('d-none');
                        // Show the "เซ็นใหม่" button row again
                        const switchRow = btnSwitchToCanvas.closest('.text-center.mt-2');
                        if (switchRow) switchRow.classList.remove('d-none');
                        useProfileSignInput.value = '1';
                        if (window.inspectorPad) inspectorPad.clear();
                    });
                }

                // ==========================================
                // Convert canvas to base64 before submit
                // ==========================================
                function prepareSignatures() {
                    // Inspector: only send canvas data if user opted to sign live
                    if (useProfileSignInput.value === '0' && window.inspectorPad && !inspectorPad.isEmpty()) {
                        document.getElementById('inspector_sign_data').value = inspectorPad.toDataURL();
                    }
                    // Driver: always check live canvas
                    if (!driverPad.isEmpty()) {
                        document.getElementById('driver_sign_data').value = driverPad.toDataURL();
                    }
                }

                // ==========================================
                // Save Draft
                // ==========================================
                document.getElementById('btnDraft').addEventListener('click', function() {
                    document.getElementById('submit_type').value = 'draft';
                    prepareSignatures();
                    document.getElementById('submitForm').submit();
                });

                // ==========================================
                // Final Submit
                // ==========================================
                document.getElementById('btnSubmit').addEventListener('click', function() {
                    document.getElementById('submit_type').value = 'final';

                    // Check evaluate_status
                    const isEvaluated = document.querySelector('input[name="evaluate_status"]:checked');
                    if (!isEvaluated) {
                        alert('กรุณาเลือกประเมินสถานะการใช้งานของรถคันนี้ครับ');
                        return;
                    }

                    if (isEvaluated.value === '3' && !nextInspectInput.value) {
                        alert('กรุณาระบุวันที่กำหนดตรวจสภาพใหม่ด้วยครับ');
                        nextInspectInput.focus();
                        return;
                    }

                    // Check inspector signature:
                    // - If using profile sign → OK (server will copy from users.signature_image)
                    // - If using live canvas → must not be empty
                    if (useProfileSignInput.value === '0') {
                        if (!window.inspectorPad || inspectorPad.isEmpty()) {
                            alert('กรุณาลงลายมือชื่อผู้ตรวจครับ');
                            return;
                        }
                    }

                    prepareSignatures();
                    document.getElementById('submitForm').submit();
                });
            });
        </script>
    @endpush
@endsection