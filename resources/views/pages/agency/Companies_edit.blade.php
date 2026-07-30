@section('title', 'ระบบตรวจมาตรฐานรถ | แก้ไขข้อมูล')
@section('description', 'ID Drives')
@extends('layout.app')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb-main">
                    <span class="fs-24 fw-bold breadcrumb-title">แก้ไขข้อมูลบริษัทฯ ว่าจ้างตรวจมาตรฐานรถ</span>
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

                      
                        <form action="{{ route('companies.update', $company->company_id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT') 

                            <div class="mb-3">
                                <label>ชื่อบริษัท <span class="text-danger">*</span></label>
                                <input type="text" name="company_name" class="form-control"
                                    value="{{ old('company_name', $company->company_name) }}" required>
                            </div>

                            <div class="mb-3">
                                <label>ที่อยู่บริษัท <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="company_address" rows="3">{{ old('company_address', $company->company_address) }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label>จังหวัด <span class="text-danger">*</span></label>
                                <select name="company_province" id="select-alerts2" class="form-control">
                                    <option value="0" disabled>--กรุณาเลือกจังหวัด--</option>
                                    @foreach ($province as $item)
                                        <option value="{{ $item->name_th }}"
                                            {{ old('company_province', $company->company_province) == $item->name_th ? 'selected' : '' }}>
                                            {{ $item->name_th }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">จำนวนฟอร์มที่สร้างได้<span
                                            class="text-danger">*</span></label>
                                    <input type="number" name="form_limit" min="0" class="form-control"
                                        value="{{ old('form_limit', $company->form_limit) }}" placeholder="0">
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">วันเริ่มใช้งาน<span class="text-danger">*</span></label>
                                    <input type="date" name="start_date" class="form-control"
                                        value="{{ old('start_date', $company->start_date) }}">
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">วันหมดอายุ<span class="text-danger">*</span></label>
                                    <input type="date" name="expire_date" class="form-control"
                                        value="{{ old('expire_date', $company->expire_date) }}">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label>เบอร์โทรศัพท์ (ถ้ามี)</label>
                                        <input type="text" name="company_phone" class="form-control"
                                            value="{{ old('company_phone', $company->user_phone ?? '') }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label>อีเมล (ถ้ามี)</label>
                                        <input type="text" name="company_email" class="form-control"
                                            value="{{ old('company_email', $company->email ?? '') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <div class="form-group">
                                    <label class="il-gray fw-bold align-center mb-10">Logo บริษัท (ถ้ามี)</label>

                                    {{-- โชว์รูปเก่าถ้ามี --}}
                                    @if (!empty($company->company_logo))
                                        <div class="mb-2" id="old-logo-container">
                                            <p class="mb-1 text-muted"><small>โลโก้ปัจจุบัน:</small></p>
                                            <img src="{{ asset($company->company_logo) }}" class="img-thumbnail"
                                                style="max-height: 120px;">
                                        </div>
                                    @endif

                                    <input type="file" name="company_logo" accept="image/*" class="form-control"
                                        id="logo-input">

                                    <div class="mt-2">
                                        <p class="mb-1 text-muted d-none" id="new-logo-text">
                                            <small>โลโก้ใหม่ที่จะอัปโหลด:</small></p>
                                        <img id="logo-preview" src="#" class="img-thumbnail d-none"
                                            style="max-height: 120px;">
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 mb-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="require_user_approval"
                                        name="require_user_approval" value="1"
                                        {{ old('require_user_approval', $company->require_user_approval) == 1 ? 'checked' : '' }}>
                                    <label class="form-check-label" for="require_user_approval">
                                        อนุมัติเปิดการใช้งาน
                                    </label>
                                </div>
                            </div>

                            <div class="border-top my-3"></div>

                            <div class="mb-3">
                                <label>Username สำหรับเข้าใช้งาน<span class="text-danger">*</span></label>
                                <input type="text" name="company_user" id="company_user" class="form-control"
                                    value="{{ old('company_user', $company->username ?? '') }}" required>
                                <div id="username-alert" class="alert alert-danger mt-2" style="display: none;"></div>
                            </div>

                            <div class="mb-3">
                                <label>Password สำหรับเข้าใช้งาน (ปล่อยว่างหากไม่ต้องการเปลี่ยน)</label>
                                <input type="text" name="company_password" class="form-control"
                                    placeholder="กรอกรหัสผ่านใหม่เมื่อต้องการเปลี่ยนเท่านั้น">
                            </div>

                            <div class="border-top my-3"></div>

                            <div class="d-flex justify-content-between mt-4">
                                <button type="submit" class="fs-18 btn btn-primary px-4">อัปเดตข้อมูล</button>
                                <a href="{{ route('companies.index') }}" class="fs-18 btn btn-light px-4">ยกเลิก</a>
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
        // พรีวิวรูปภาพ
        document.getElementById('logo-input')?.addEventListener('change', function(event) {
            const input = event.target;
            const preview = document.getElementById('logo-preview');
            const newLogoText = document.getElementById('new-logo-text');

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('d-none');
                    if (newLogoText) newLogoText.classList.remove('d-none');
                };
                reader.readAsDataURL(input.files[0]);
            }
        });

        // เช็ค Username ซ้ำ
        $(document).ready(function() {
            // เก็บชื่อผู้ใช้เดิมไว้เทียบ จะได้ไม่ฟ้องว่าซ้ำถ้าพิมพ์ชื่อเดิม
            var originalUsername = $('#company_user').val();

            $('#company_user').blur(function() {
                var username = $(this).val();

                if (!username || username === originalUsername) {
                    $('#username-alert').hide();
                    return;
                }

                $.ajax({
                    url: '/check-username',
                    method: 'GET',
                    data: {
                        company_user: username
                    },
                    success: function(data) {
                        if (data.exists) {
                            $('#username-alert').text('Username นี้ถูกใช้แล้ว กรุณาใช้ชื่ออื่น')
                                .show();
                        } else {
                            $('#username-alert').hide();
                        }
                    }
                });
            });
        });
    </script>
@endpush
