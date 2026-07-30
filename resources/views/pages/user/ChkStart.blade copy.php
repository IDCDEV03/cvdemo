@section('title', 'ระบบ E-Checker')
@section('description', 'ID Drives')
@extends('layout.app')
@section('content')
    <div class="container-fluid">
        <div class="social-dash-wrap">

            <div class="row mt-4">
                <div class="col-md-12">
                    <div class=" alert alert-primary " role="alert">
                        <div class="alert-content">
                            <span class="fs-20 fw-bold"> {{ $forms->form_name }} </span>
                        </div>
                    </div>

                    <div class="card mb-25">
                        <div class="card-body">                         

                            @include('partials._steps', ['currentStep' => 1])

                            @if (session('error'))
                                <div class="alert alert-danger">{{ session('error') }}</div>
                            @endif
                            
                            <form action="{{route('user.chk_insert_step1')}}" method="POST" enctype="multipart/form-data">
                                @csrf

                                <input type="hidden" name="form_id" value="{{request()->id}}">

                                <div class="mb-3">
                                    <label>ทะเบียนรถ <span class="text-danger">*</span></label>
                                    <input type="text" name="plate" class="form-control" maxlength="10" required>
                                </div>

                                <div class="mb-3">
                                    <label>จังหวัดทะเบียนรถ <span class="text-danger">*</span></label>
                                    <select name="province" id="select-alerts2" class="form-control ">
                                        <option value="0">--กรุณาเลือกจังหวัด--</option>
                                        @foreach ($province as $item)
                                            <option value="{{$item->name_th}}">{{$item->name_th}}</option>
                                        @endforeach                                     
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label>ประเภทรถ <span class="text-danger">*</span></label>
                                    <select class="form-control px-15" name="vehicle_type" id="vehicle_type" required>
                                        <option value="0" selected disabled>--เลือกประเภทรถ--</option>
                                        @foreach ($car_type as $data)
                                            <option value="{{ $data->id }}">{{ $data->vehicle_type }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label>วันหมดอายุภาษี <span class="text-danger">*</span></label>
                                    <input type="date" name="tax_exp" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label>ภาพถ่ายรถ <span class="text-danger">*</span></label>
                                    <input type="file" name="vehicle_image" class="form-control" accept="image/*"
                                        required>
                                </div>

                                <div class="border-top my-3"></div>

                                <button type="submit" class="btn btn-block btn-secondary">เริ่มการตรวจ <i
                                        class="fas fa-arrow-right"></i></button>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
