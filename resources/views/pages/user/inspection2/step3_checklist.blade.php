@section('title', 'การตรวจรถ')
@section('description', 'ID Drives - ระบบตรวจมาตรฐานรถ')
@extends('layout.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/custom-mobile.css') }}">
    <style>
        /* ปรับแต่งปุ่มเลือกสถานะ */
        .btn-status-custom { background-color: #F4F6F9; border: 1px solid #E4E7EC !important; color: #4A5568; font-weight: 600 !important; border-radius: 8px !important; transition: all 0.25s ease; }
        .btn-status-custom:hover { background-color: #E2E8F0; }
        .btn-check:checked+.btn-pass-custom { background-color: #10B981 !important; border-color: #10B981 !important; color: #ffffff !important; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25) !important; }
        .btn-check:checked+.btn-warning-custom { background-color: #F59E0B !important; border-color: #F59E0B !important; color: #ffffff !important; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.25) !important; }
        .btn-check:checked+.btn-fail-custom { background-color: #EF4444 !important; border-color: #EF4444 !important; color: #ffffff !important; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.25) !important; }
        .checklist-card { border-radius: 12px !important; border: 1px solid #EAEDF2 !important; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02) !important; }

        /* --- ปุ่ม Back to Top --- */
        #backToTop {
            position: fixed; bottom: 25px; right: 25px; z-index: 999; display: none; width: 45px; height: 45px; border-radius: 50%;
            background: #5F63F2; color: #fff; border: none; box-shadow: 0 4px 12px rgba(95, 99, 242, 0.4); cursor: pointer; transition: all 0.3s;
        }
        #backToTop:hover { transform: translateY(-3px); background: #4448dc; }

        /* --- Loader สำหรับ AJAX --- */
        .ajax-loader-overlay {
            position: absolute; top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(255, 255, 255, 0.6); z-index: 10;
            display: flex; justify-content: center; align-items: flex-start;
            padding-top: 50px; border-radius: 12px; backdrop-filter: blur(2px);
        }
    </style>
@endpush

@section('content')
  

    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-md-10 col-lg-8 col-xl-7">

                <div class="d-flex justify-content-between align-items-center mb-3 bg-white p-3 rounded-3 shadow-sm border" style="border-color: #EAEDF2 !important;">
                    <div class="d-flex flex-column">
                        <h5 class="mb-0 fw-bold text-dark">
                            <i class="uil uil-clipboard-notes text-primary me-2"></i>{{ $formGroup->form_group_name ?? 'แบบฟอร์มการตรวจ' }}
                        </h5>
                        <div class="mt-1" style="padding-left: 28px;">
                            <span class="fs-16 fw-bold text-primary">{{ $vehicle->car_plate ?? 'ไม่ระบุ' }}</span>
                        </div>
                    </div>
                    <a href="{{ route('user.inspection.step4', $record->record_id) }}" class="btn btn-success btn-sm radius-xs shadow-sm fw-bold px-3">
                        สรุปผล <i class="uil uil-arrow-right ms-1"></i>
                    </a>
                </div>

       
                <div class="card mb-4 border-0 shadow-sm" style="border-radius: 12px;">
                    <div class="card-body p-3">
                         <p class="text-muted mb-2" style="font-size: 11px; letter-spacing: 0.05em; text-transform: uppercase;">หมวดหมู่การตรวจ</p>
                        <div class="row g-2 justify-content-center justify-content-md-start" id="category-buttons">
                            @foreach ($categories as $cat)
                                @php
                                    $nameLength = mb_strlen($cat->chk_cats_name, 'UTF-8');
                                    $colClass = $nameLength > 16 ? 'col-12 col-md-auto' : 'col-6 col-md-auto';
                                @endphp

                                <div class="{{ $colClass }}">
                                    <button type="button" 
                                        data-url="{{ route('user.inspection.step3', ['record_id' => $record->record_id, 'cat_id' => $cat->category_id]) }}"
                                        class="category-ajax-btn btn w-100 rounded-3 fw-bold d-flex align-items-center justify-content-center
                                            {{ $currentCategoryId == $cat->category_id ? 'btn-info shadow active-cat' : 'btn-outline-info' }}"
                                        style="min-height: 48px; padding: 8px 18px; font-size: 12px; white-space: normal; line-height: 1.2;">
                                        {{ $cat->chk_cats_name }}
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div id="checklist-container" class="position-relative min-vh-50">
                    
                    <div id="ajax-spinner" class="ajax-loader-overlay d-none">
                        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;"></div>
                    </div>

                    <div class="d-flex flex-column gap-3" id="checklist-items-wrapper">
                        @foreach ($items as $item)
                            @php
                                $result = $existingResults->get($item->item_id);
                                $status = $result ? $result->result_status : '';
                                $val = $result ? $result->result_value : '';
                                $comment = $result ? $result->user_comment : '';
                                $images = $existingImages->get($item->item_id) ?? collect();

                                $isType2 = $item->item_type == 2;
                                $lblPass = $isType2 ? 'ปกติ' : 'ผ่าน';
                                $lblAlmost = $isType2 ? 'ไม่ปกติ แต่ยังสามารถใช้งานได้' : 'ผ่าน แต่แก้ไข';
                                $lblFail = $isType2 ? 'ไม่ปกติ' : 'ไม่ผ่าน';
                            @endphp

                            <div class="card checklist-card">
                                <div class="card-body p-4">
                                    <div class="mb-3">
                                        <div class="fw-bold text-dark lh-base fs-16">
                                            {{ $loop->iteration }}. {{ $item->item_name }}
                                        </div>
                                        @if (!empty($item->item_description))
                                            <div class="text-danger fs-14 mt-1">{!! nl2br(e($item->item_description)) !!}</div>
                                        @endif
                                    </div>

                                    <input type="hidden" class="data-record" value="{{ $record->record_id }}">
                                    <input type="hidden" class="data-item" value="{{ $item->item_id }}">

                                    @if (in_array($item->item_type, [1, 2, 6]))
                                        <div class="d-flex flex-column gap-2 mb-3">
                                            <div class="row g-2">
                                                <div class="col-6">
                                                    <input type="radio" class="btn-check btn-pass" name="status_{{ $item->item_id }}" id="pass_{{ $item->item_id }}" value="1" {{ $status === '1' ? 'checked' : '' }}>
                                                    <label class="btn btn-status-custom btn-pass-custom w-100 py-2 d-flex justify-content-center align-items-center gap-2" for="pass_{{ $item->item_id }}">
                                                        <i class="uil uil-check-circle fs-18"></i> <span>{{ $lblPass }}</span>
                                                    </label>
                                                </div>
                                                <div class="col-6">
                                                    <input type="radio" class="btn-check btn-fail" name="status_{{ $item->item_id }}" id="fail_{{ $item->item_id }}" value="0" {{ $status === '0' ? 'checked' : '' }}>
                                                    <label class="btn btn-status-custom btn-fail-custom w-100 py-2 d-flex justify-content-center align-items-center gap-2" for="fail_{{ $item->item_id }}">
                                                        <i class="uil uil-times-circle fs-18"></i> <span>{{ $lblFail }}</span>
                                                    </label>
                                                </div>
                                            </div>

                                            @if ($item->item_type == 1 || $item->item_type == 2)
                                                <div class="w-100">
                                                    <input type="radio" class="btn-check btn-warning" name="status_{{ $item->item_id }}" id="almost_{{ $item->item_id }}" value="2" {{ $status === '2' ? 'checked' : '' }}>
                                                    <label class="btn btn-status-custom btn-warning-custom w-100 py-2 fs-13 d-flex align-items-center justify-content-center gap-2" for="almost_{{ $item->item_id }}">
                                                        <i class="uil uil-exclamation-triangle fs-18"></i> <span>{{ $lblAlmost }}</span>
                                                    </label>
                                                </div>
                                            @endif
                                        </div>
                                    @endif

                                    @if (in_array($item->item_type, [3, 4, 5, 6]))
                                        <div class="mb-2">
                                            @if ($item->item_type == 3 || $item->item_type == 6)
                                                <input type="text" class="form-control bg-light input-value radius-xs border-0" placeholder="ระบุข้อความ..." value="{{ $val }}">
                                            @elseif($item->item_type == 4)
                                                <input type="date" class="form-control bg-light input-value radius-xs border-0" value="{{ $val }}">
                                            @elseif($item->item_type == 5)
                                                <input type="number" class="form-control bg-light input-value radius-xs border-0" placeholder="ระบุตัวเลข..." value="{{ $val }}">
                                            @endif
                                        </div>
                                    @endif

                                    <div class="detail-box pt-3 mt-3 border-top {{ $status === '0' || $status === '2' ? '' : 'd-none' }}" id="detail_box_{{ $item->item_id }}">
                                        <label class="small fw-bold mb-2 {{ $status === '0' ? 'text-danger' : 'text-warning' }}">
                                            <i class="uil uil-comment-info"></i> สิ่งที่ตรวจพบ
                                        </label>
                                        <textarea class="form-control bg-light input-comment radius-xs border-0 mb-3" rows="2" placeholder="อธิบายเพิ่มเติม...">{{ $comment }}</textarea>

                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label class="small text-dark fw-bold mb-0">ภาพประกอบ <span id="count_{{ $item->item_id }}">({{ $images->count() }}/10)</span></label>
                                            <button type="button" class="btn btn-outline-primary btn-xs radius-xs" onclick="document.getElementById('file_{{ $item->item_id }}').click();">
                                                <i class="uil uil-camera-plus"></i> เพิ่มภาพ
                                            </button>
                                            <input type="file" id="file_{{ $item->item_id }}" class="d-none image-uploader" accept="image/*" capture="environment" data-item="{{ $item->item_id }}">
                                        </div>

                                        <div class="row g-2 image-gallery" id="gallery_{{ $item->item_id }}">
                                            @foreach ($images as $img)
                                                <div class="col-3 col-md-2 position-relative img-wrapper-{{ $img->id }}">
                                                    <img src="{{ asset($img->image_path) }}" class="img-fluid rounded border" style="height: 70px; width: 100%; object-fit: cover;">
                                                    <button type="button" class="btn btn-danger btn-xs position-absolute top-0 end-0 m-1 rounded-circle p-0" style="width: 20px; height: 20px;" onclick="deleteImage({{ $img->id }}, '{{ $item->item_id }}')">
                                                        <i class="uil uil-times fs-12"></i>
                                                    </button>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <button id="backToTop" title="กลับขึ้นบนสุด">
                    <i class="uil uil-arrow-up" style="font-size: 20px;"></i>
                </button>

            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                const backToTopBtn = $('#backToTop');
                $(window).scroll(function() {
                    if ($(window).scrollTop() > 300) backToTopBtn.fadeIn();
                    else backToTopBtn.fadeOut();
                });
                backToTopBtn.click(function() {
                    $('html, body').animate({ scrollTop: 0 }, 400);
                    return false;
                });

                // 🌟 ระบบ AJAX เปลี่ยนหมวดหมู่ 🌟
                $('.category-ajax-btn').click(function(e) {
                    e.preventDefault();
                    let btn = $(this);
                    let url = btn.data('url');

                    // 1. เปลี่ยนสีปุ่มทันที
                    $('.category-ajax-btn').removeClass('btn-info shadow active-cat').addClass('btn-outline-info');
                    btn.removeClass('btn-outline-info').addClass('btn-info shadow active-cat');

                    // 2. แสดงตัวโหลดดิ้ง (Spinner)
                    $('#ajax-spinner').removeClass('d-none');

                    // 3. ยิง AJAX ไปดึงข้อมูลมาเฉพาะส่วน
                    $.ajax({
                        url: url,
                        type: 'GET',
                        success: function(response) {
                            // ดึงเฉพาะก้อน HTML ที่อยู่ใน #checklist-items-wrapper มาแปะทับ
                            let newHtml = $(response).find('#checklist-items-wrapper').html();
                            $('#checklist-items-wrapper').html(newHtml);

                            // ซ่อนโหลดดิ้ง
                            $('#ajax-spinner').addClass('d-none');
                        },
                        error: function() {
                            alert('เกิดข้อผิดพลาดในการโหลดข้อมูล กรุณาลองใหม่อีกครั้ง');
                            $('#ajax-spinner').addClass('d-none');
                        }
                    });
                });
            });
        </script>
        
       <script>
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            function saveItemData(card) {
                const recordId = card.querySelector('.data-record').value;
                const itemId = card.querySelector('.data-item').value;
                let resultStatus = null;
                const checkedRadio = card.querySelector('input[type="radio"]:checked');
                if (checkedRadio) resultStatus = checkedRadio.value;
                let resultValue = null;
                const valueInput = card.querySelector('.input-value');
                if (valueInput) resultValue = valueInput.value;
                let userComment = null;
                const commentInput = card.querySelector('.input-comment');
                if (commentInput) userComment = commentInput.value;

                fetch('{{ route('user.inspection.saveResult') }}', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json', 
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json' // บังคับรับค่า JSON
                    },
                    body: JSON.stringify({ record_id: recordId, item_id: itemId, result_status: resultStatus, result_value: resultValue, user_comment: userComment })
                });
            }

            // 🌟 ใช้งาน Event Delegation เพื่อให้ AJAX ทำงานร่วมกับฟังก์ชันได้ 🌟
            $(document).on('change', 'input[type="radio"].btn-check', function() {
                const card = this.closest('.card-body');
                const itemId = card.querySelector('.data-item').value;
                const detailBox = document.getElementById('detail_box_' + itemId);
                if (this.value === '0' || this.value === '2') {
                    detailBox.classList.remove('d-none');
                } else {
                    detailBox.classList.add('d-none');
                }
                saveItemData(card);
            });

            $(document).on('change', '.input-value, .input-comment', function() { 
                saveItemData(this.closest('.card-body')); 
            });

            // 🌟 อัปโหลดรูปภาพ (แก้ไขแล้ว) 🌟
            $(document).on('change', '.image-uploader', function() {
                const itemId = this.dataset.item;
                const card = this.closest('.card-body');
                const recordId = card.querySelector('.data-record').value;
                const file = this.files[0];
                if (!file) return;

                const gallery = document.getElementById('gallery_' + itemId);
                const tempId = Date.now();
                const objectUrl = URL.createObjectURL(file);
                const tempHtml = `
                    <div class="col-3 col-md-2 position-relative img-wrapper-temp-${tempId}">
                        <img src="${objectUrl}" class="img-fluid rounded border" style="height: 70px; width: 100%; object-fit: cover; opacity: 0.5;">
                        <div class="position-absolute top-50 start-50 translate-middle">
                            <i class="uil uil-spinner fa-spin text-primary fs-20"></i>
                        </div>
                    </div>`;
                gallery.insertAdjacentHTML('beforeend', tempHtml);

                const formData = new FormData();
                formData.append('image', file);
                formData.append('item_id', itemId);
                formData.append('record_id', recordId);

                fetch('{{ route('user.inspection.uploadItemImage') }}', { 
                    method: 'POST', 
                    headers: { 
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json' // บังคับรับค่า JSON
                    }, 
                    body: formData 
                })
                .then(res => res.text())
                .then(text => {
                    try {
                        const data = JSON.parse(text);
                        if (data.success || data.status === 'success') {
                            document.querySelector(`.img-wrapper-temp-${tempId}`).remove();
                            if (data.image_id) {
                                const finalHtml = `
                                    <div class="col-3 col-md-2 position-relative img-wrapper-${data.image_id}">
                                        <img src="${objectUrl}" class="img-fluid rounded border" style="height: 70px; width: 100%; object-fit: cover;">
                                        <button type="button" class="btn btn-danger btn-xs position-absolute top-0 end-0 m-1 rounded-circle p-0" style="width: 20px; height: 20px;" onclick="deleteImage(${data.image_id}, '${itemId}')">
                                            <i class="uil uil-times fs-12"></i>
                                        </button>
                                    </div>`;
                                gallery.insertAdjacentHTML('beforeend', finalHtml);
                                const countSpan = document.getElementById('count_' + itemId);
                                if (countSpan) {
                                    let currentCount = parseInt(countSpan.innerText.match(/\d+/)[0]) || 0;
                                    countSpan.innerText = `(${currentCount + 1}/10)`;
                                }
                            } else { 
                                // ถ้าไม่มี ID รูปกลับมา ให้คลิกโหลดหมวดหมู่ซ้ำแบบ AJAX
                                $('.category-ajax-btn.active-cat').trigger('click'); 
                            }
                        } else { 
                            alert('อัปโหลดไม่สำเร็จ กรุณาลองใหม่');
                            document.querySelector(`.img-wrapper-temp-${tempId}`).remove();
                        }
                    } catch (e) { 
                        // 🌟 แก้ปัญหาหน้ากระพริบ: ถ้าระบบส่งกลับมาไม่ใช่ JSON ให้โหลด AJAX ซ้ำแทนการรีเฟรชจอ
                        $('.category-ajax-btn.active-cat').trigger('click'); 
                    }
                })
                .catch(error => { 
                    console.error('Upload Error:', error); 
                    $('.category-ajax-btn.active-cat').trigger('click'); 
                });
                
                this.value = '';
            });

            // 🌟 ลบรูปภาพ (แก้ไขแล้ว) 🌟
            function deleteImage(imageId, itemId) {
                if (!confirm('ยืนยันการลบรูปภาพ?')) return;
                const imgWrapper = document.querySelector(`.img-wrapper-${imageId}`);
                if (imgWrapper) imgWrapper.style.opacity = '0.5';

                fetch('{{ route('user.inspection.deleteItemImage') }}', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json', 
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json' 
                    },
                    body: JSON.stringify({ image_id: imageId })
                })
                .then(res => res.text())
                .then(text => {
                    try {
                        const data = JSON.parse(text);
                        if (data.success || data.status === 'success') {
                            if (imgWrapper) imgWrapper.remove();
                            const countSpan = document.getElementById('count_' + itemId);
                            if (countSpan) {
                                let currentCount = parseInt(countSpan.innerText.match(/\d+/)[0]) || 0;
                                if (currentCount > 0) countSpan.innerText = `(${currentCount - 1}/10)`;
                            }
                        } else { $('.category-ajax-btn.active-cat').trigger('click'); }
                    } catch (e) { 
                        // 🌟 แก้ปัญหาหน้ากระพริบ: ถ้าระบบส่งกลับมาไม่ใช่ JSON ให้โหลด AJAX ซ้ำ
                        $('.category-ajax-btn.active-cat').trigger('click'); 
                    }
                })
                .catch(error => { console.error('Delete Error:', error); $('.category-ajax-btn.active-cat').trigger('click'); });
            }
        </script>
    @endpush
@endsection