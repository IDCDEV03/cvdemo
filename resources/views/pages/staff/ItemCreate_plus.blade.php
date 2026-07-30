@section('title', 'ระบบตรวจมาตรฐานรถ : เพิ่มข้อตรวจ')
@section('description', 'ID Drives')
@extends('layout.app')
@section('content')
    <div class="container-fluid">
        <div class="social-dash-wrap">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb-main">
                        <label class="fs-22 fw-bold text-capitalize breadcrumb-title">เพิ่มข้อตรวจ</label>

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

                    <div class="card mb-3">
                        <div class="card-header bg-info">
                            <label class="fs-18 fw-bold text-dark mb-0">รายการข้อตรวจที่มีอยู่แล้ว</label>
                        </div>
                        <div class="card-body p-0">
                            @if ($item_data->count() > 0)
                                <ol class="list-group list-group-numbered list-group-flush">
                                    @foreach ($item_data as $item)
                                        <li class="list-group-item d-flex ">
                                            <span>{{ $item->item_name }}</span>
                                        </li>
                                    @endforeach
                                </ol>
                            @else
                                <div class="p-3 text-muted">ยังไม่มีข้อตรวจในหมวดหมู่นี้</div>
                            @endif
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




                            <form action="{{route('staff.item_insert')}}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="cate_id" value="{{ request()->id }}">

                                <div id="item-wrapper">
                                    <div class="item-group border-warning border p-3 mb-2 rounded">
                                        <span class="fs-20 fw-bold text-dark item-number">ข้อตรวจที่
                                            {{ $lastOrder + 1 }}</span>
                                        <input type="hidden" name="item_order[]" value="{{ $lastOrder + 1 }}">
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
                                            <img class="image-preview img-thumbnail mt-2"
                                                style="max-width: 200px; display: none;" />
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

                                <div class="dm-button-list d-flex flex-wrap">
                                    <button type="submit" class="btn btn-default btn-success" disabled>บันทึกข้อมูล</button>
                                    <button type="button" class="btn btn-default btn-warning"
                                        onclick="sessionStorage.clear(); localStorage.clear(); window.history.back();">
                                       ย้อนกลับ
                                    </button>

                                </div>





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
        let lastOrder = {{ $lastOrder }}; // ค่า max order ล่าสุดจาก DB
        const wrapper = document.getElementById('item-wrapper');

        // ฟังก์ชันอัพเดตลำดับ
        function updateItemNumbers() {
            wrapper.querySelectorAll('.item-group').forEach((el, i) => {
                let order = (lastOrder + 1) + i;
                el.querySelector('.item-number').innerText = 'ข้อตรวจที่ ' + order;

                // ถ้าไม่มี hidden order ให้สร้างใหม่
                let hidden = el.querySelector('input[name="item_order[]"]');
                if (!hidden) {
                    hidden = document.createElement('input');
                    hidden.type = 'hidden';
                    hidden.name = 'item_order[]';
                    el.appendChild(hidden);
                }
                hidden.value = order;
            });
        }

        // ปุ่มเพิ่มข้อตรวจ
        document.getElementById('add-item').addEventListener('click', function() {
            let group = document.createElement('div');
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
              <button type="button" class="btn btn-xs btn-danger btn-squared remove-image" style="display: none;">ลบรูป</button>
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

            // จัดการ preview รูป
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

        // ปุ่มลบข้อตรวจ
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-item')) {
                e.target.closest('.item-group').remove();
                updateItemNumbers();
            }
        });

        // เริ่มต้นครั้งแรก
        updateItemNumbers();
    </script>
@endpush
