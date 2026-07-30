@section('title', 'ระบบปฏิบัติการพนักงานขับรถราชการ')
@section('description', 'ID Drives')
@extends('layout.LayoutAdmin')
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

                            <form action="{{route('admin.member.insert')}}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="role" value="{{ $role }}">
                                <input type="hidden" name="agency_id" value="{{$agency->id}}">

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="a1"
                                                class="il-gray fw-bold align-center mb-10">คำนำหน้า</label>
                                            <select class="form-control px-15" id="exampleFormControlSelect1" name="prefix">
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
                                            <label class="il-gray fw-bold align-center mb-10">อีเมล<span
                                                    class="text-danger">*</span></label>
                                            <input type="text"
                                                class="form-control ih-medium ip-light radius-xs b-light px-15" name="email"
                                                placeholder="สำหรับเข้าสู่ระบบ" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="il-gray fw-bold align-center mb-10">อีเมล<span
                                                    class="text-danger">*</span></label>
                                            <input type="text"
                                                class="form-control ih-medium ip-light radius-xs b-light px-15" name="email"
                                                placeholder="สำหรับเข้าสู่ระบบ" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="il-gray fw-bold align-center mb-10">รหัสผ่าน<span
                                                    class="text-danger">*</span></label>
                                            <input type="text"
                                                class="form-control ih-medium ip-light radius-xs b-light px-15" name="password"
                                                placeholder="สำหรับเข้าสู่ระบบ" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="il-gray fw-bold align-center mb-10">เบอร์โทรศัพท์ (ถ้ามี)</label>
                                            <input type="text"
                                                class="form-control ih-medium ip-light radius-xs b-light px-15"
                                                name="phone">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="il-gray fw-bold align-center mb-10">รูปภาพโปรไฟล์ (ถ้ามี) </label>

                                            <input type="file" name="avatar" accept="image/*" class="form-control"
                                                id="avatar-input">
                                            <div class="mt-2">
                                                <img id="avatar-preview" src="#" class="img-thumbnail d-none" style="max-height: 120px;">
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
                                                <img id="signature-preview" src="#" class="img-thumbnail d-none" style="max-height: 120px;">
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
document.getElementById('avatar-input')?.addEventListener('change', function (event) {
    const input = event.target;
    const preview = document.getElementById('avatar-preview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function (e) {
            preview.src = e.target.result;
            preview.classList.remove('d-none');
        };
        reader.readAsDataURL(input.files[0]);
    }
});

document.getElementById('signature-input')?.addEventListener('change', function (event) {
    const input = event.target;
    const preview = document.getElementById('signature-preview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function (e) {
            preview.src = e.target.result;
            preview.classList.remove('d-none');
        };
        reader.readAsDataURL(input.files[0]);
    }
});
</script>
@endpush
