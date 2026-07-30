@section('title', 'ระบบตรวจมาตรฐานรถ')
@section('description', 'ID Drives')
@extends('layout.app')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb-main">
                    <span class="fs-24 fw-bold breadcrumb-title">ข้อมูลผู้ใช้งาน</span>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card mb-25">
                    <div class="card-body">

                        <form action="#" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">คำนำหน้า<span class="text-danger">*</span></label>
                                    <input type="text" name="prefix" class="form-control"
                                        value="{{ old('prefix', $user->prefix) }}" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">ชื่อ<span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control"
                                        value="{{ old('name', $user->name) }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">นามสกุล<span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control"
                                        value="{{ old('lastname', $user->lastname) }}" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">อีเมล</label>
                                    <input type="text" name="name" class="form-control"
                                        value="{{ old('email', $user->email) }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">เบอร์โทรศัพท์</label>
                                    <input type="email" name="email" class="form-control"
                                        value="{{ old('phone', $user->phone) }}">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">เลขที่ใบอนุญาตขับขี่</label>
                                    <input type="text" name="dl_number" class="form-control" maxlength="15">
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">รูปภาพโปรไฟล์</label><br>
                                    <img id="profile-preview"
                                        src="{{ $user->profile_image ? Storage::url($user->profile_image) : '' }}"
                                        class="rounded mb-2"
                                        style="max-width: 120px; {{ $user->profile_image ? '' : 'display:none;' }}">
                                    <input type="file" name="profile_image" id="profile_image" class="form-control">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">ลายเซ็น</label><br>
                                    <img id="signature-preview"
                                        src="{{ $user->signature_image ? Storage::url($user->signature_image) : '' }}"
                                        class="rounded mb-2"
                                        style="max-width: 150px; {{ $user->signature_image ? '' : 'display:none;' }}">
                                    <input type="file" name="signature_image" id="signature_image" class="form-control">
                                </div>
                            </div>

                            <div class="mt-3 d-flex justify-content-between">
                                <a href="#" class="btn btn-outline-secondary">
                                    <i class="fas fa-key"></i> เปลี่ยนรหัสผ่าน
                                </a>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save"></i> บันทึกข้อมูล
                                </button>
                            </div>
                        </form>

                    </div>
                </div>
            </div> 
        </div>

    </div>
@endsection
