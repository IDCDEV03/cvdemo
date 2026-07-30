@section('title', 'ระบบตรวจมาตรฐานรถ')
@section('description', 'ID Drives')
@extends('layout.app')
@section('content')
    <div class="container-fluid">
        <div class="social-dash-wrap">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb-main">
                        <h4 class="text-capitalize breadcrumb-title">หน้าหลัก</h4>
                       
                </div>
            </div>

            <div class="row">  
                <!-- รายการรถ -->
                <div class="col-md-4 mb-4">
                    <a href="{{route('supply.veh_list')}}" class="text-decoration-none">
                        <div class="card shadow-sm h-100" style="border: 2px solid 	#ffd6b5; background-color: #fff1e6;">
                            <div class="card-body text-center">
                                <div class="mb-3">
                                    <img src="{{ asset('truck.png') }}" alt="" width="120px">
                                </div>
                                <h5 class="card-title">รายการรถ</h5>
                                <p class="card-text text-muted">สร้างและจัดการข้อมูลทะเบียนรถ</p>
                            </div>
                        </div>
                    </a>
                </div>

              
                <!-- รายการช่างตรวจรถ -->
                <div class="col-md-4 mb-4">
                    <a href="{{route('supply.inspector_list', ['id' => Auth::user()->user_id])}}" class="text-decoration-none">
                        <div class="card shadow-sm h-100" style="border: 2px solid #b8e1c2; background-color: #ebfdf3;">
                            <div class="card-body text-center">
                                   <div class="mb-3">
                                    <img src="{{ asset('inspector.png') }}" alt="" width="120px">
                                </div>
                                <h5 class="card-title">รายการช่างตรวจ</h5>
                                <p class="card-text text-muted">สร้างและจัดการข้อมูลช่างตรวจ</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
