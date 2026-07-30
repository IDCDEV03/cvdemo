@section('title', 'ระบบ E-Checker')
@section('description', 'ID Drives')
@extends('layout.app')
@section('content')
    <div class="container-fluid">
        <div class="social-dash-wrap">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb-main">
                        <h4 class="text-capitalize breadcrumb-title">เพิ่มข้อตรวจ</h4>

                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class=" alert alert-success " role="alert">
                        <div class="alert-content">
                            <span class="fs-20 fw-bold">แบบฟอร์ม : {{ $cates_data->form_name }}</span>
                            <br>
                            <span class="fs-20 fw-bold">หมวดหมู่ : {{ $cates_data->chk_cats_name }} </span>
                        </div>
                    </div>

                    <div class="card mb-25">
                        <div class="card-body">
                            @if (session('error'))
                                <div class="alert alert-big alert-danger">
                                    <div class="alert-content">
                                        {{ session('error') }}
                                    </div>
                                </div>
                            @endif

                            <form action="{{route('agency.item_insert')}}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="cate_id" value="{{ request()->id }}">

                                <div id="item-wrapper">
                                    <div class="item-group border-warning border p-3 mb-2 rounded">
                                        <span class="fs-20 fw-bold text-dark item-number">ข้อตรวจที่ 1</span>
                                        <div class="mb-2 mt-2">
                                            <label>หัวข้อตรวจ <span class="text-danger">*</span></label>
                                            <input type="text" name="item_name[]" class="form-control" required>
                                        </div>
                                        <div class="mb-2">
                                            <label>รายละเอียด (ถ้ามี)</label>
                                            <textarea name="item_description[]" class="form-control"></textarea>
                                        </div>

                                        <div class="mb-2">
                                            <label>รูปภาพ (ถ้ามี)</label>
                                            <input type="file" name="item_image[]" accept="image/*"
                                                class="form-control image-input">
                                            <img class="image-preview img-thumbnail mt-2" style="max-width: 200px; display: none;" />
                                            <button type="button" class="btn btn-xs btn-danger btn-squared remove-image"
                                                style="display: none;">ลบรูป</button>
                                        </div>

                                        <div class="mb-2">
                                            <label>ประเภทการตรวจ</label>
                                            <select name="item_type[]" class="form-select" required>
                                                <option value="1">แบบตัวเลือก (ผ่าน/ไม่ผ่าน)</option>
                                                <option value="2" selected>แบบตัวเลือก (ปกติ/ปรับปรุง)</option>
                                                <option value="3">แบบกรอกข้อความ</option>
                                                <option value="4">แบบเลือกวันที่</option>
                                            </select>
                                        </div>
                                        <button type="button"
                                            class="btn btn-xs btn-default btn-squared color-danger btn-outline-danger remove-item">ลบข้อตรวจ</button>
                                    </div>
                                </div>

                                <button type="button" id="add-item"
                                    class="btn btn-dark btn-default btn-squared btn-transparent-dark btn-block"><i
                                        class="fas fa-plus"></i> เพิ่มข้อตรวจ</button>

                                <div class="border-top my-3"></div>

                                <button type="submit" class="btn btn-default btn-success mt-4">บันทึกข้อมูล</button>

                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.querySelectorAll('.image-input').forEach(input => {
            const container = input.closest('.mb-2');
            const preview = container.querySelector('.image-preview');
            const removeBtn = container.querySelector('.remove-image');

            input.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        preview.src = event.target.result;
                        preview.style.display = 'block';
                        removeBtn.style.display = 'inline-block';
                    }
                    reader.readAsDataURL(file);
                }
            });

            removeBtn.addEventListener('click', function() {
                input.value = '';
                preview.src = '';
                preview.style.display = 'none';
                removeBtn.style.display = 'none';
            });
        });

        function updateItemNumbers() {
            document.querySelectorAll('.item-group').forEach((el, i) => {
                el.querySelector('.item-number').textContent = `ข้อตรวจที่ ${i + 1}`;
            });
        }

        document.getElementById('add-item').addEventListener('click', function() {
            const wrapper = document.getElementById('item-wrapper');
            const group = document.createElement('div');
            group.className = 'item-group border-warning border p-3 mb-2 rounded';

            group.innerHTML = `
             <span class="fs-20 fw-bold text-dark item-number">ข้อตรวจที่ ?</span>
            <div class="mb-2 mt-2">
              <label>หัวข้อตรวจ <span class="text-danger">*</span></label>
                <input type="text" name="item_name[]" class="form-control" required>
            </div>
            <div class="mb-2">
                <label>รายละเอียด (ถ้ามี)</label>
                <textarea name="item_description[]" class="form-control"></textarea>
            </div>
             <div class="mb-2">
                <label>รูปภาพ (ถ้ามี)</label>
                <input type="file" name="item_image[]" accept="image/*" class="form-control image-input">
                <img class="image-preview img-thumbnail mt-2" style="max-width: 200px; display: none;" />
                <button type="button" class="btn btn-xs btn-danger btn-squared remove-image"
                                                style="display: none;">ลบรูป</button>
            </div>
            <div class="mb-2">
                <label>ประเภทการตรวจ</label>
                <select name="item_type[]" class="form-select" required>
                     <option value="1">แบบตัวเลือก (ผ่าน/ไม่ผ่าน)</option>
                        <option value="2" selected>แบบตัวเลือก (ปกติ/ปรับปรุง)</option>
                        <option value="3">แบบกรอกข้อความ</option>
                        <option value="4">แบบเลือกวันที่</option>
                </select>
            </div>
                <button type="button" class="btn btn-xs btn-default btn-squared color-danger btn-outline-danger remove-item">ลบข้อตรวจ</button>
        `;

            wrapper.appendChild(group);
            updateItemNumbers();


            const input = group.querySelector('.image-input');
            const preview = group.querySelector('.image-preview');
            const removeBtn = group.querySelector('.remove-image');

            input.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        preview.src = event.target.result;
                        preview.style.display = 'block';
                        removeBtn.style.display = 'inline-block';
                    }
                    reader.readAsDataURL(file);
                }
            });

            removeBtn.addEventListener('click', function() {
                input.value = '';
                preview.src = '';
                preview.style.display = 'none';
                removeBtn.style.display = 'none';
            });


        });

        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-item')) {
                e.target.closest('.item-group').remove();
                updateItemNumbers();
            }
        });

        updateItemNumbers();
    </script>
@endpush
