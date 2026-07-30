@section('title', 'ระบบตรวจมาตรฐานรถ')
@section('description', 'ID Drives')
@extends('layout.app')
@section('content')
    <div class="container-fluid">
        <div class="row pt-4 pb-3">
            <div class="col-12">
                <h4>แก้ไขข้อมูลรถ: <span class="text-primary">{{ $car->car_plate }}</span></h4>
            </div>
        </div>

       <div class="row">
    <div class="col-12">
        <div class="card shadow-sm mb-25">
            <div class="card-body">
                <form action="{{ route('company.vehicles.update', $car->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>ทะเบียนรถ</label>
                            <input type="text" name="plate" class="form-control"
                                value="{{ old('plate', $plateNumber) }}" maxlength="10" readonly>
                            <small class="text-danger mt-1 d-block fw-500">
                                <i class="uil uil-info-circle"></i> 
                                หากต้องการแก้ไขทะเบียนรถกรุณาติดต่อเจ้าหน้าที่ดูแลระบบ
                            </small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>จังหวัดทะเบียนรถ</label>
                            <input type="text" class="form-control" value="{{ $provinceName }}" readonly>
                            <input type="hidden" name="province" value="{{ $provinceName }}">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>ยี่ห้อรถ <span class="text-danger">*</span></label>
                            <select name="veh_brand" class="form-control">
                                <option value="0" disabled>--กรุณาเลือกยี่ห้อรถ--</option>
                                @foreach ($car_brand as $brand)
                                    <option value="{{ $brand->brand_name }}"
                                        {{ $car->car_brand == $brand->brand_name ? 'selected' : '' }}>
                                        {{ $brand->brand_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>ประเภทรถ <span class="text-danger">*</span></label>
                            <select class="form-control" name="vehicle_type" required>
                                <option value="0" disabled>--เลือกประเภทรถ--</option>
                                @foreach ($car_type as $data)
                                    <option value="{{ $data->id }}"
                                        {{ $car->car_type == $data->id ? 'selected' : '' }}>
                                        {{ $data->vehicle_type }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>รุ่นรถ</label>
                            <input type="text" name="car_model" class="form-control"
                                value="{{ old('car_model', $car->car_model) }}">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>หมายเลขประจำรถ</label>
                            <input type="text" name="car_number_record" class="form-control"
                                value="{{ old('car_number_record', $car->car_number_record) }}">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>ทะเบียนหาง</label>
                            <input type="text" name="car_trailer_plate" class="form-control"
                                value="{{ old('car_trailer_plate', $car->car_trailer_plate) }}" placeholder="ระบุทะเบียนหาง (ถ้ามี)">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>วันที่จดทะเบียน</label>
                            <input type="date" name="car_register_date" class="form-control"
                                value="{{ old('car_register_date', $car->car_register_date) }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="fs-14">ปีที่เริ่มใช้งานครั้งแรกหรือจดทะเบียนครั้งแรก</label>
                            <input type="text" name="car_age" class="form-control"
                                value="{{ old('car_age', $car->car_age) }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>เลขไมล์สะสม</label>
                            <input type="text" name="car_mileage" class="form-control"
                                value="{{ old('car_mileage', $car->car_mileage) }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>ปีหมดอายุภาษี</label>
                            <input type="text" name="car_tax" class="form-control"
                                value="{{ old('car_tax', $car->car_tax) }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>บริษัทประกันภัย</label>
                            <input type="text" name="car_insure" class="form-control"
                                value="{{ old('car_insure', $car->car_insure) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>วันที่ประกันหมดอายุ</label>
                            <input type="date" name="car_insurance_expire" class="form-control"
                                value="{{ old('car_insurance_expire', $car->car_insurance_expire) }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>น้ำหนักรถรวม</label>
                            <input type="text" name="car_total_weight" class="form-control"
                                value="{{ old('car_total_weight', $car->car_total_weight) }}" placeholder="เช่น 15 ตัน หรือ 15,000 กก.">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>ชนิดเชื้อเพลิง</label>
                            <input type="text" name="car_fuel_type" class="form-control"
                                value="{{ old('car_fuel_type', $car->car_fuel_type) }}" placeholder="เช่น ดีเซล, NGV, B20">
                        </div>
                    </div>

                    <div class="col-12 mb-4 mt-3">
                        <label class="d-block mb-2">สถานะสำหรับตรวจเช็ครถ <span class="text-danger">*</span></label>
                        <div class="d-flex flex-wrap gap-4">
                            <div class="form-check form-radio">
                                <input class="form-check-input" type="radio" name="status" id="status_active"
                                    value="1" {{ $car->status == '1' ? 'checked' : '' }} required>
                                <label class="form-check-label text-success" for="status_active">
                                    เปิดใช้งาน
                                </label>
                            </div>

                            <div class="form-check form-radio">
                                <input class="form-check-input" type="radio" name="status" id="status_inactive"
                                    value="0" {{ $car->status == '0' ? 'checked' : '' }} required>
                                <label class="form-check-label text-warning" for="status_inactive">
                                    ปิดการใช้
                                </label>
                            </div>

                            <div class="form-check form-radio">
                                <input class="form-check-input" type="radio" name="status"
                                    id="status_unavailable" value="2"
                                    {{ $car->status == '2' ? 'checked' : '' }} required>
                                <label class="form-check-label text-danger" for="status_unavailable">
                                    ไม่พร้อมใช้งาน (เสีย/ชำรุด)
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label>ภาพถ่ายหน้ารถ (อัพโหลดใหม่หากต้องการเปลี่ยนภาพ)</label>
                        <input type="file" name="vehicle_image" id="vehicle_image" class="form-control"
                            accept="image/*">
                        <img id="vehicle-preview" class="mt-2" style="max-width: 200px; display: none;">
                        
                        @if ($car->car_image)
                            <div class="border-top my-3"></div>
                            <div class="mt-2 text-center">
                                <p class="small text-muted mb-1">รูปภาพปัจจุบัน:</p>
                                <img src="{{ asset($car->car_image) }}" class="rounded shadow-sm"
                                    style="max-width: 250px;">
                            </div>
                        @endif
                    </div>

                    <div class="d-flex gap-2">
                        <a href="{{ route('company.supplies.show', $car->supply_id) }}"
                            class="btn btn-light btn-lg fs-18 flex-grow-1">ยกเลิก</a>
                        <button type="submit"
                            class="btn btn-lg btn-warning flex-grow-1 fs-18">บันทึกการแก้ไข</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
    </div>
@endsection

@push('scripts')
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
