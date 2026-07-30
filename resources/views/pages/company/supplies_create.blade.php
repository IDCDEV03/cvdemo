@section('title', 'ระบบตรวจมาตรฐานรถ')
@section('description', 'ID Drives')
@extends('layout.app')
@section('content')


<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="breadcrumb-main">
                <span class="fs-24 fw-bold breadcrumb-title">เพิ่มบริษัท Supply ในเครือ</span>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-25">
                <div class="card-body">
                    @if (session('error'))
                        <div class="alert alert-alert">{{ session('error') }}</div>
                    @endif

                    <form action="{{ route('company.supplies.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label>ชื่อบริษัท Supply <span class="text-danger">*</span></label>
                            <input type="text" name="supply_name" class="form-control" value="{{ old('supply_name') }}" required>
                        </div>

                        <div class="mb-3">
                            <label>ที่อยู่ <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="supply_address" rows="3" required>{{ old('supply_address') }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">จำนวนรถที่ลงทะเบียนได้ <span class="text-danger">*</span></label>
                                <input type="number" name="vehicle_limit" min="0" class="form-control" value="{{ old('vehicle_limit', 0) }}">
                                <small class="text-danger">กำหนดจำนวนรถสูงสุดของบริษัทนี้</small>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">วันเริ่มใช้งาน <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">วันหมดอายุ <span class="text-danger">*</span></label>
                                <input type="date" name="expire_date" class="form-control" value="{{ old('expire_date') }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>เบอร์โทรศัพท์ (ถ้ามี)</label>
                                <input type="text" name="supply_phone" class="form-control" value="{{ old('supply_phone') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>อีเมล (ถ้ามี)</label>
                                <input type="email" name="supply_email" class="form-control" value="{{ old('supply_email') }}">
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="form-group">
                                <label class="il-gray fw-bold mb-2">Logo บริษัท (ถ้ามี)</label>
                                <input type="file" name="supply_logo" accept="image/*" class="form-control" id="logo-input">
                                <div class="mt-2">
                                    <img id="logo-preview" src="#" class="img-thumbnail d-none" style="max-height: 120px;">
                                </div>
                            </div>
                        </div>

                        <div class="col-12 mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="require_user_approval" name="require_user_approval" value="1" {{ old('require_user_approval') ? 'checked' : '' }}>
                                <label class="form-check-label" for="require_user_approval">อนุมัติเปิดการใช้งาน Account</label>
                            </div>
                        </div>

                        <div class="border-top my-3"></div>
                        <h6 class="mb-3">ข้อมูลสำหรับเข้าสู่ระบบ (Login)</h6>

                        <div class="mb-3">
                            <label>Username สำหรับเข้าใช้งาน <span class="text-danger">*</span></label>
                            <button type="button" class="btn btn-xs btn-outline-secondary mb-2 mt-2" onclick="generateRandom('supply_user')">สุ่ม Username</button>
                            <input type="text" name="supply_user" id="supply_user" class="form-control" value="{{ old('supply_user') }}" required>
                            @error('supply_user') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-3">
                            <label>Password สำหรับเข้าใช้งาน <span class="text-danger">*</span></label>
                            <input type="text" name="supply_password" class="form-control" required>
                        </div>

                        <div class="border-top my-4"></div>

                        <div class="d-flex justify-content-between">
                            <button type="submit" class="fs-18 btn btn-primary px-4">บันทึกข้อมูล</button>
                            <a href="{{ route('company.supplies.index') }}" class="fs-18 btn btn-light px-4">ยกเลิก</a>
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
    function generateRandom(fieldId) {
        const chars = 'ABCDEFGHIJKLMNPQRSTUVWXYZ0123456789';
        let result = 'SUP-';
        for (let i = 0; i < 6; i++) {
            result += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        document.getElementById(fieldId).value = result;
    }

    document.getElementById('logo-input')?.addEventListener('change', function(event) {
        const input = event.target;
        const preview = document.getElementById('logo-preview');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('d-none');
            };
            reader.readAsDataURL(input.files[0]);
        } else {
            preview.src = '#';
            preview.classList.add('d-none');
        }
    });
</script>
@endpush