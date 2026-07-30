@section('title', 'บันทึกข้อมูลก่อนตรวจ')
@section('description', 'ID Drives - ระบบตรวจมาตรฐานรถ')
@extends('layout.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/custom-mobile.css') }}">
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
                <div class="card">
                    <div class="card-body">


                        <form action="{{ route('user.inspection.storeStep2', $record->record_id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="row g-3 mb-4">

                                @php $imgIndex = 1; @endphp

                                @foreach ($preFields as $field)
                                    @if ($field->field_type == 'image')
                                        @if ($imgIndex <= 8)
                                            <div class="col-6">
                                                <div class="card shadow-none radius-xs h-100 photo-card"
                                                    id="card_{{ $field->id }}">
                                                    <div class="card-body p-2 text-center position-relative d-flex flex-column align-items-center justify-content-center"
                                                        style="min-height: 140px;">
                                                        <span
                                                            class="badge bg-success position-absolute top-0 end-0 mt-2 me-2 d-none"
                                                            id="badge_{{ $field->id }}"><i
                                                                class="uil uil-check"></i></span>
                                                        <label for="input_{{ $field->id }}" class="w-100 h-100 m-0">
                                                            <img id="preview_{{ $field->id }}" src=""
                                                                class="img-fluid rounded d-none"
                                                                style="width: 100%; height: 120px; object-fit: cover;">
                                                            <div id="content_{{ $field->id }}">
                                                                <i class="uil uil-camera-plus fs-32 text-primary"></i>
                                                                <span
                                                                    class="fw-bold text-dark fs-14 d-block">{{ $field->field_label }}</span>
                                                            </div>
                                                        </label>
                                                        <input type="file" name="photos[{{ $imgIndex }}]"
                                                            id="input_{{ $field->id }}" class="d-none file-input"
                                                            accept="image/*" capture="environment"
                                                            data-id="{{ $field->id }}"
                                                            {{ $field->is_required ? 'required' : '' }}>
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
                                                {{ $field->is_required ? 'required' : '' }}>
                                           
                                        </div>
                                    @endif
                                @endforeach
                            </div>
 <div class="border-top my-3"></div>
                            <div class="d-flex gap-2">
                                <button type="submit"
                                    class="btn btn-info text-white btn-lg w-100 py-3 fw-bold radius-xs shadow-sm">
                                    ถัดไป <i class="uil uil-arrow-right ms-1"></i>
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
                // จัดการพรีวิวรูปภาพ
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
                        }
                    });
                });

                // จัดการดึงพิกัด GPS
                document.querySelectorAll('.get-gps-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const id = this.getAttribute('data-id');
                        const input = document.getElementById('gps_' + id);

                        this.innerHTML = '<i class="uil uil-spinner fa-spin"></i> รอพิกัด...';

                        if (navigator.geolocation) {
                            navigator.geolocation.getCurrentPosition(function(position) {
                                input.value = position.coords.latitude + ',' + position.coords
                                    .longitude;
                                btn.innerHTML =
                                    '<i class="uil uil-check"></i> สำเร็จ';
                                btn.classList.replace('btn-outline-primary', 'btn-success');
                            }, function(error) {
                                alert(
                                    'ไม่สามารถดึงตำแหน่งได้ กรุณาเปิด Location Service ของมือถือครับ');
                                btn.innerHTML = '<i class="uil uil-map-marker"></i> ลองใหม่';
                            });
                        } else {
                            alert('เบราว์เซอร์นี้ไม่รองรับ GPS');
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection
