@section('title', 'แก้ไขบัญชีผู้ใช้')
@section('description', 'ID Drives')
@extends('layout.app')

@section('content')
<div class="container-fluid">

    <div class="row">
        <div class="col-lg-12">
            <div class="breadcrumb-main">
                <h5 class="text-capitalize breadcrumb-title">แก้ไขบัญชีผู้ใช้</h5>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-xl-6 col-lg-8 col-12">

            {{-- Alert --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-20" role="alert">
                    <strong><i class="uil uil-check-circle"></i></strong> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-20" role="alert">
                    <strong><i class="uil uil-exclamation-triangle"></i></strong> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card shadow-sm">
                <div class="card-header" style="background: linear-gradient(135deg,#5840ff 0%,#7c66ff 100%); padding:14px 20px;">
                    <h6 class="mb-0 fw-bold text-white"><i class="uil uil-user-circle me-2"></i>ข้อมูลบัญชีผู้ใช้</h6>
                </div>
                <div class="card-body p-4">

                    <form action="{{ route('company.profile.update') }}" method="POST" autocomplete="off">
                        @csrf
                        @method('PUT')

                        {{-- Username --}}
                        <div class="mb-3">
                            <label class="form-label fw-600">ชื่อผู้ใช้ (Username) <span class="text-danger">*</span></label>
                            <input type="text" name="username" class="form-control @error('username') is-invalid @enderror"
                                value="{{ old('username', $user->username) }}" autocomplete="off">
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div class="mb-3">
                            <label class="form-label fw-600">อีเมล</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email', $user->email) }}" autocomplete="off">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">
                        <p class="text-muted fs-13 mb-3"><i class="uil uil-lock me-1"></i>เปลี่ยนรหัสผ่าน — เว้นว่างไว้หากไม่ต้องการเปลี่ยน</p>

                        {{-- Password --}}
                        <div class="mb-3">
                            <label class="form-label fw-600">รหัสผ่านใหม่</label>
                            <div class="input-group">
                                <input type="password" name="password" id="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    placeholder="อย่างน้อย 4 ตัวอักษร" autocomplete="new-password">
                                <button type="button" class="btn btn-outline-secondary px-3" onclick="togglePwd('password','eyePassword')">
                                    <i class="uil uil-eye" id="eyePassword"></i>
                                </button>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Confirm Password --}}
                        <div class="mb-4">
                            <label class="form-label fw-600">ยืนยันรหัสผ่านใหม่</label>
                            <div class="input-group">
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                    class="form-control" placeholder="พิมพ์รหัสผ่านอีกครั้ง" autocomplete="new-password">
                                <button type="button" class="btn btn-outline-secondary px-3" onclick="togglePwd('password_confirmation','eyeConfirm')">
                                    <i class="uil uil-eye" id="eyeConfirm"></i>
                                </button>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('company.index') }}" class="btn btn-light px-4">ยกเลิก</a>
                            <button type="submit" class="btn btn-primary px-5">
                                <i class="uil uil-save me-1"></i> บันทึก
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
function togglePwd(fieldId, iconId) {
    const field = document.getElementById(fieldId);
    const icon  = document.getElementById(iconId);
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.replace('uil-eye', 'uil-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.replace('uil-eye-slash', 'uil-eye');
    }
}
</script>
@endsection