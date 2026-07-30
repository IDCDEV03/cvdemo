@section('title', 'ระบบตรวจมาตรฐานรถ')
@section('description', 'ID Drives')
@extends('layout.app')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb-main">
                    <span class="fs-24 fw-bold breadcrumb-title">ลงทะเบียนรถ: <span
                            class="text-primary">{{ $supply->supply_name }}</span>

                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-sm mb-25">
                    <div class="card-body">
                        @if (session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        <form action="{{ route('company.vehicles.store', $supply->sup_id) }}" method="POST"
                            enctype="multipart/form-data" id="vehicleForm">
                            @csrf

                            <div class="mb-3">
                                <label>ทะเบียนรถ <span class="text-danger">*</span></label>
                                <input type="text" name="plate" class="form-control" maxlength="10" required>
                            </div>

                            <div class="mb-3">
                                <label>จังหวัดทะเบียนรถ <span class="text-danger">*</span></label>
                                <select name="province" id="select-alerts2" class="form-control ">
                                    <option value="0" selected disabled>--กรุณาเลือกจังหวัด--</option>
                                    @foreach ($province as $item)
                                        <option value="{{ $item->name_th }}">{{ $item->name_th }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label>ยี่ห้อรถ <span class="text-danger">*</span></label>
                                <select name="veh_brand" id="select-option2" class="form-control ">
                                    <option value="0" selected disabled>--กรุณาเลือกยี่ห้อรถ--</option>
                                    @foreach ($car_brand as $brand)
                                        <option value="{{ $brand->brand_name }}">{{ $brand->brand_name }}</option>
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

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="fs-16 fw-500 align-center mb-10">รุ่นรถ <span
                                            class="text-danger">*</span> (ถ้าไม่มีให้ใส่ - )</label>
                                    <input type="text" name="car_model" class="form-control"
                                        placeholder="เช่น Victor500, FXZ320">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="a8"
                                            class="fs-16 fw-500 align-center mb-10">หมายเลขรถ</label>
                                        <input type="text" name="car_number_record" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="a9"
                                            class="fs-16 fw-500 align-center mb-10">ปีที่เริ่มใช้งานครั้งแรกหรือจดทะเบียนครั้งแรก
                                            </label>
                                        <input type="text" name="car_age" placeholder="ระบุ ปี พ.ศ. ถ้าไม่มีให้ใส่ -" class="form-control ">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="a8"
                                            class="fs-16 fw-500 align-center mb-10">เลขไมล์</label>
                                        <input type="text" name="car_mileage" class="form-control"
                                            placeholder="เลขไมล์ ณ วันที่บันทึกข้อมูล">
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">ปีหมดอายุภาษี/ประกัน</label>
                                    <input type="text" class="form-control" name="car_tax" value="{{ old('car_tax') }}"
                                        placeholder="ระบุข้อมูลภาษีหรือประกัน">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="car_trailer_plate"
                                            class="fs-16 fw-500 align-center mb-10">ทะเบียนหาง</label>
                                        <input type="text" name="car_trailer_plate" id="car_trailer_plate"
                                            value="{{ old('car_trailer_plate') }}" class="form-control"
                                            placeholder="ระบุทะเบียนหาง (ถ้ามี)">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="car_register_date"
                                            class="fs-16 fw-500 align-center mb-10">วันที่จดทะเบียน</label>
                                        <input type="date" name="car_register_date" id="car_register_date"
                                            value="{{ old('car_register_date') }}" class="form-control">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="car_insurance_expire"
                                            class="fs-16 fw-500 align-center mb-10">วันที่ประกันหมดอายุ</label>
                                        <input type="date" name="car_insurance_expire" id="car_insurance_expire"
                                            value="{{ old('car_insurance_expire') }}" class="form-control">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="car_total_weight"
                                            class="fs-16 fw-500 align-center mb-10">น้ำหนักรถรวม</label>
                                        <input type="text" name="car_total_weight" id="car_total_weight"
                                            value="{{ old('car_total_weight') }}" class="form-control"
                                            >
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="car_fuel_type"
                                            class="fs-16 fw-500 align-center mb-10">ชนิดเชื้อเพลิง</label>
                                        <input type="text" name="car_fuel_type" id="car_fuel_type"
                                            value="{{ old('car_fuel_type') }}" class="form-control"
                                            placeholder="เช่น ดีเซล, NGV, B20">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="fs-16 fw-500 align-center mb-10">บริษัทประกันภัย </label>
                                        <input type="text" name="car_insure" class="form-control">
                                    </div>
                                </div>
                            </div>




                            <div class="col-12 mb-4 mt-3">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="status" value="0">
                                    <input class="form-check-input" type="checkbox" id="status" name="status"
                                        value="1" checked>
                                    <label class="form-check-label" for="status">เปิดใช้งาน (Active)</label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label>ภาพถ่ายรถหน้ารถ </label>
                                <input type="file" name="vehicle_image" id="vehicle_image" class="form-control"
                                    accept="image/*">
                                <img id="vehicle-preview" class="mt-2" style="max-width: 200px; display: none;">
                            </div>

                            <div class="border-top my-3"></div>

                            <button type="button" id="btnPreview"
                                class="fs-18 btn btn-block btn-success">บันทึกข้อมูล</button>
                        </form>


                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white" id="previewModalLabel"><i class="uil uil-check-circle"></i>
                        ยืนยันข้อมูลการลงทะเบียนรถ</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert-big alert alert-danger mb-4">
                        <div class="alert-content">
                            <h6 class='alert-heading'> <i class="uil uil-exclamation-triangle"></i> กรุณาตรวจสอบ ทะเบียนรถ
                                ให้ถูกต้อง</h6>
                            <p> หลังจากการบันทึกแล้ว จะไม่สามารถแก้ไขทะเบียนรถได้ด้วยตนเอง </p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <span class="text-muted">ทะเบียนรถ:</span>
                            <h5 id="prev_plate" class="text-primary fw-bold">-</h5>
                        </div>
                        <div class="col-md-6 mb-2">
                            <span class="text-muted">ยี่ห้อ / ประเภทรถ:</span>
                            <h6 id="prev_brand_type">-</h6>
                        </div>
                        <div class="col-md-6 mb-2">
                            <span class="text-muted">รุ่นรถ:</span>
                            <span id="prev_model" class="d-block fw-500">-</span>
                        </div>
                        <div class="col-md-6 mb-2">
                            <span class="text-muted">หมายเลขประจำรถ:</span>
                            <span id="prev_number" class="d-block fw-500">-</span>
                        </div>
                        <div class="col-12 mt-3 text-center">
                            <span class="text-muted d-block mb-2">ภาพถ่ายรถ:</span>
                            <img id="prev_image" src="" alt="ยังไม่ได้อัปโหลดรูปภาพ" class="img-thumbnail"
                                style="max-height: 200px; display: none;">
                            <span id="prev_no_image" class="text-danger fs-14">ไม่มีรูปภาพ</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning" data-bs-dismiss="modal">กลับไปแก้ไข</button>
                    <button type="button" id="btnConfirmSubmit" class="btn btn-success">ยืนยันและบันทึกข้อมูล</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {

            $('#btnPreview').on('click', function() {
                let form = $('#vehicleForm')[0];

                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }

                let provinceVal = $('select[name="province"]').val();
                let brandVal = $('select[name="veh_brand"]').val();
                let typeVal = $('select[name="vehicle_type"]').val();

                if (!provinceVal || provinceVal == '0' || !brandVal || brandVal == '0' || !typeVal ||
                    typeVal == '0') {
                    alert('กรุณาเลือก จังหวัด, ยี่ห้อรถ และ ประเภทรถ ให้ครบถ้วน');
                    return;
                }

                let plateStr = $('input[name="plate"]').val() + ' ' + $(
                    'select[name="province"] option:selected').text();
                let brandTypeStr = $('select[name="veh_brand"] option:selected').text() + ' / ' + $(
                    'select[name="vehicle_type"] option:selected').text();
                let modelStr = $('input[name="car_model"]').val() || '-';
                let numberStr = $('input[name="car_number_record"]').val() || '-';


                $('#prev_plate').text(plateStr);
                $('#prev_brand_type').text(brandTypeStr);
                $('#prev_model').text(modelStr);
                $('#prev_number').text(numberStr);


                let fileInput = $('input[name="vehicle_image"]')[0];
                if (fileInput.files && fileInput.files[0]) {
                    let reader = new FileReader();
                    reader.onload = function(e) {
                        $('#prev_image').attr('src', e.target.result).show();
                        $('#prev_no_image').hide();
                    }
                    reader.readAsDataURL(fileInput.files[0]);
                } else {
                    $('#prev_image').hide();
                    $('#prev_no_image').show();
                }


                $('#previewModal').modal('show');
            });

            $('#btnConfirmSubmit').on('click', function() {
                $(this).prop('disabled', true).html(
                    '<i class="uil uil-spinner fa-spin"></i> กำลังบันทึก...');
                $('#vehicleForm').submit();
            });

        });
    </script>
    <script>
        const input = document.getElementById('vehicle_image');
        const preview = document.getElementById('vehicle-preview');

        input.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    preview.src = event.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            } else {
                preview.src = '';
                preview.style.display = 'none';
            }
        });
    </script>
@endpush
