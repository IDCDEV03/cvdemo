@section('title', 'ระบบ E-Checker')
@section('description', 'ID Drives')
@extends('layout.app')
@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <div class="container-fluid">
        <div class="social-dash-wrap">

            <div class="row mt-4">
                <div class="col-md-12">
                    <div class=" alert alert-primary " role="alert">
                        <div class="alert-content">
                            <span class="fs-20 fw-bold"> {{ $forms->form_name }} </span>
                        </div>
                    </div>

                    <div class="card card-default card-sm mb-4">
                        <div class="card-header">
                            <label class="fw-bold fs-18">รายการหมวดหมู่</label>
                        </div>
                        <div class="card-body">

                            <div class="d-flex flex-wrap gap-2 mb-3">
                                @foreach ($allCategories as $cat)
                                    @php
                                        $isActive = $category->category_id == $cat->category_id;
                                        $isChecked = in_array($cat->category_id, $checkedCategories);

                                        if ($isActive) {
                                            $btnClass = 'btn-primary'; // หมวดปัจจุบัน
                                        } elseif ($isChecked) {
                                            $btnClass = 'btn-success'; // หมวดที่ตรวจแล้ว
                                        } else {
                                            $btnClass = 'btn-outline-primary'; // ยังไม่ได้ตรวจ
                                        }
                                    @endphp

                                    <a href="{{ route('user.chk_step2', ['rec' => $record->record_id, 'cats' => $cat->category_id]) }}"
                                        class="btn btn-xs {{ $btnClass }}">
                                        @if ($isChecked)
                                            <i class="fas fa-check"></i>
                                        @endif
                                        {{ $cat->cates_no }}. {{ $cat->chk_cats_name }}
                                    </a>
                                @endforeach
                            </div>


                        </div>
                    </div>

                    <div class="card shadow-sm mb-25">
                        <div class="card-body">


                            @if (session('error'))
                                <div class="alert alert-danger">{{ session('error') }}</div>
                            @endif
                            <p class="fs-20 fw-bold">หมวดหมู่ที่ {{ $category->cates_no }} : {{ $category->chk_cats_name }}
                            </p>
                            <form method="POST"
                                action="{{ route('user.storeOrUpdate', [$record->record_id, $category->category_id]) }}"
                                enctype="multipart/form-data">
                                @csrf

                                @foreach ($items as $item)
                                    @php
                                        $key = $item->id; // <<-- ใช้ id จาก check_items
                                        $val = old("item_result.$key", $item->result_value);
                                        $comm = old("user_comment.$key", $item->user_comment);
                                    @endphp
                                    <div class="mb-3 border-success rounded p-3">
                                        <label class="fw-bold fs-18">{{ $item->item_no }}. {{ $item->item_name }}</label>
                                        @if (!empty($item->item_description))
                                            <div class="text-danger d-block mb-2 mt-2">{{ $item->item_description }}</div>
                                        @endif
                                        @if (!empty($item->item_image))
                                            <p></p>
                                            <img src="{{ asset($item->item_image) }}" class="img-thumbnail mb-2"
                                                width="400" alt="">
                                            <br>
                                        @endif

                                        @if ($item->item_type == '1')
                                            <select name="item_result[{{ $key }}]" class="form-select mt-2"
                                                required>
                                                <option value="1" {{ (string) $val === '1' ? 'selected' : '' }}>✅ ผ่าน
                                                </option>
                                                <option value="2" {{ (string) $val === '2' ? 'selected' : '' }}>⚠️
                                                    ผ่าน
                                                    แต่ต้องแก้ไขปรับปรุง</option>
                                                <option value="0" {{ (string) $val === '0' ? 'selected' : '' }}>❌
                                                    ไม่ผ่าน
                                                </option>
                                            </select>

                                            <textarea name="user_comment[{{ $key }}]" class="form-control mt-2"
                                                placeholder="ความคิดเห็นเพิ่มเติม (ถ้ามี)">{{ $comm }}</textarea>

                                            <div class="preview-multi d-flex flex-wrap gap-2 mt-2"></div>
                                        @elseif ($item->item_type == '2')
                                            <select name="item_result[{{ $key }}]" class="form-select mt-2"
                                                required>
                                                <option value="1" {{ (string) $val === '1' ? 'selected' : '' }}>✅ ปกติ
                                                </option>
                                                <option value="2" {{ (string) $val === '2' ? 'selected' : '' }}>⚠️
                                                    ไม่ปกติ แต่ยังสามารถใช้งานได้</option>
                                                <option value="0" {{ (string) $val === '0' ? 'selected' : '' }}>❌
                                                    ไม่สามารถใช้งานได้</option>
                                                <option value="3" {{ (string) $val === '3' ? 'selected' : '' }}>⛔
                                                    ไม่เกี่ยวข้อง</option>
                                            </select>
                                            <textarea name="user_comment[{{ $key }}]" class="form-control mt-2"
                                                placeholder="ความคิดเห็นเพิ่มเติม (ถ้ามี)">{{ $comm }}</textarea>

                                            <div class="preview-multi d-flex flex-wrap gap-2 mt-2"></div>
                                        @elseif ($item->item_type == '3')
                                            <textarea name="item_result[{{ $key }}]" class="form-control mt-2" required>{{ $val }}</textarea>

                                            <div class="preview-multi d-flex flex-wrap gap-2 mt-2"></div>
                                        @elseif ($item->item_type == '4')
                                            <input type="date" name="item_result[{{ $key }}]"
                                                class="form-control mt-2" value="{{ $val }}" required>
                                        @endif
                                        <label class="mt-2">อัปโหลดภาพ (ไม่เกิน 10 ภาพ)</label>
                                        <input type="file" name="item_images[{{ $key }}][]"
                                            class="form-control image-input-multi" multiple accept="image/*">
                                        <div class="preview-multi d-flex flex-wrap gap-2 mt-2"></div>

                                    </div>
                                @endforeach

                                <div class="border-top my-3"></div>
                                <div class="d-flex justify-content-between mt-4">
                                    <button type="submit" class="btn btn-success fs-18">บันทึกผลการตรวจ หมวดหมู่ที่
                                        {{ $category->cates_no }}.{{ $category->chk_cats_name }}</button>

                                    <a href="{{ route('user.chk_summary', $record->record_id) }}"
                                        class="btn btn-primary fs-18">
                                        ดูสรุปผล
                                    </a>
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
    <script src="https://npmcdn.com/flatpickr/dist/l10n/th.js"></script>

    <script>
        flatpickr("#date_input", {
            dateFormat: 'j F Y',
            altInput: true,
            altFormat: "j F Y",
            locale: "th",
            disableMobile: true,
            defaultDate: "today",

        });
    </script>
    <script>
        document.querySelectorAll('.image-input-multi').forEach(input => {
            input.addEventListener('change', function() {
                const previewContainer = this.nextElementSibling;
                previewContainer.innerHTML = '';

                const files = this.files;
                if (files.length > 10) {
                    alert('ไม่สามารถอัปโหลดได้เกิน 10 รูป');
                    this.value = '';
                    return;
                }

                Array.from(files).forEach(file => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.style.maxWidth = '120px';
                        img.classList.add('rounded', 'border');
                        previewContainer.appendChild(img);
                    };
                    reader.readAsDataURL(file);
                });
            });
        });
    </script>
@endpush
