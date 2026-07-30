@section('title', 'แก้ไข Supply')
@section('description', 'ID Drives')
@extends('layout.app')

@section('content')
<div class="container-fluid">

    <div class="row">
        <div class="col-12">
            <div class="breadcrumb-main">
                <h5 class="breadcrumb-title">
                    <a href="{{ route('staff.supplies.index') }}" class="text-muted">จัดการ Supply</a>
                    <span class="text-muted mx-1">/</span> แก้ไข: {{ $supply->supply_name }}
                </h5>
            </div>
        </div>
    </div>

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="uil uil-exclamation-triangle me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('staff.supplies.update', $supply->sup_id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row">

            {{-- ซ้าย: ข้อมูล Supply --}}
            <div class="col-lg-8">
                <div class="card shadow-sm mb-25">
                    <div class="card-header" style="background:linear-gradient(135deg,#5840ff 0%,#7c66ff 100%);padding:14px 20px;">
                        <h6 class="mb-0 fw-bold text-white"><i class="uil uil-building me-2"></i>ข้อมูล Supply</h6>
                    </div>
                    <div class="card-body p-4">

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-600">บริษัทแม่ <span class="text-danger">*</span></label>
                                <select name="company_code" class="form-select @error('company_code') is-invalid @enderror">
                                    <option value="">-- เลือกบริษัท --</option>
                                    @foreach ($companies as $c)
                                        <option value="{{ $c->company_id }}"
                                            {{ old('company_code', $supply->company_code) == $c->company_id ? 'selected' : '' }}>
                                            {{ $c->company_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('company_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-600">ชื่อ Supply <span class="text-danger">*</span></label>
                                <input type="text" name="supply_name" class="form-control @error('supply_name') is-invalid @enderror"
                                    value="{{ old('supply_name', $supply->supply_name) }}">
                                @error('supply_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-600">ที่อยู่</label>
                                <textarea name="supply_address" class="form-control" rows="3">{{ old('supply_address', $supply->supply_address) }}</textarea>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-600">เบอร์โทรศัพท์</label>
                                <input type="text" name="supply_phone" class="form-control"
                                    value="{{ old('supply_phone', $supply->supply_phone) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-600">อีเมล</label>
                                <input type="email" name="supply_email" class="form-control @error('supply_email') is-invalid @enderror"
                                    value="{{ old('supply_email', $supply->supply_email) }}">
                                @error('supply_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-600">สถานะ <span class="text-danger">*</span></label>
                                <select name="supply_status" class="form-select">
                                    <option value="1" {{ old('supply_status', $supply->supply_status) == '1' ? 'selected' : '' }}>ใช้งาน</option>
                                    <option value="0" {{ old('supply_status', $supply->supply_status) == '0' ? 'selected' : '' }}>ปิดใช้งาน</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-600">โลโก้ Supply</label>
                                @if ($supply->supply_logo)
                                    <div class="mb-2">
                                        <img src="{{ asset($supply->supply_logo) }}" class="rounded" style="max-height:60px;">
                                        <span class="text-muted fs-12 ms-2">โลโก้ปัจจุบัน</span>
                                    </div>
                                @endif
                                <input type="file" name="supply_logo" class="form-control @error('supply_logo') is-invalid @enderror"
                                    accept="image/jpg,image/jpeg,image/png" onchange="previewLogo(this)">
                                @error('supply_logo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <img id="logoPreview" src="#" class="mt-2 rounded" style="max-height:80px;display:none;">
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- ขวา: บัญชีผู้ใช้ --}}
            <div class="col-lg-4">
                <div class="card shadow-sm mb-25">
                    <div class="card-header" style="background:linear-gradient(135deg,#0ea5e9 0%,#38bdf8 100%);padding:14px 20px;">
                        <h6 class="mb-0 fw-bold text-white"><i class="uil uil-user me-2"></i>บัญชีผู้ใช้</h6>
                    </div>
                    <div class="card-body p-4">

                        <div class="mb-3">
                            <label class="form-label fw-600">Username <span class="text-danger">*</span></label>
                            <input type="text" name="username" class="form-control @error('username') is-invalid @enderror"
                                value="{{ old('username', $supply->username) }}" autocomplete="off">
                            @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <hr class="my-3">
                        <p class="text-muted fs-13 mb-3"><i class="uil uil-lock me-1"></i>เปลี่ยนรหัสผ่าน — เว้นว่างไว้หากไม่ต้องการเปลี่ยน</p>

                        <div class="mb-3">
                            <label class="form-label fw-600">รหัสผ่านใหม่</label>
                            <div class="input-group">
                                <input type="password" name="password" id="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    placeholder="อย่างน้อย 4 ตัวอักษร" autocomplete="new-password">
                                <button type="button" class="btn btn-outline-secondary px-2" onclick="togglePwd('password','eye1')">
                                    <i class="uil uil-eye" id="eye1"></i>
                                </button>
                                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-600">ยืนยันรหัสผ่านใหม่</label>
                            <div class="input-group">
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                    class="form-control" autocomplete="new-password">
                                <button type="button" class="btn btn-outline-secondary px-2" onclick="togglePwd('password_confirmation','eye2')">
                                    <i class="uil uil-eye" id="eye2"></i>
                                </button>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('staff.supplies.index') }}" class="btn btn-light flex-fill">ยกเลิก</a>
                    <button type="submit" class="btn btn-primary flex-fill">
                        <i class="uil uil-save me-1"></i> บันทึก
                    </button>
                </div>
            </div>

        </div>
    </form>

</div>

<script>
function togglePwd(fieldId, iconId) {
    const f = document.getElementById(fieldId), i = document.getElementById(iconId);
    f.type = f.type === 'password' ? 'text' : 'password';
    i.classList.toggle('uil-eye'); i.classList.toggle('uil-eye-slash');
}
function previewLogo(input) {
    const img = document.getElementById('logoPreview');
    if (input.files && input.files[0]) {
        img.src = URL.createObjectURL(input.files[0]);
        img.style.display = 'block';
    }
}
</script>
@endsection