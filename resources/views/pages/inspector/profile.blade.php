@section('title', 'แก้ไขโปรไฟล์')
@section('description', 'Inspector Profile')
@extends('layout.app')

@push('styles')
<style>
    .profile-card {
        border-radius: 16px;
        box-shadow: 0 2px 16px rgba(0,0,0,0.08);
        border: none;
    }
    .section-title {
        font-size: 16px;
        font-weight: 700;
        color: #8c8c8c;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 16px;
        padding-bottom: 8px;
        border-bottom: 1px solid #f0f0f0;
    }
    .signature-preview-box {
        border: 2px dashed #d9d9d9;
        border-radius: 10px;
        padding: 12px;
        background: #fafafa;
        min-height: 120px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .signature-preview-box img {
        max-height: 110px;
        max-width: 100%;
        object-fit: contain;
    }
    .age-badge {
        display: inline-block;
        background: #e6f4ff;
        color: #1677ff;
        border-radius: 20px;
        padding: 2px 14px;
        font-size: 13px;
        font-weight: 600;
        margin-left: 8px;
    }
    .ins-id-badge {
        font-family: monospace;
        background: #f5f5f5;
        border-radius: 6px;
        padding: 4px 10px;
        font-size: 13px;
        color: #555;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <div class="breadcrumb-main">
                <h4 class="text-capitalize breadcrumb-title">แก้ไขโปรไฟล์</h4>
                <div class="breadcrumb-action justify-content-center flex-wrap">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('ins-dashboard') }}">หน้าหลัก</a></li>
                            <li class="breadcrumb-item active" aria-current="page">แก้ไขโปรไฟล์</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <form action="{{ route('ins.profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">

            {{-- ซ้าย: ข้อมูลบัญชี --}}
            <div class="col-lg-6 mb-4">
                <div class="card profile-card h-100">
                    <div class="card-body p-4">
                        <p class="section-title"><i class="uil uil-lock me-1"></i> ข้อมูลบัญชีผู้ใช้</p>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">ชื่อผู้ใช้ (Username) <span class="text-danger">*</span></label>
                            <input type="text" name="username" class="form-control @error('username') is-invalid @enderror"
                                value="{{ old('username', $user->username) }}" required>
                            @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">อีเมล</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email', $user->email) }}">
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                     <div class="border-top my-3"></div>
                        <p class="section-title mt-3 ">เปลี่ยนรหัสผ่าน</p>
                        <p class="text-danger" style="font-size:13px;">หากไม่ต้องการเปลี่ยนรหัสผ่าน ให้เว้นว่างไว้</p>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">รหัสผ่านใหม่</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                                placeholder="อย่างน้อย 6 ตัวอักษร" autocomplete="new-password">
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">ยืนยันรหัสผ่านใหม่</label>
                            <input type="password" name="password_confirmation" class="form-control"
                                placeholder="กรอกรหัสผ่านอีกครั้ง" autocomplete="new-password">
                        </div>
                    </div>
                </div>
            </div>

            {{-- ขวา: ข้อมูลส่วนตัว --}}
            <div class="col-lg-6 mb-4">
                <div class="card profile-card h-100">
                    <div class="card-body p-4">
                        <p class="section-title"><i class="uil uil-user me-1"></i> ข้อมูลส่วนตัว</p>

                        <div class="row mb-3">
                            <div class="col-4">
                                <label class="form-label fw-semibold">คำนำหน้า</label>
                                <select name="prefix" class="form-select @error('prefix') is-invalid @enderror">
                                    <option value="">-- เลือก --</option>
                                    @foreach(['-', 'นาย', 'นาง', 'นางสาว'] as $p)
                                        <option value="{{ $p }}" {{ old('prefix', $user->prefix) == $p ? 'selected' : '' }}>{{ $p }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-8">
                                <label class="form-label fw-semibold">ชื่อ <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $user->name) }}" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">นามสกุล<span class="text-danger">*</span></label>
                            <input type="text" name="lastname" class="form-control @error('lastname') is-invalid @enderror"
                                value="{{ old('lastname', $user->lastname) }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">เบอร์โทรศัพท์</label>
                            <input type="text" name="user_phone" class="form-control @error('user_phone') is-invalid @enderror"
                                value="{{ old('user_phone', $user->user_phone) }}" maxlength="10">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                ปีเกิด (พ.ศ.)
                                @if($age !== null)
                                    <span class="age-badge">อายุ {{ $age }} ปี</span>
                                @endif
                            </label>
                            <input type="text" name="ins_birthyear"
                                class="form-control @error('ins_birthyear') is-invalid @enderror"
                                value="{{ old('ins_birthyear', $inspector->ins_birthyear ?? '') }}"
                                placeholder="เช่น 2530" maxlength="4">
                            @error('ins_birthyear')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">เลขที่ใบขับขี่</label>
                            <input type="text" name="dl_number"
                                class="form-control @error('dl_number') is-invalid @enderror"
                                value="{{ old('dl_number', $inspector->dl_number ?? '') }}"
                                placeholder="กรอกเลขที่ใบขับขี่">
                            @error('dl_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- ลายเซ็น --}}
            <div class="col-12 mb-4">
                <div class="card profile-card">
                    <div class="card-body p-4">
                        <p class="section-title"><i class="uil uil-pen me-1"></i> ลายเซ็น</p>
                        <div class="row align-items-start">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">ลายเซ็นปัจจุบัน</label>
                                <div class="signature-preview-box">
                                    @if($user->signature_image)
                                        <img src="{{ asset($user->signature_image) }}" alt="ลายเซ็น" id="sig-preview">
                                    @else
                                        <div id="sig-no-image" class="text-center text-muted">
                                            <i class="uil uil-image-slash fs-3 d-block mb-1"></i>
                                            ยังไม่มีลายเซ็น
                                        </div>
                                        <img src="" alt="preview" id="sig-preview" style="display:none; max-height:110px; max-width:100%; object-fit:contain;">
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label fw-semibold">อัปโหลดลายเซ็นใหม่</label>
                                <input type="file" name="signature_image" id="sig-input"
                                    class="form-control @error('signature_image') is-invalid @enderror"
                                    accept="image/png,image/jpeg,image/jpg">
                                @error('signature_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <small class="text-muted mt-1 d-block">
                                    รองรับ JPG, JPEG, PNG &bull; ขนาดไม่เกิน 2MB<br>
                                    แนะนำรูปลายเซ็นพื้นหลังสีขาว
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="row">
            <div class="col-12 d-flex justify-content-end gap-2 mb-5">
                <a href="{{ route('ins-dashboard') }}" class="btn btn-outline-secondary px-4">ยกเลิก</a>
                <button type="submit" class="btn btn-primary px-5">
                    <i class="uil uil-check me-1"></i> บันทึกข้อมูล
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('sig-input').addEventListener('change', function (e) {
        const file = e.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function (ev) {
            const preview = document.getElementById('sig-preview');
            const noImage = document.getElementById('sig-no-image');

            preview.src = ev.target.result;
            preview.style.display = 'block';
            if (noImage) noImage.style.display = 'none';
        };
        reader.readAsDataURL(file);
    });
</script>
@endpush