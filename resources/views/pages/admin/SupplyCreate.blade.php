@section('title', 'ระบบตรวจมาตรฐานรถ')
@section('description', 'ID Drives')
@extends('layout.LayoutAdmin')
@section('content')
    <div class="container-fluid">


        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb-main">
                    <span class="fs-24 fw-bold breadcrumb-title">ลงทะเบียนบริษัทขนส่ง (Supply)</span>                    
                </div>              
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                  
                <div class="card mb-25">
                    <div class="card-body">
                        
                         <div class="alert alert-info mb-20 fw-bold fs-18">บริษัทฯว่าจ้าง : {{$company_name->name}} </div>

                        @if (session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        <form action="{{route('admin.sup_insert')}}" method="POST" enctype="multipart/form-data">
                            @csrf

                  <input type="hidden" name="company_code" value="{{ request()->id }}">
                            <div class="mb-3">
                                <label>ชื่อ Supply <span class="text-danger">*</span></label>
                                <input type="text" name="supply_name" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label>ที่อยู่ Supply <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="supply_address" rows="3"></textarea>
                            </div>

                         
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label>เบอร์โทรศัพท์ (ถ้ามี)</label>
                                        <input type="text" name="supply_phone" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label>อีเมล (ถ้ามี)</label>
                                        <input type="text" name="supply_email" class="form-control">
                                    </div>
                                </div>
                            </div>

                                <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="il-gray fw-bold align-center mb-10">Logo บริษัท (ถ้ามี) </label>

                                            <input type="file" name="supply_logo" accept="image/*" class="form-control"
                                                id="logo-input">
                                            <div class="mt-2">
                                                <img id="logo-preview" src="#" class="img-thumbnail d-none"
                                                    style="max-height: 120px;">
                                            </div>
                                        </div>
                                    </div>

                            <div class="border-top my-3"></div>

                            <div class="mb-3">
                                <label>กำหนด Username สำหรับเข้าใช้งาน<span class="text-danger">*</span></label>
                                   <button type="button" class="btn btn-xs btn-outline-secondary mb-2 mt-2" onclick="generateRandom('company_user')">สุ่ม username</button> 
                                <input type="text" name="company_user" id="company_user" class="form-control" required>
                               <div id="username-alert" class="alert alert-danger mt-2" style="display: none;"></div>                          
                            </div>

                            <div class="mb-3">
                                <label>กำหนด Password สำหรับเข้าใช้งาน<span class="text-danger">*</span></label>
                                <input type="text" name="supply_password" class="form-control" required>
                            </div>

                            <div class="border-top my-3"></div>

                            <button type="submit" class="fs-18 btn btn-block btn-success">บันทึกข้อมูล</button>
                        </form>


                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
@push('scripts')
<script>
function generateRandom(fieldId) {
  
    let letters = '';
    const alphabet = 'ABCDEFGHIJKLMNPQRSTUVWXYZ';
    for (let i = 0; i < 4; i++) {
        letters += alphabet.charAt(Math.floor(Math.random() * alphabet.length));
    }

     let numbers = '';
    const digits = '0123456789';
    for (let j = 0; j < 3; j++) {
        numbers += digits.charAt(Math.floor(Math.random() * digits.length));
    }
     document.getElementById(fieldId).value = letters + numbers;
}
   
document.getElementById('logo-input')?.addEventListener('change', function (event) {
    const input = event.target;
    const preview = document.getElementById('logo-preview');
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
<script>
$(document).ready(function () {
    $('#company_user').blur(function () {
        var username = $(this).val();
        if (!username) {
            $('#username-alert').hide();
            return;
        }

        $.ajax({
            url: '/check-username',
            method: 'GET',
            data: { company_user: username },
            success: function (data) {
                if (data.exists) {
                    $('#username-alert')
                        .text('Username นี้ถูกใช้แล้ว กรุณาใช้ชื่ออื่น')
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