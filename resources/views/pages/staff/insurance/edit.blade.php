@section('title', 'แก้ไขบริษัทประกันภัย')
@section('description', 'ID Drives')
@extends('layout.app')

@section('content')
<div class="container-fluid">

    <div class="row">
        <div class="col-12">
            <div class="breadcrumb-main">
                <h5 class="breadcrumb-title">
                    <a href="{{ route('staff.insurance.index') }}" class="text-muted">บริษัทประกันภัย</a>
                    <span class="text-muted mx-1">/</span> แก้ไข
                </h5>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-xl-6 col-lg-8 col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white fw-bold" style="font-size:15px;">
                    <i class="uil uil-edit me-2 text-primary"></i>แก้ไขบริษัทประกันภัย
                </div>
                <div class="card-body p-4">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $e)
                                    <li>{{ $e }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('staff.insurance.update', $company->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label fw-600">ชื่อบริษัทประกันภัย <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $company->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-600">สถานะ</label>
                            <div class="form-check form-switch mt-1">
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                                    {{ old('is_active', $company->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">ใช้งาน</label>
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="uil uil-save me-1"></i> บันทึก
                            </button>
                            <a href="{{ route('staff.insurance.index') }}" class="btn btn-light px-4">ยกเลิก</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection