@section('title', 'ระบบตรวจมาตรฐานรถ')
@section('description', 'ID Drives')
@extends('layout.LayoutAdmin')
@section('content')

@php
    $roleLabels = [
        'agency' => 'หน่วยงาน',
        'manager' => 'หัวหน้า',
        'user' => 'เจ้าหน้าที่หน่วยงาน',
        'company' => 'บริษัทฯว่าจ้าง',
        'supply' => 'บริษัทฯ Supply',
        'staff' => 'เจ้าหน้าที่',
        'inspector' => 'ช่างตรวจ',
    ];
@endphp

    <div class="container-fluid">
        <div class="social-dash-wrap">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb-main">
                        <span class="fs-24 fw-bold breadcrumb-title">แก้ไขผู้ใช้งาน</span>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card card-default mb-25">
                        <div class="card-body">

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @if ($roleLocked)
                                <div class="alert alert-info">
                                    ผู้ใช้งานนี้เป็น role "{{ $roleLabels[$user->role->value] ?? $user->role->value }}"
                                    ซึ่งมีข้อมูลเฉพาะทางผูกอยู่ ไม่สามารถเปลี่ยน role จากหน้านี้ได้
                                </div>
                            @endif

                            <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="il-gray fw-bold align-center mb-10">คำนำหน้า</label>
                                            <select class="form-control px-15" name="prefix">
                                                @foreach (['คุณ', 'นาย', 'นางสาว', 'นาง'] as $p)
                                                    <option value="{{ $p }}" {{ old('prefix', $user->prefix) === $p ? 'selected' : '' }}>{{ $p }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="il-gray fw-bold align-center mb-10">ชื่อ<span class="text-danger">*</span></label>
                                            <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                                class="form-control ih-medium ip-light radius-xs b-light px-15" required>
                                        </div>
                                    </div>

                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label class="il-gray fw-bold align-center mb-10">นามสกุล</label>
                                            <input type="text" name="lastname" value="{{ old('lastname', $user->lastname) }}"
                                                class="form-control ih-medium ip-light radius-xs b-light px-15">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="il-gray fw-bold align-center mb-10">อีเมล</label>
                                            <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                                class="form-control ih-medium ip-light radius-xs b-light px-15">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="il-gray fw-bold align-center mb-10">Username (ถ้ามี)</label>
                                            <input type="text" name="username" value="{{ old('username', $user->username) }}"
                                                class="form-control ih-medium ip-light radius-xs b-light px-15">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="il-gray fw-bold align-center mb-10">รหัสผ่านใหม่ (เว้นว่างถ้าไม่เปลี่ยน)</label>
                                            <input type="password" name="password"
                                                class="form-control ih-medium ip-light radius-xs b-light px-15">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="il-gray fw-bold align-center mb-10">เบอร์โทรศัพท์ (ถ้ามี)</label>
                                            <input type="text" name="user_phone" value="{{ old('user_phone', $user->user_phone) }}"
                                                class="form-control ih-medium ip-light radius-xs b-light px-15">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="il-gray fw-bold align-center mb-10">Role<span class="text-danger">*</span></label>
                                            @if ($roleLocked)
                                                <input type="text" class="form-control px-15"
                                                    value="{{ $roleLabels[$user->role->value] ?? $user->role->value }}" disabled>
                                            @else
                                                <select class="form-control px-15" name="role" required>
                                                    @foreach ($roles as $role)
                                                        <option value="{{ $role->value }}" {{ old('role', $user->role->value) === $role->value ? 'selected' : '' }}>
                                                            {{ $roleLabels[$role->value] ?? $role->value }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="il-gray fw-bold align-center mb-10">สถานะ</label>
                                            <select class="form-control px-15" name="user_status">
                                                <option value="1" {{ old('user_status', (string) $user->user_status) === '1' ? 'selected' : '' }}>ใช้งานอยู่</option>
                                                <option value="2" {{ old('user_status', (string) $user->user_status) === '2' ? 'selected' : '' }}>ปิดใช้งาน</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex">
                                    <button type="submit"
                                        class="btn btn-success btn-default btn-shadow-success btn-squared">
                                        <i class="fas fa-save"></i> บันทึกการแก้ไข
                                    </button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
