@section('title', 'ระบบตรวจมาตรฐานรถ')
@section('description', 'ID Drives')
@extends('layout.app')
@section('content')
    <div class="container-fluid">


        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb-main">
                    <span class="fs-24 fw-bold breadcrumb-title">ลงทะเบียนบริษัท</span>
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

                        <form action="{{route('manager.company_insert')}}" method="POST" >
                            @csrf

                            <div class="mb-3">
                                <label>ชื่อบริษัท <span class="text-danger">*</span></label>
                                <input type="text" name="company_name" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label>ที่อยู่บริษัท <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="company_address" rows="3"></textarea>
                            </div>

                            <div class="mb-3">
                                <label>จังหวัด <span class="text-danger">*</span></label>
                                <select name="company_province" id="select-alerts2" class="form-control ">
                                    <option value="0" selected disabled>--กรุณาเลือกจังหวัด--</option>
                                    @foreach ($province as $item)
                                        <option value="{{ $item->name_th }}">{{ $item->name_th }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label>เบอร์โทรศัพท์ (ถ้ามี)</label>
                                        <input type="text" name="company_phone" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label>อีเมล (ถ้ามี)</label>
                                        <input type="text" name="company_email" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="border-top my-3"></div>

                            <div class="mb-3">
                                <label>กำหนด Username สำหรับเข้าใช้งาน<span class="text-danger">*</span></label>
                                <input type="text" name="company_user" id="company_user" class="form-control" required>
                               <div id="username-alert" class="alert alert-danger mt-2" style="display: none;"></div>
                            </div>

                            <div class="mb-3">
                                <label>กำหนด Password สำหรับเข้าใช้งาน<span class="text-danger">*</span></label>
                                <input type="text" name="company_password" class="form-control" required>
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