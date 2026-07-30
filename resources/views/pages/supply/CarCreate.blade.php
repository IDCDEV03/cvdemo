@section('title', 'ระบบตรวจมาตรฐานรถ')
@section('description', 'ID Drives')
@extends('layout.app')
@section('content')

    <div class="container-fluid">


        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb-main">
                    <span class="fs-24 fw-bold breadcrumb-title">ลงทะเบียนรถ</span>
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

                        <form action="{{route('supply.veh_store')}}" method="POST">
                            @csrf
                          
                            <div class="mb-3">
                                <label class="il-gray fs-16 fw-500 align-center mb-10">ทะเบียนรถ <span
                                        class="text-danger">*</span>                                   
                                    </label>
                                        <small class="text-muted d-block mb-2">กรอกเฉพาะเลขทะเบียน เช่น 85-2053 , 71-8600</small>

                                <input type="text" name="plate"
                                    class="form-control ih-medium ip-light radius-xs b-light px-15" maxlength="10" required>
                            </div>

                            <div class="mb-3">
                                <label class="il-gray fs-16 fw-500 align-center mb-10">จังหวัดทะเบียนรถ <span
                                        class="text-danger">*</span></label>
                                <select name="province" id="select-alerts2" class="form-control ">
                                    <option value="0" selected disabled>--กรุณาเลือกจังหวัด--</option>
                                    @foreach ($province as $item)
                                        <option value="{{ $item->name_th }}">{{ $item->name_th }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="il-gray fs-16 fw-500 align-center mb-10">ยี่ห้อรถ <span
                                        class="text-danger">*</span></label>
                                <select name="car_brand" id="select-option2" class="form-control ">
                                    <option value="0" selected disabled>--กรุณาเลือกยี่ห้อรถ--</option>
                                    @foreach ($car_brand as $brand)
                                        <option value="{{ $brand->brand_name }}">{{ $brand->brand_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="il-gray fs-16 fw-500 align-center mb-10">รุ่นรถ (Model)</label>
                                  <small class="text-muted d-block mb-2">เช่น Victor500, FXZ360</small>
                                <input type="text" name="car_model"
                                    class="form-control ih-medium ip-light radius-xs b-light px-15">
                            </div>


                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="a8"
                                            class="il-gray fs-16 fw-500 align-center mb-10">หมายเลขข้างรถ</label>
                                             <small class="text-muted d-block mb-2">เช่น ME200, PTS125</small>
                                        <input type="text" name="car_number_record"
                                            class="form-control ih-medium ip-light radius-xs b-light px-15" id="a8">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="a9"
                                            class="il-gray fs-16 fw-500 align-center mb-10">ปีที่เริ่มใช้งานครั้งแรกหรือจดทะเบียนครั้งแรก</label>
                                            <small class="text-muted d-block mb-2">ระบุปี พ.ศ. เช่น 2545</small>
                                        <input type="text" name="car_age"
                                            class="form-control ih-medium ip-light radius-xs b-light px-15" id="a9">
                                    </div>
                                </div>
                            </div>                   
                           

                            <div class="mb-3">
                                <label class="il-gray fs-16 fw-500 align-center mb-10">ประเภทรถ <span
                                        class="text-danger">*</span></label>
                                  <select class="form-control px-15" name="car_type" id="car_type" required>
                                    <option value="0" selected disabled>--เลือกประเภทรถ--</option>
                                    @foreach ($car_type as $data)
                                        <option value="{{ $data->id }}">{{ $data->vehicle_type }}</option>
                                    @endforeach
                                </select>
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

