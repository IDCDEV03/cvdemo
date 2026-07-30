@section('title', 'ระบบปฏิบัติการพนักงานขับรถราชการ')
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

                            <form action="{{route('user.veh_create')}}" method="POST" enctype="multipart/form-data">
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

                                <div class="mb-3">
                                    <label>ภาพถ่ายรถ <span class="text-danger">*</span></label>
                                    <input type="file" name="vehicle_image" id="vehicle_image" class="form-control" accept="image/*"
                                        required>
                                    <img id="vehicle-preview" class="mt-2" style="max-width: 200px; display: none;">
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
