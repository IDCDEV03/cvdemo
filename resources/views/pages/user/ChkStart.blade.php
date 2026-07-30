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
                            <span class="fs-20 fw-bold">เลือกแบบฟอร์ม</span>
                        </div>
                    </div>

                    <div class="card mb-25">
                        <div class="card-header d-flex justify-content-end align-items-center">

                            <small class="text-muted" id="live-clock">

                        </div>
                        <div class="card-body">

                            @if (session('error'))
                                <div class="alert alert-danger fs-20 fw-bold">{{ session('error') }}</div>
                            @endif

                            <form action="{{ route('user.insert_new1', request()->id) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf

                                <div class="form-group row">
                                    <div class="col-sm-2 d-flex aling-items-center">
                                        <label for="inputName"
                                            class="col-form-label color-dark fs-18 fw-bold align-center">ทะเบียนรถ</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control radius-xs b-light fs-20"
                                            value="{{ $veh_detail->car_plate }}" readonly>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-sm-2 d-flex aling-items-center">
                                        <label for="inputName"
                                            class="col-form-label color-dark fs-18 fw-bold align-center">เลือกแบบฟอร์ม<span
                                                class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-sm-9">
                                        <select name="form_id" id="select-alerts2" class="form-control " required>
                                            <option value="JOM3GBDG" selected>-เอกสารตรวจสอบความปลอดภัยเพื่อขึ้นทะเบียนรถโม่</option>
                                            @foreach ($forms as $form)
                                                <option value="{{ $form->form_id }}" class="fs-18">{{ $form->form_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="border-top my-3"></div>
                                <div class=" alert alert-secondary " role="alert">
                                    <div class="alert-content">
                                        <span class="fs-20 fw-bold">รูปถ่ายประเมินรอบคัน (8 ภาพ)</span>
                                    </div>
                                </div>
                                 <a href="#" data-bs-toggle="modal" data-bs-target="#exampleImageModal" class="ms-2 fs-14 btn btn-sm btn-secondary mt-2 mb-2">
                ดูตัวอย่างภาพ
            </a>
                                @php
                                    $imageFields = [
                                        'image1' => '1. ด้านหน้ารถ',
                                        'image2' => '2. หลังรถเยื้องไปทางซ้าย',
                                        'image3' => '3. หลังรถเยื้องไปทางขวา',
                                        'image4' => '4. ด้านหลังรถเฟืองท้าย',
                                        'image5' => '5. ในห้องโดยสารฝั่งคนขับ',
                                        'image6' => '6. เฟืองเกียร์หมุนโม่',
                                        'image7' => '7. ลูกหมากคันชักคันส่ง',
                                        'image8' => '8. เพลาส่งกำลัง',
                                    ];
                                @endphp


                                @foreach ($imageFields as $field => $label)
                                    <div class="form-group row mb-3">
                                        <div class="col-sm-3 d-flex align-items-center">
                                            <label for="{{ $field }}"
                                                class="col-form-label color-dark fs-18 fw-bold">{{ $label }}</label>
                                        </div>
                                        <div class="col-sm-9">
                                            <input class="form-control" type="file" name="{{ $field }}"
                                                id="{{ $field }}" accept="image/*"
                                                onchange="previewImage(this, 'preview_{{ $field }}')" required>
                                            <img id="preview_{{ $field }}" src="#" alt="Preview"
                                                class="img-thumbnail mt-2" style="display: none; max-width: 200px;">
                                        </div>
                                    </div>
                                @endforeach


                                <div class="border-top my-3"></div>

                                <button type="submit" class="btn btn-block btn-primary fs-20">เริ่มการตรวจ &nbsp;<i
                                        class="fas fa-arrow-right"></i> </button>

                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="exampleImageModal" tabindex="-1" aria-labelledby="exampleImageLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleImageLabel">ตัวอย่างภาพประเมินรอบคัน</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center">
        <img src="{{ asset('example_8.png') }}" alt="ตัวอย่างภาพหน้ารถ" class="img-fluid rounded">
      </div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
    <script>
        function previewImage(input, previewId) {
            const file = input.files[0];
            const preview = document.getElementById(previewId);

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                preview.src = '#';
                preview.style.display = 'none';
            }
        }




        function updateClock() {
            const now = new Date();

            const day = String(now.getDate()).padStart(2, '0');
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const year = now.getFullYear() + 543;

            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');

            const fullDateTime = `${day}/${month}/${year} ${hours}:${minutes}:${seconds}`;

            document.getElementById('live-clock').textContent = fullDateTime;
        }

        updateClock();
        setInterval(updateClock, 1000);
    </script>
@endpush
