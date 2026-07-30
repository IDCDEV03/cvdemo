@section('title', 'ระบบปฏิบัติการพนักงานขับรถราชการ')
@section('description', 'ID Drives')
@extends('layout.app')
@section('content')

    <div class="container-fluid">
        <div class="social-dash-wrap">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb-main">
                        <span
                            class="fs-24 fw-bold breadcrumb-title">เพิ่ม{{ $role === 'manager' ? 'หัวหน้า' : 'เจ้าหน้าที่' }}
                            (หน่วยงาน : {{ $agency->name }})</span>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card card-default mb-25">
                        <div class="card-body">
                            @if (session('error'))
                                <div class="alert alert-big alert-danger">
                                    <div class="alert-content">
                                        {{ session('error') }}
                                    </div>
                                </div>
                            @endif
                            <form action="{{ route('agency.insert_account') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="role" value="{{ $role }}">
                                <input type="hidden" name="agency_id" value="{{ Auth::id() }}">

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="a1"
                                                class="il-gray fw-bold align-center mb-10">คำนำหน้า</label>
                                            <select class="form-control px-15" id="exampleFormControlSelect1"
                                                name="prefix">
                                                <option selected value="คุณ">--เลือก--</option>
                                                <option value="นาย">นาย</option>
                                                <option value="นางสาว">นางสาว</option>
                                                <option value="นาง">นาง</option>
                                                <option value="คุณ">คุณ</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="a2" class="il-gray fw-bold align-center mb-10">ชื่อ<span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="name"
                                                class="form-control ih-medium ip-light radius-xs b-light px-15" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="a3" class="il-gray fw-bold align-center mb-10">นามสกุล</label>
                                            <input type="text" name="lastname"
                                                class="form-control ih-medium ip-light radius-xs b-light px-15" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="il-gray fw-bold align-center mb-10">Username<span
                                                    class="text-danger">*</span></label>
                                            <input type="text"
                                                class="form-control ih-medium ip-light radius-xs b-light px-15"
                                                name="username" id="username" placeholder="สำหรับใช้เข้าสู่ระบบ" required>
                                                
                                                 <div id="username-feedback" class="text-danger small mt-1"></div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="il-gray fw-bold align-center mb-10">Password<span
                                                    class="text-danger">*</span></label>
                                            <input type="text"
                                                class="form-control ih-medium ip-light radius-xs b-light px-15"
                                                name="password" placeholder="สำหรับใช้เข้าสู่ระบบ" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="il-gray fw-bold align-center mb-10">อีเมล (ถ้ามี) </label>
                                            <input type="email"
                                                class="form-control ih-medium ip-light radius-xs b-light px-15"
                                                name="email">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="il-gray fw-bold align-center mb-10">เบอร์โทรศัพท์ (ถ้ามี)</label>
                                            <input type="text"
                                                class="form-control ih-medium ip-light radius-xs b-light px-15"
                                                name="phone" maxlength="20">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="il-gray fw-bold align-center mb-10">รูปภาพโปรไฟล์ (ถ้ามี) </label>

                                            <input type="file" name="avatar" accept="image/*" class="form-control"
                                                id="avatar-input">
                                            <div class="mt-2">
                                                <img id="avatar-preview" src="#" class="img-thumbnail d-none"
                                                    style="max-height: 120px;">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="il-gray fw-bold align-center mb-10">ลายเซ็นอิเล็กทรอนิกส์
                                                (ถ้ามี)</label>
                                            <input type="file" name="signature" accept="image/*" class="form-control"
                                                id="signature-input">
                                            <div class="mt-2">
                                                <img id="signature-preview" src="#" class="img-thumbnail d-none"
                                                    style="max-height: 120px;">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex">
                                    <button type="submit"
                                        class="btn btn-success btn-default btn-squared btn-shadow-success"><i
                                            class="fas fa-save"></i> เพิ่มผู้ใช้งาน</button>
                                </div>
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
document.getElementById('username').addEventListener('blur', function () {
    const username = this.value;
    const feedback = document.getElementById('username-feedback');

    if (username.length > 0) {
        fetch("{{ route('check.username') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify({ username: username })
        })
        .then(response => response.json())
        .then(data => {
            if (data.exists) {
                feedback.textContent = 'ชื่อนี้ถูกใช้ไปแล้ว กรุณาเลือกชื่ออื่น';
            } else {
                feedback.textContent = '';
            }
        });
    } else {
        feedback.textContent = '';
    }
});
</script>

    <script>
        document.getElementById('avatar-input')?.addEventListener('change', function(event) {
            const input = event.target;
            const preview = document.getElementById('avatar-preview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('d-none');
                };
                reader.readAsDataURL(input.files[0]);
            }
        });

        document.getElementById('signature-input')?.addEventListener('change', function(event) {
            const input = event.target;
            const preview = document.getElementById('signature-preview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('d-none');
                };
                reader.readAsDataURL(input.files[0]);
            }
        });
    </script>
@endpush
