@section('title', 'ระบบตรวจมาตรฐานรถ')
@section('description', 'ID Drives')
@extends('layout.app')
@section('content')

    <div class="container-fluid">


        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb-main">
                    <span class="fs-24 fw-bold breadcrumb-title">แก้ไขข้อมูลรถ</span>
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

                        <form action="{{route('user.veh_update')}}" method="POST">
                            @csrf
                          <input type="hidden" name="car_id" value="{{$vehicle->car_id}}">
                            <div class="mb-3">
                                <label class="il-gray fs-16 fw-500 align-center mb-10">ทะเบียนรถ                                
                                    </label>
                                        <small class="text-muted d-block mb-2">เฉพาะ Admin เท่านั้น ที่สามารถแก้ไขทะเบียนรถได้</small>

                                <input type="text" name="plate"
                                    class="form-control ih-medium ip-light radius-xs b-light px-15" value="{{$vehicle->car_plate}}" disabled>
                            </div>
                       

                            <div class="mb-3">
                                <label class="il-gray fs-16 fw-500 align-center mb-10">ยี่ห้อรถ</label>
                                      <input type="text" name="car_brand"
                                    class="form-control ih-medium ip-light radius-xs b-light px-15" value="{{$vehicle->car_brand}}" required>
                            </div>

                            <div class="mb-3">
                                <label class="il-gray fs-16 fw-500 align-center mb-10">รุ่นรถ (Model)</label>
                                  <small class="text-muted d-block mb-2">เช่น Victor500, FXZ360</small>
                                <input type="text" name="car_model"
                                    class="form-control ih-medium ip-light radius-xs b-light px-15" value="{{$vehicle->car_model}}">
                            </div>


                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="a8"
                                            class="il-gray fs-16 fw-500 align-center mb-10">หมายเลขข้างรถ</label>
                                             <small class="text-muted d-block mb-2">เช่น ME200, PTS125</small>
                                        <input type="text" name="car_number_record"
                                            class="form-control ih-medium ip-light radius-xs b-light px-15" id="a8" value="{{$vehicle->car_number_record}}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="a9"
                                            class="il-gray fs-16 fw-500 align-center mb-10">ปีที่เริ่มใช้งานครั้งแรกหรือจดทะเบียนครั้งแรก</label>
                                            <small class="text-muted d-block mb-2">ระบุปี พ.ศ. เช่น 2545</small>
                                        <input type="text" name="car_age"
                                            class="form-control ih-medium ip-light radius-xs b-light px-15" id="a9" value="{{$vehicle->car_age}}">
                                    </div>
                                </div>
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

