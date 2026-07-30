@section('title', 'ระบบตรวจมาตรฐานรถ')
@section('description', 'ID Drives')
@extends('layout.app')
@section('content')
    <div class="container-fluid">
        <div class="social-dash-wrap">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb-main">
                        <h4 class="text-capitalize breadcrumb-title">สร้างฟอร์มใหม่</h4>

                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card mb-25">
                        <div class="card-body">
                            @if (session('error'))
                                <div class="alert alert-big alert-danger">
                                    <div class="alert-content">
                                        {{ session('error') }}
                                    </div>
                                </div>
                            @endif
                            <form action="{{route('agency.insert_form')}}" method="POST">
                                @csrf
                                <div class="form-group row mb-25">
                                    <div class="col-sm-3 d-flex aling-items-center">
                                        <label class=" col-form-label color-dark align-center">รหัสฟอร์ม (ถ้ามี) </label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control ih-medium ip-gray radius-xs"
                                            id="form_code" name="form_code">
                                    </div>
                                </div>
                                <div class="form-group row mb-25">
                                    <div class="col-sm-3 d-flex aling-items-center">
                                        <label class="col-form-label color-dark align-center">ชื่อฟอร์ม<span
                                                class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control ih-medium ip-gray radius-xs"
                                            id="form_name" name="form_name" required>
                                    </div>
                                </div>
                                <div class="form-group row mb-25">
                                    <div class="col-sm-3 d-flex aling-items-center">
                                        <label class="col-form-label  color-dark align-center">ประเภทฟอร์ม<span
                                        class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-sm-9">
                                        <select name="form_category" class="form-control ih-medium ip-gray radius-xs">
                                            <option value="" selected disabled>-- กรุณาเลือกประเภทรถ --</option>
                                            @foreach ($car_type as $data)
                                                <option value="{{ $data->id }}">{{ $data->vehicle_type }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-9">
                                    <div class="layout-button mt-25">
                                        <button type="submit"
                                            class="btn btn-primary btn-default btn-squared">ถัดไป</button>
                                    </div>
                                </div>

                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
