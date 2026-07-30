@section('title', 'ระบบตรวจมาตรฐานรถ')
@section('description', 'ID Drives')
@extends('layout.app')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb-main">
                    <span class="fs-24 fw-bold breadcrumb-title">แก้ไขข้อมูล Supply : {{ $supply->supply_name }}</span>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card mb-25">
                    <div class="card-body">
                        @if (session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        <form action="{{ route('company.supplies.update', $supply->sup_id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT') <div class="mb-3">
                                <label>ชื่อบริษัทในเครือ(Supply) <span class="text-danger">*</span></label>
                                <input type="text" name="supply_name" class="form-control"
                                    value="{{ old('supply_name', $supply->supply_name) }}" required>
                            </div>

                            <div class="mb-3">
                                <label>ที่อยู่ <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="supply_address" rows="3" required>{{ old('supply_address', $supply->supply_address) }}</textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">จำนวนรถที่ลงทะเบียนได้ <span
                                            class="text-danger">*</span></label>
                                    <input type="number" name="vehicle_limit" min="0" class="form-control"
                                        value="{{ old('vehicle_limit', $supply->vehicle_limit) }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">วันเริ่มใช้งาน</label>
                                    <input type="date" name="start_date" class="form-control"
                                        value="{{ old('start_date', $supply->start_date) }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">วันหมดอายุ</label>
                                    <input type="date" name="expire_date" class="form-control"
                                        value="{{ old('expire_date', $supply->expire_date) }}">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>เบอร์โทรศัพท์</label>
                                    <input type="text" name="supply_phone" class="form-control"
                                        value="{{ old('supply_phone', $supply->supply_phone) }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>อีเมล</label>
                                    <input type="email" name="supply_email" class="form-control"
                                        value="{{ old('supply_email', $supply->supply_email) }}">
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <div class="form-group">
                                    <label class="il-gray fw-bold mb-2">Logo สาขา (ถ้ามี)</label>

                                    @if (!empty($supply->supply_logo))
                                        <div class="mb-2">
                                            <p class="mb-1 text-muted"><small>โลโก้ปัจจุบัน:</small></p>
                                            <img src="{{ asset($supply->supply_logo) }}" class="img-thumbnail"
                                                style="max-height: 120px;">
                                        </div>
                                    @endif

                                    <input type="file" name="supply_logo" accept="image/*" class="form-control"
                                        id="logo-input">

                                    <div class="mt-2">
                                        <p class="mb-1 text-muted d-none" id="new-logo-text"><small>โลโก้ใหม่:</small></p>
                                        <img id="logo-preview" src="#" class="img-thumbnail d-none"
                                            style="max-height: 120px;">
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 mb-4">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="supply_status" value="0">

                                    <input class="form-check-input" type="checkbox" id="supply_status" name="supply_status"
                                        value="1"
                                       {{ (old('supply_status', $supply->supply_status) == '1') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="supply_status">เปิดใช้งานสาขา (สถานะ
                                        Active)</label>
                                </div>
                            </div>

                            <div class="border-top my-3"></div>
                            <h6 class="mb-3">ข้อมูลสำหรับเข้าสู่ระบบ (Login)</h6>

                            <div class="mb-3">
                                <label>Username สำหรับเข้าใช้งาน <span class="text-danger">*</span></label>
                                <input type="text" name="supply_user" class="form-control"
                                    value="{{ old('supply_user', $user->username ?? '') }}" required>
                            </div>

                            <div class="mb-3">
                                <label>Password สำหรับเข้าใช้งาน (ปล่อยว่างหากไม่ต้องการเปลี่ยน)</label>
                                <input type="text" name="supply_password" class="form-control"
                                    placeholder="กรอกรหัสผ่านใหม่เมื่อต้องการเปลี่ยนเท่านั้น">
                            </div>

                            <div class="border-top my-4"></div>

                            <div class="d-flex justify-content-between">
                                <button type="submit" class="fs-18 btn btn-primary px-4">อัปเดตข้อมูล</button>
                                <a href="{{ route('company.supplies.index') }}"
                                    class="fs-18 btn btn-light px-4">ยกเลิก</a>
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
        document.getElementById('logo-input')?.addEventListener('change', function(event) {
            const input = event.target;
            const preview = document.getElementById('logo-preview');
            const text = document.getElementById('new-logo-text');

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('d-none');
                    if (text) text.classList.remove('d-none');
                };
                reader.readAsDataURL(input.files[0]);
            }
        });
    </script>
@endpush
