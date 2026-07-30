@section('title', 'ระบบปฏิบัติการพนักงานขับรถราชการ')
@section('description', 'ID Drives')
@extends('layout.LayoutAdmin')
@section('content')

    <div class="container-fluid">
        <div class="social-dash-wrap">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb-main">
                        <span class="fs-24 fw-bold breadcrumb-title">แก้ไขหน่วยงาน</span>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card card-default mb-25">
                        <div class="card-body">

                            <form action="{{route('admin.agency.update',$agency->id)}}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="mb-3">
                                    <label for="name" class="form-label">ชื่อหน่วยงาน<span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name"
                                        class="form-control @error('name') is-invalid @enderror"
                                        value="{{ old('name', $agency->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">อีเมล (สำหรับใช้ Login เข้าสู่ระบบ) <span
                                            class="text-danger">*</span></label>
                                    <input type="email" name="email" id="email"
                                        class="form-control @error('email') is-invalid @enderror"
                                        value="{{ old('email', $agency->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label">รหัสผ่าน (ถ้าไม่เปลี่ยนให้เว้นไว้) </label>
                                    <input type="password" name="password" id="password"
                                        class="form-control @error('password') is-invalid @enderror">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="phone" class="form-label">เบอร์โทรศัพท์ (ถ้ามี)</label>
                                    <input type="text" name="phone" id="phone"class="form-control"
                                        value="{{ old('phone', $agency->user_phone) }}">
                                </div>

                              

                                {{-- preview --}}
                                @if ($agency->logo_agency)
                                    <div class="mb-3">
                                        <label class="form-label">โลโก้เดิม</label><br>
                                   
                                        <img src="{{ asset($agency->logo_agency) }}" alt="โลโก้เดิม" class="img-thumbnail"
                                            style="max-height: 100px;">
                                    </div>
                                @endif

                                  <div class="mb-3">
                                    <label for="logo" class="form-label">โลโก้หน่วยงาน</label>
                                    <input type="file" name="logo" id="logo"
                                        class="form-control @error('logo') is-invalid @enderror" accept="image/*">
                                    <div class="text-muted fs-12 mt-2">ขนาดไฟล์ไม่เกิน 10MB</div>

                                    @error('logo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3 d-none" id="preview-wrapper">
                                    <label class="form-label">โลโก้ใหม่ (Preview)</label><br>
                                    <img id="logo-preview" class="img-thumbnail" style="max-height: 100px;">
                                </div>


                                <button type="submit" class="btn btn-secondary">
                                 บันทึกการแก้ไข
                                </button>

                            </form>


                        </div>
                    </div>


                </div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script>
       document.getElementById('logo').addEventListener('change', function (event) {
        const input = event.target;
        const preview = document.getElementById('logo-preview');
        const wrapper = document.getElementById('preview-wrapper');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
                wrapper.classList.remove('d-none');
            };
            reader.readAsDataURL(input.files[0]);
        }
    });
    </script>
@endpush
