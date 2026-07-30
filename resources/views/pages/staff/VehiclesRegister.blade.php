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

                        <form action="{{ route('staff.veh_create') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label class="il-gray fs-16 fw-500 align-center mb-10">บริษัทฯ ผู้ว่าจ้าง <span
                                        class="text-danger">*</span></label>
                                <select name="company_id" id="company_id" class="form-control">
                                    <option value="">-- เลือกบริษัทหลัก --</option>
                                    @foreach ($company_list as $company)
                                        <option value="{{ $company->company_code }}">{{ $company->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="il-gray fs-16 fw-500 align-center mb-10">บริษัทฯ Supply <span
                                        class="text-danger">*</span></label>
                                <select name="supply_id" id="supply_id" class="form-control">
                                    <option value="">-- เลือกบริษัท Supply --</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="il-gray fs-16 fw-500 align-center mb-10">ทะเบียนรถ <span
                                        class="text-danger">*</span></label>
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
                                <select name="car_brand" class="form-control ">
                                    <option value="0" selected disabled>--กรุณาเลือกยี่ห้อรถ--</option>
                                    @foreach ($car_brand as $brand)
                                        <option value="{{ $brand->brand_name }}">{{ $brand->brand_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="il-gray fs-16 fw-500 align-center mb-10">รุ่นรถ <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="car_model"
                                    class="form-control ih-medium ip-light radius-xs b-light px-15">
                            </div>


                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="a8"
                                            class="il-gray fs-16 fw-500 align-center mb-10">หมายเลขรถ</label>
                                        <input type="text" name="car_number_record"
                                            class="form-control ih-medium ip-light radius-xs b-light px-15" id="a8">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="a9"
                                            class="il-gray fs-16 fw-500 align-center mb-10">อายุการใช้งานรถ
                                            (ระบุเป็นจำนวนปี)</label>
                                        <input type="text" name="car_age"
                                            class="form-control ih-medium ip-light radius-xs b-light px-15" id="a9">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="a8"
                                            class="il-gray fs-16 fw-500 align-center mb-10">เลขไมล์</label>
                                        <input type="text" name="car_mileage"
                                            class="form-control ih-medium ip-light radius-xs b-light px-15" id="a8"
                                            placeholder="เลขไมล์ ณ วันที่บันทึกข้อมูล">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="a9"
                                            class="il-gray fs-16 fw-500 align-center mb-10">ป้ายภาษีประจำปี</label>
                                        <input type="text" name="car_tax"
                                            class="form-control ih-medium ip-light radius-xs b-light px-15" id="a9">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="il-gray fs-16 fw-500 align-center mb-10">ประกันภัย </label>
                                <input type="text" name="car_insure"
                                    class="form-control ih-medium ip-light radius-xs b-light px-15">
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
@push('scripts')
    <script>
        $(document).ready(function() {
            $('#company_id').change(function() {
                var companyID = $(this).val();

                $('#supply_id').empty().append('<option value="">กำลังโหลด...</option>');

                if (companyID) {
                    $.get("{{ route('get.supply') }}", {
                        company_id: companyID
                    }, function(data) {
                        $('#supply_id').empty().append(
                            '<option value="">-- เลือกบริษัท Supply --</option>');
                        $.each(data, function(id, name) {
                            $('#supply_id').append('<option value="' + id + '">' + name +
                                '</option>');
                        });
                    });
                } else {
                    $('#supply_id').empty().append('<option value="">-- เลือกบริษัท Supply --</option>');
                }
            });
        });
    </script>
@endpush
