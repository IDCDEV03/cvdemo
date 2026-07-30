@section('title', 'เพิ่ม Supply')
@section('description', 'ID Drives')
@extends('layout.app')

@section('content')
<div class="container-fluid">

    <div class="row">
        <div class="col-12">
            <div class="breadcrumb-main">
                <h5 class="breadcrumb-title">
                    <a href="{{ route('staff.supplies.index') }}" class="text-muted">จัดการ Supply</a>
                    <span class="text-muted mx-1">/</span> เพิ่ม Supply
                </h5>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-xl-8 col-lg-10 col-12">

            <form action="{{ route('staff.supplies.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="card shadow-sm ">
                    <div class="card-header" style="background:linear-gradient(135deg,#5840ff 0%,#7c66ff 100%);padding:14px 20px;">
                        <h6 class="mb-0 fw-bold text-white"><i class="uil uil-building me-2"></i>ข้อมูล Supply</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">

                            {{-- บริษัทแม่ --}}
                            <div class="col-12">
                                <label class="form-label fw-600">บริษัทแม่ <span class="text-danger">*</span></label>
                                <select name="company_code" class="form-select @error('company_code') is-invalid @enderror">
                                    <option value="">-- เลือกบริษัท --</option>
                                    @foreach ($companies as $c)
                                        <option value="{{ $c->company_id }}" {{ old('company_code') == $c->company_id ? 'selected' : '' }}>
                                            {{ $c->company_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('company_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- ชื่อ Supply --}}
                            <div class="col-12">
                                <label class="form-label fw-600">ชื่อ Supply <span class="text-danger">*</span></label>
                                <input type="text" name="supply_name" class="form-control @error('supply_name') is-invalid @enderror"
                                    value="{{ old('supply_name') }}" placeholder="ชื่อ Supply">
                                @error('supply_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- ที่อยู่ --}}
                            <div class="col-12">
                                <label class="form-label fw-600">ที่อยู่</label>
                                <textarea name="supply_address" class="form-control" rows="3"
                                    placeholder="ที่อยู่ Supply">{{ old('supply_address') }}</textarea>
                            </div>

                            {{-- เบอร์ + อีเมล --}}
                            <div class="col-12">
                                <label class="form-label fw-600">เบอร์โทรศัพท์</label>
                                <input type="text" name="supply_phone" class="form-control"
                                    value="{{ old('supply_phone') }}" placeholder="0x-xxxx-xxxx">
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-600">อีเมล</label>
                                <input type="email" name="supply_email" class="form-control @error('supply_email') is-invalid @enderror"
                                    value="{{ old('supply_email') }}" >
                                @error('supply_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- สถานะ --}}
                            <div class="col-12">
                                <label class="form-label fw-600">สถานะ <span class="text-danger">*</span></label>
                                <select name="supply_status" class="form-select">
                                    <option value="1" {{ old('supply_status', '1') == '1' ? 'selected' : '' }}>ใช้งาน</option>
                                    <option value="0" {{ old('supply_status') == '0' ? 'selected' : '' }}>ปิดใช้งาน</option>
                                </select>
                            </div>

                            {{-- โลโก้ --}}
                            <div class="col-12">
                                <label class="form-label fw-600">โลโก้ Supply</label>
                                <input type="file" name="supply_logo" id="logo-input"
                                    class="form-control @error('supply_logo') is-invalid @enderror"
                                    accept="image/jpg,image/jpeg,image/png">
                                @error('supply_logo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <img id="logo-preview" src="#" class="mt-2 rounded d-none" style="max-height:80px;">
                            </div>

                        </div>
                    </div>
                </div>

                {{-- บัญชีผู้ใช้ --}}
                <div class="card shadow-sm mt-3 ">
                    <div class="card-header" style="background:linear-gradient(135deg,#0ea5e9 0%,#38bdf8 100%);padding:14px 20px;">
                        <h6 class="mb-0 fw-bold text-white"><i class="uil uil-user me-2"></i>บัญชีผู้ใช้ Supply</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">

                            {{-- Username --}}
                            <div class="col-12">
                                <label class="form-label fw-600">Username <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" name="username" id="username"
                                        class="form-control @error('username') is-invalid @enderror"
                                        value="{{ old('username') }}" autocomplete="off"
                                        placeholder="ชื่อผู้ใช้สำหรับเข้าสู่ระบบ">
                                    <button type="button" class="btn btn-outline-secondary"
                                        onclick="generateUsername()" title="สุ่ม Username">
                                        <i class="uil uil-sync me-1"></i> สุ่ม
                                    </button>
                                    @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div id="username-feedback" class="mt-1" style="display:none;"></div>
                            </div>

                            {{-- Password --}}
                            <div class="col-12">
                                <label class="form-label fw-600">รหัสผ่าน <span class="text-danger">*</span></label>
                                <input type="text" name="password" id="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    placeholder="อย่างน้อย 4 ตัวอักษร" autocomplete="off">
                                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>


                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-3 mb-20">
                    <a href="{{ route('staff.supplies.index') }}" class="btn btn-warning px-4">ยกเลิก</a>
                    <button type="submit" id="submitBtn" class="btn btn-primary px-5">
                        <i class="uil uil-save me-1"></i> บันทึก
                    </button>
                </div>

            </form>
        </div>
    </div>

</div>

<script>
// ─── Generate username (4 ตัวอักษร + 3 ตัวเลข) ───────────────────
function generateUsername() {
    const alpha  = 'ABCDEFGHIJKLMNPQRSTUVWXYZ';
    const digits = '0123456789';
    let letters  = '', nums = '';
    for (let i = 0; i < 4; i++) letters += alpha[Math.floor(Math.random() * alpha.length)];
    for (let j = 0; j < 3; j++) nums    += digits[Math.floor(Math.random() * digits.length)];
    const val = letters + nums;
    document.getElementById('username').value = val;
    checkUsername(val);
}

// ─── Toggle show/hide password ────────────────────────────────────
function togglePwd(fieldId, iconId) {
    const f = document.getElementById(fieldId), i = document.getElementById(iconId);
    f.type = f.type === 'password' ? 'text' : 'password';
    i.classList.toggle('uil-eye');
    i.classList.toggle('uil-eye-slash');
}

// ─── Logo preview ─────────────────────────────────────────────────
document.getElementById('logo-input').addEventListener('change', function () {
    const preview = document.getElementById('logo-preview');
    if (this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = e => { preview.src = e.target.result; preview.classList.remove('d-none'); };
        reader.readAsDataURL(this.files[0]);
    }
});

// ─── Username duplicate check ──────────────────────────────────────
function checkUsername(val) {
    const feedback = document.getElementById('username-feedback');
    const submitBtn = document.getElementById('submitBtn');
    if (!val) { feedback.style.display = 'none'; return; }

    $.get('/check-username', { company_user: val }, function (data) {
        if (data.exists) {
            feedback.innerHTML = '<span class="text-danger fs-13"><i class="uil uil-times-circle me-1"></i>Username นี้ถูกใช้งานแล้ว กรุณาใช้ชื่ออื่น</span>';
            submitBtn.disabled = true;
        } else {
            feedback.innerHTML = '<span class="text-success fs-13"><i class="uil uil-check-circle me-1"></i>Username นี้ใช้งานได้</span>';
            submitBtn.disabled = false;
        }
        feedback.style.display = 'block';
    });
}

$(function () {
    $('#username').on('blur', function () {
        checkUsername($(this).val().trim());
    }).on('input', function () {
        // reset state when user types
        document.getElementById('username-feedback').style.display = 'none';
        document.getElementById('submitBtn').disabled = false;
    });
});
</script>
@endsection