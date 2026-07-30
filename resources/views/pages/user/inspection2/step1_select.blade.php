@section('title', 'เริ่มตรวจสภาพรถ')
@section('description', 'ID Drives - ระบบตรวจมาตรฐานรถ')
@extends('layout.app')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/custom-mobile.css') }}">
@endpush
@section('content')
    <div class="container-fluid py-3">
        <div class="row justify-content-center mt-30 mb-25">
            <div class="col-12 col-md-8 col-lg-5">

                <div class="d-flex align-items-center mb-4 mt-2">
                    <div class="bg-primary text-white rounded-circle d-flex justify-content-center align-items-center me-3 shadow-sm"
                        style="width: 45px; height: 45px;">
                        <i class="uil uil-clipboard-notes fs-20"></i>
                    </div>
                    <div>
                        <p class="mb-0 fs-16 fw-bold text-dark">เริ่มตรวจรถ</p>
                        <span class="small text-muted">ขั้นตอนที่ 1 : เลือกรถและแบบฟอร์ม</span>
                    </div>
                </div>

                <div class="card border-0 shadow-sm radius-xs mb-4">
                     <div class="card-header d-flex justify-content-end align-items-center">
                            <small class="text-muted" id="live-clock">
                        </div>
                    <div class="card-body p-3 p-md-4">
                        <form action="{{ route('user.inspection.start') }}" method="POST">
                            @csrf

                            <div class="mb-4 pb-2 border-bottom">
                                <label class="form-label fw-bold text-dark mb-2">1. ค้นหาทะเบียนรถ <span class="text-danger">*</span></label>
                                <select name="car_id" id="vehicleSearch" class="form-control form-control-lg" required>
                                </select>
                                <small class="text-muted mt-2 d-block"><i class="uil uil-info-circle"></i>
                                    พิมพ์ตัวเลขทะเบียนรถเพื่อค้นหา (เช่น 82-11)</small>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold text-dark mb-3">2. เลือกแบบฟอร์มการตรวจ <span
                                        class="text-danger">*</span></label>
                                <div class="d-grid gap-3">
                                    @foreach ($formGroups as $group)
                                        <div class="form-check-custom">
                                            <input type="radio" class="btn-check" name="form_group_id"
                                                id="form_{{ $group->id }}" value="{{ $group->id }}" required>
                                            <label
                                                class="btn w-100 text-start p-3 radius-xs d-flex align-items-center justify-content-between form-card-label"
                                                for="form_{{ $group->id }}">

                                                <div class="fw-bold text-dark fs-16">{{ $group->form_group_name }}</div>

                                                <div class="icon-check text-primary ms-2" style="display: none;">
                                                    <i class="uil uil-check-circle fs-24"></i>
                                                </div>

                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <button type="submit"
                                class="btn btn-success btn-lg w-100 py-3 mt-2 fw-bold radius-xs shadow-sm">
                                เริ่มการตรวจ <i class="uil uil-arrow-right ms-1"></i>
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <style>
            .form-check-custom .btn-check:checked+label {
                border-color: #5F63F2 !important;
                background-color: #f4f5ff !important;
            }

            .form-check-custom .btn-check:checked+label .icon-check {
                display: block !important;
            }

            .select2-container .select2-selection--single {
                height: 50px;
                border-radius: 8px;
                border-color: #e3e6ef;
                display: flex;
                align-items: center;
            }

            .select2-container--default .select2-selection--single .select2-selection__arrow {
                height: 48px;
            }

            .select2-container--default .select2-selection--single .select2-selection__rendered {
                line-height: normal;
                font-size: 15px;
                padding-left: 15px;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script>
               function updateClock() {
            const now = new Date();

            const day = String(now.getDate()).padStart(2, '0');
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const year = now.getFullYear() + 543;

            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');

            const fullDateTime = `${day}/${month}/${year} ${hours}:${minutes}:${seconds}`;

            document.getElementById('live-clock').textContent = fullDateTime;
        }

        updateClock();
        setInterval(updateClock, 1000);

            $(document).ready(function() {
                $('#vehicleSearch').select2({
                    theme: 'bootstrap-5',
                    ajax: {
                        url: "{{ route('user.inspection.searchVehicle') }}",
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                q: params.term
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: $.map(data, function(item) {
                                    return {
                                        text: item.car_plate + (item.car_brand ? ' (' + item
                                            .car_brand + ')' : ''),
                                        id: item.car_id
                                    }
                                })
                            };
                        },
                        cache: true
                    },
                    minimumInputLength: 2,
                    placeholder: 'พิมพ์ทะเบียนรถ...',
                    language: {
                        inputTooShort: function() {
                            return "กรุณาพิมพ์อย่างน้อย 2 ตัวอักษร";
                        },
                        noResults: function() {
                            return "ไม่พบข้อมูลรถยนต์";
                        },
                        searching: function() {
                            return "กำลังค้นหา...";
                        }
                    }
                });
            });
        </script>
        
    @endpush
@endsection
