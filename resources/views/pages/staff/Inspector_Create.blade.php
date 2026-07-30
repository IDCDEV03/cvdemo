@section('title', 'ระบบตรวจมาตรฐานรถ')
@section('description', 'ID Drives')
@extends('layout.app')
@section('content')
    <div class="container-fluid">


        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb-main">
                    <span class="fs-24 fw-bold breadcrumb-title">ลงทะเบียนช่างตรวจ</span>                    
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

                        <form action="{{route('staff.ins_store')}}" method="POST" >
                            @csrf
 <div class="row">
                              <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="a1"
                                                class="il-gray fw-bold align-center mb-10">คำนำหน้า<span
                                                    class="text-danger">*</span></label>
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
                                            <label for="a3" class="il-gray fw-bold align-center mb-10">นามสกุล<span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="lastname"
                                                class="form-control ih-medium ip-light radius-xs b-light px-15" required>
                                        </div>
                                    </div>
 </div>
                            <div class="mb-3">
                                 <label for="a1"
                                                class="il-gray fw-bold align-center mb-10">สังกัด Supply<span
                                                    class="text-danger">*</span></label>
                                      <select name="supply_id" id="select-alerts2" class="form-control ">
                                    <option value="0" selected disabled>--เลือก Supply--</option>
                                    @foreach ($supply_list as $item)
                                        <option value="{{ $item->sup_id }}">{{ $item->supply_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                         
                            <div class="row">
                                   <div class="col-md-6">
                                    <div class="mb-3">
                                        <label>เลขที่ใบขับขี่</label>
                                        <input type="text" name="dl_number" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label>เบอร์โทรศัพท์ (ถ้ามี)</label>
                                        <input type="text" name="ins_phone" class="form-control">
                                    </div>
                                </div>                             
                            </div>

                               <div class="row">
                                   <div class="col-md-6">
                                    <div class="mb-3">
                                        <label>ปี พ.ศ. เกิด</label>
                                        <input type="text" name="ins_birthyear" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label>ประสบการณ์ผู้ขับขี่ (ระบุเป็นจำนวนปี)</label>
                                        <input type="text" name="ins_experience" class="form-control">
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
                                <input type="text" name="inspector_password" id="inspector_password" class="form-control" required>
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
    const chars = 'abcdefghijklmnpqrstuvwxyz0123456789';
    let result = '';
    for (let i = 0; i < 8; i++) {
        result += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    document.getElementById(fieldId).value = result;
}
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