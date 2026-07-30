@section('title', 'ระบบตรวจมาตรฐานรถ')
@section('description', 'ID Drives')
@extends('layout.app')
@section('content')

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">


                <div class="card mb-4 mt-4">
                    <div class="card-header">
                        <label class="fw-bold fs-20">เลือกเพื่อกลับไปยังหมวดหมู่</label>
                    </div>
                    <div class="card-body">


                        <div class="d-flex flex-wrap gap-2 mb-3">
                            @foreach ($categories as $cat)
                                @php
                                    $catId = $cat->category_id;
                                    $isChecked = in_array($catId, $checkedCategories, true);

                                    $btnClass = $isChecked ? 'btn-success' : 'btn-outline-primary';
                                @endphp
                                <a href="{{ route('user.chk_step2', ['rec' => $record->record_id, 'cats' => $cat->category_id]) }}"
                                    class="btn btn-sm {{ $btnClass }}">
                                    @if ($isChecked)
                                        <i class="fas fa-check"></i>
                                    @endif
                                    {{ $cat->cates_no }}. {{ $cat->chk_cats_name }}
                                </a>
                            @endforeach
                        </div>



                        @if (session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                    </div>
                </div>

                <div class="card mt-20 mb-20">
                    <div class="card-header bg-info">
                        <label class="fw-bold fs-20 text-dark">สรุปผลการตรวจ :
                            {{ $veh_detail->car_plate }} ({{ $veh_detail->car_brand }})

                        </label>
                    </div>



                    <div class="card-body">

                        <div class="mb-4">
                            <label class="fw-bold">ความคืบหน้าการตรวจ: {{ $checkedItems }}/{{ $totalItems }} ข้อ
                                ({{ $progress }}%)</label>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar bg-success" role="progressbar"
                                    style="width: {{ $progress }}%;" aria-valuenow="{{ $progress }}"
                                    aria-valuemin="0" aria-valuemax="100">
                                    {{ $progress }}%
                                </div>
                            </div>
                        </div>

                        @foreach ($categories as $cat)
                            <div class="alert alert-info fs-20 fw-bold">
                                {{ $cat->cates_no }}.{{ $cat->chk_cats_name }}
                            </div>

                            @php

                                $catItems = $itemsByCategory->get($cat->category_id, collect());
                            @endphp
                            <table class="table table-sm table-bordered fixed-table">
                                <thead>
                                    <tr>
                                        <th style="width:31%">รายการตรวจประเมิน</th>
                                        <th style="width:15%">ผลการประเมิน</th>
                                        <th style="width:24%">สิ่งที่ตรวจพบ</th>
                                        <th style="width:30%">ภาพ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($catItems as $item)
                                        @php
                                            $res = $results->get($item->id);
                                            $imgs = $images->get($item->id, collect());
                                        @endphp
                                        <tr>
                                            <td>{{ $item->item_no }}.{{ $item->item_name }}</td>
                                            <td>
                                                @if ($res)
                                                    @if ($res->result_value == '1')
                                                        <span class="text-success"> ผ่าน </span>
                                                    @elseif($res->result_value == '0')
                                                        <span class="text-danger">ไม่ผ่าน</span>
                                                    @elseif($res->result_value == '2')
                                                        <span class="text-secondary"> ผ่าน แต่ต้องแก้ไขปรับปรุง
                                                        </span>
                                                    @elseif($res->result_value == '3')
                                                        <span class="text-muted"> ไม่เกี่ยวข้อง
                                                        </span>
                                                    @else
                                                        {{ $res->result_value }}
                                                    @endif
                                                @else
                                                    <span class="text-danger fw-bold">ยังไม่ได้ตรวจ</span>
                                                @endif
                                            </td>
                                            <td>{{ $res->user_comment ?? '-' }}</td>


                                            <td>
                                                @if ($imgs->count())
                                                    <div class="d-flex flex-wrap gap-2">
                                                        @foreach ($imgs as $img)
                                                            @php
                                                                // ต่อ URL จาก base + image_name
                                                                $rel =
                                                                    trim($imageBase, '/') .
                                                                    '/' .
                                                                    ltrim($img->image_path, '/');
                                                                $src = asset($rel);
                                                            @endphp
                                                            <img src="{{ $src }}" data-enlarge
                                                                data-src="{{ $src }}"
                                                                data-title="{{ $item->item_no }}. {{ $item->item_name }}"
                                                                alt="ภาพข้อตรวจ {{ $item->item_no }}"
                                                                class="img-thumbnail cursor-zoom-in" width="250"
                                                                loading="lazy" onerror="this.style.display='none'">
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>

                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-muted">ไม่มีข้อตรวจในหมวดนี้</td>
                                        </tr>
                                    @endforelse


                                </tbody>
                            </table>
                            <div class="border-top my-3"></div>
                        @endforeach
                    </div>
                </div>

                @if ($record->chk_status === '0' and $progress == '100')
                    <form method="POST" action="{{ route('user.chk_confirm', ['record'=>request()->record]) }}">
                        @csrf
                        <button type="submit" class="mb-4 fs-18 btn btn-success btn-block"
                            onclick="return confirm('ยืนยันผลการตรวจนี้หรือไม่? หลังจากยืนยันแล้วจะไม่สามารถแก้ไขได้อีก')">
                            ยืนยันการตรวจ
                        </button>
                    </form>
                @elseif ($record->chk_status === '1' and $progress >= '100')
                    <div class="alert alert-info mt-20 mb-20">การตรวจนี้ถูกยืนยันแล้ว ไม่สามารถแก้ไขได้</div>
                @elseif ($record->chk_status === '0')
                    <div class="alert alert-danger mt-20 mb-20">กรุณาตรวจให้ครบทุกข้อก่อนยืนยันผลการตรวจ</div>
                    <button class="mb-4 fs-18 btn btn-primary disabled">ยืนยันการตรวจ</button>
                @endif


            </div>
        </div>

    </div>
    <!-- Modal: Preview Image -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0">
      <div class="modal-header">
        <h6 class="modal-title" id="imageModalLabel"></h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="ปิด"></button>
      </div>
      <div class="modal-body p-0 text-center" style="background:#000">
        <!-- ตัวโหลดระหว่างรอรูป -->
        <div id="imageModalSpinner" class="py-5 text-white">กำลังโหลดรูป...</div>
        <img id="imageModalImg" src="" alt="" class="w-100 d-none" style="max-height:80vh;object-fit:contain">
      </div>
      <div class="modal-footer justify-content-between">
        <small class="text-muted" id="imageModalCaption"></small>
        <a id="imageModalDownload" class="btn btn-sm btn-outline-secondary" download>ดาวน์โหลด</a>
      </div>
    </div>
  </div>
</div>

@endsection

@push('styles')
    <style>
        .fixed-table {
            table-layout: fixed;
            width: 100%;
            word-wrap: break-word;
            text-align: center;
            vertical-align: middle;
        }
    </style>
@endpush
@push('scripts')
<style>
  .cursor-zoom-in { cursor: zoom-in; }
</style>

<script>
document.addEventListener('click', function (e) {
  const img = e.target.closest('img[data-enlarge]');
  if (!img) return;

  const src   = img.dataset.src || img.src;
  const title = img.dataset.title || img.alt || '';
  const modalEl   = document.getElementById('imageModal');
  const labelEl   = document.getElementById('imageModalLabel');
  const captionEl = document.getElementById('imageModalCaption');
  const dlEl      = document.getElementById('imageModalDownload');
  const imgEl     = document.getElementById('imageModalImg');
  const spinEl    = document.getElementById('imageModalSpinner');

  // reset
  imgEl.classList.add('d-none');
  spinEl.classList.remove('d-none');
  imgEl.src = ''; // เคลียร์ก่อน

  // ใส่ข้อมูลหัว/แคปชัน/ดาวน์โหลด
  labelEl.textContent   = title;
  captionEl.textContent = src;
  dlEl.href             = src;

  // โหลดรูปใหม่ แล้วค่อยโชว์
  const preload = new Image();
  preload.onload = function () {
    imgEl.src = src;
    imgEl.alt = title;
    spinEl.classList.add('d-none');
    imgEl.classList.remove('d-none');
  };
  preload.onerror = function () {
    spinEl.textContent = 'ไม่สามารถโหลดรูปได้';
  };
  preload.src = src;

  // แสดงโมดัล
  const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
  modal.show();
});
</script>

    <!-- DataTables  -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#table-one').DataTable({
                responsive: true,
                pageLength: 25,
                language: {
                    search: "ค้นหา:",
                    lengthMenu: "แสดง _MENU_ รายการ",
                    info: "แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ",
                    paginate: {
                        next: "ถัดไป",
                        previous: "ก่อนหน้า"
                    }
                }
            });
        });
    </script>
@endpush
