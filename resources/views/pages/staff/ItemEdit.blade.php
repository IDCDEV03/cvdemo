@section('title', 'ระบบ E-Checker')
@section('description', 'ID Drives')
@extends('layout.app')
@section('content')
    <div class="container-fluid">
        <div class="social-dash-wrap">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb-main">
                        <h4 class="text-capitalize breadcrumb-title">แก้ข้อตรวจ</h4>

                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class=" alert alert-success " role="alert">
                        <div class="alert-content">

                            <span class="fs-20 fw-bold">หมวดหมู่ : {{ $item_data->chk_cats_name }} </span>
                        </div>
                    </div>

                    <div class="card mb-25">
                        <div class="card-body">

                            @if (session('error'))
                                <div class="alert alert-danger">{{ session('error') }}</div>
                            @endif

                            <form action="{{route('staff.item_update')}}" method="POST" enctype="multipart/form-data">
                                @csrf

                                <input type="hidden" name="item_id" value="{{ request()->id }}">

                                <div id="item-wrapper">
                                    <div class="item-group border-warning border p-3 mb-2 rounded">
                                        <span class="fs-20 fw-bold text-dark item-number">ข้อตรวจที่
                                            {{ $item_data->item_no }} </span>

                                        <div class="mb-2 mt-2">
                                            <label>หัวข้อตรวจ <span class="text-danger">*</span></label>
                                            <input type="text" name="item_name" class="form-control"
                                                value="{{ $item_data->item_name }}">
                                        </div>

                                        <div class="mb-2">
                                            <label>รายละเอียด (ถ้ามี)</label>
                                            <textarea name="item_description" class="form-control">{{ $item_data->item_description }}
                                            </textarea>
                                        </div>

                                        <div class="mb-2">
                                            <label>รูปภาพปัจจุบัน</label><br>
                                            @if ($item_data->item_image)
                                                <img src="{{ asset($item_data->item_image) }}" style="max-width: 300px;"
                                                    class="mt-2 img-thumbnail">
                                                <a href="{{ route('agency.item_delete_image', ['id' => request()->id]) }}"
                                                    class="mb-2 btn btn-xs btn-danger btn-default btn-squared btn-transparent-danger"
                                                    onclick="return confirm('ต้องการลบใช่หรือไม่ หากลบแล้วไม่สามารถกู้คืนได้อีก?');">
                                                    <i class="fas fa-trash-alt"></i>ลบภาพ</a>
                                            @else
                                                <p class="text-danger fs-14 mt-2">ยังไม่มีรูปภาพ</p>
                                            @endif

                                        </div>

                                        <div class="border-top my-3"></div>
                                        <div class="mb-2">
                                            <label>อัพโหลดภาพใหม่</label>
                                            <input type="file" name="item_image" accept="image/*"
                                                class="form-control image-input" id="new-image-input">
                                            <img id="new-image-preview" class="mt-2"
                                                style="max-width: 200px; display: none;">
                                            <button type="button" class="btn btn-xs btn-danger btn-squared mt-2"
                                                id="clear-new-image-btn" style="display: none;">
                                                ยกเลิก
                                            </button>
                                        </div>

                                        <div class="mb-2 mt-2">
                                            <label>ประเภทการตรวจ</label>
                                            <select name="item_type" class="form-select" required>
                                                <option value="1" {{ $item_data->item_type == 1 ? 'selected' : '' }}>
                                                    แบบตัวเลือก (ผ่าน/ไม่ผ่าน)</option>
                                                <option value="2" {{ $item_data->item_type == 2 ? 'selected' : '' }}>
                                                    แบบตัวเลือก (ปกติ/ปรับปรุง)</option>
                                                <option value="3" {{ $item_data->item_type == 3 ? 'selected' : '' }}>
                                                    แบบกรอกข้อความ</option>
                                                <option value="4" {{ $item_data->item_type == 4 ? 'selected' : '' }}>
                                                    แบบเลือกวันที่</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>


                                <div class="border-top my-3"></div>

                                <div class="dm-button-list d-flex flex-wrap">
                                    <button type="submit"
                                        class="btn btn-sm btn-default btn-primary ">บันทึกการแก้ไข</button>

                                    <a href="{{ url()->previous() }}"
                                        class="btn btn-sm btn-default btn-warning">ย้อนกลับ</a>

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
        const input = document.getElementById('new-image-input');
        const preview = document.getElementById('new-image-preview');
        const clearBtn = document.getElementById('clear-new-image-btn');

        input?.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    preview.src = event.target.result;
                    preview.style.display = 'block';
                    clearBtn.style.display = 'inline-block';
                };
                reader.readAsDataURL(file);
            } else {
                preview.src = '';
                preview.style.display = 'none';
                clearBtn.style.display = 'none';
            }
        });

        clearBtn?.addEventListener('click', function() {
            input.value = '';
            preview.src = '';
            preview.style.display = 'none';
            clearBtn.style.display = 'none';
        });
    </script>
@endpush
