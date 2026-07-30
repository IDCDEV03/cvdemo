@section('title', 'ระบบตรวจมาตรฐานรถ')
@section('description', 'ID Drives')
@extends('layout.LayoutAdmin')
@section('content')
    <div class="container-fluid">
        <div class="social-dash-wrap">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb-main">
                        <h4 class="text-capitalize breadcrumb-title">หน้าหลัก</h4>
                        <div class="breadcrumb-action justify-content-center flex-wrap">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="#"><i class="las la-home"></i>Home</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">
                                        {{ trans('menu.blank-menu-title') }}</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <!-- รายการบริษัทฯ -->
                <div class="col-md-4 mb-4">
                    <a href="{{route('admin.cp_list')}}" class="text-decoration-none">
                        <div class="card shadow-sm h-100" style="border: 2px solid 	#ffd6b5; background-color: #fff1e6;">
                            <div class="card-body text-center">
                                <div class="mb-3">
                                    <img src="{{ asset('company.png') }}" alt="" width="120px">
                                </div>
                                <h5 class="card-title">รายการบริษัทฯ</h5>
                                <p class="card-text text-muted">สร้างและจัดการข้อมูลบริษัทฯว่าจ้าง</p>
                            </div>
                        </div>
                    </a>
                </div>

                          <!-- รายการบริษัทฯSup -->
                <div class="col-md-4 mb-4">
                    <a href="{{route('admin.supply_all')}}" class="text-decoration-none">
                        <div class="card shadow-sm h-100" style="border: 2px solid 	#C2B9F0; background-color: #F0EDFF;">
                            <div class="card-body text-center">
                                <div class="mb-3">
                                    <img src="{{ asset('supply.png') }}" alt="" width="120px">
                                </div>
                                <h5 class="card-title">รายการบริษัทฯ Supply</h5>
                                <p class="card-text text-muted">สร้างและจัดการข้อมูล Supply</p>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- รายการฟอร์มเช็ครถ -->
                <div class="col-md-4 mb-4">
                    <a href="#" class="text-decoration-none">
                        <div class="card shadow-sm h-100" style="border: 2px solid #ffbcd9; background-color: #fff0f5;">
                            <div class="card-body text-center">
                                   <div class="mb-3">
                                    <img src="{{ asset('form.png') }}" alt="" width="120px">
                                </div>
                                <h5 class="card-title">รายการฟอร์ม</h5>
                                <p class="card-text text-muted">สร้างและจัดการฟอร์ม</p>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- รายการช่างตรวจรถ -->
                <div class="col-md-4 mb-4">
                    <a href="#" class="text-decoration-none">
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
