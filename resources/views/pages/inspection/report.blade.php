 @php
     use App\Enums\Role;
     $role = Auth::user()->role;
 @endphp
 @section('title', 'รายงานการตรวจ: ' . ($vehicle->car_plate ?? ''))
 @section('description', 'ID Drives - ระบบตรวจมาตรฐานรถ')
 @extends('layout.app')
 @push('styles')
     <style>
         /* สไตล์สำหรับการพิมพ์รายงาน */
         .report-container {
             background: #fff;
             border-radius: 4px;
         }

         .report-header-content img,
         .report-footer-section img {
             max-height: 60px !important;
             width: auto !important;
         }

         /* จัดการการแสดงผลตาราง Checklist ให้ดูเป็นระเบียบ */
         .table-checklist {
             width: 100%;
             border-collapse: collapse;
             margin-bottom: 20px;
         }

         .table-checklist th {
             background-color: #666;
             color: #fff;
             padding: 8px;
             text-align: center;
             font-size: 14px;
         }

         .table-checklist td {
             border: 1px solid #ccc;
             padding: 8px;
             font-size: 14px;
             vertical-align: top;
         }

         .category-title {
             background-color: #f0f2f5;
             padding: 10px;
             font-weight: bold;
             margin-top: 18px;
         }

         .appendix-card {
             border: 1px solid #dee2e6;
             border-radius: 8px;
             margin-bottom: 20px;
             background-color: #fff;
             page-break-inside: avoid;
             /* ป้องกันไม่ให้ Card ถูกตัดขาดครึ่งเมื่อขึ้นหน้าใหม่ตอนปริ้น */
         }

         .appendix-header {
             background-color: #f8f9fa;
             padding: 10px 15px;
             border-bottom: 1px solid #dee2e6;
             border-top-left-radius: 8px;
             border-top-right-radius: 8px;
             font-size: 14px;
         }

         .appendix-body {
             padding: 15px;
         }

         .appendix-footer {
             background-color: #fff;
             padding: 10px 15px;
             border-top: 1px dashed #dee2e6;
             border-bottom-left-radius: 8px;
             border-bottom-right-radius: 8px;
             font-size: 13px;
         }

         .img-gallery {
             display: flex;
             flex-wrap: wrap;
             gap: 15px;
         }

         .img-gallery-item {
             width: calc(33.333% - 10px);
             /* เรียง 3 รูปต่อ 1 แถว */
         }

         .appendix-img {
             width: 100%;
             height: 200px;
             object-fit: cover;
             border-radius: 6px;
             border: 1px solid #ccc;
             box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
         }

         .pre-inspect-img {
             width: 100%;
             height: auto;
             max-width: 100%;
             object-fit: contain;
             border-radius: 6px;
             border: 1px solid #ccc;
             box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
             display: block;
         }

         @media print {
             @page {
                 size: A4 portrait;
                 margin: 10mm;
                 /* ตั้งขอบกระดาษไว้ที่ 1 ซม. (ปรับได้ตามต้องการ) */
             }

             /* ซ่อนเมนูระบบ */
             body * {
                 visibility: hidden;
             }

             /* แสดงเฉพาะพื้นที่รายงาน */
             #printable-area,
             #printable-area * {
                 visibility: visible;
             }

             #printable-area {
                 position: absolute;
                 left: 0;
                 top: 0;
                 width: 100%;
             }

             /* ซ่อนปุ่มและเคลียร์พื้นหลัง */
             .no-print {
                 display: none !important;
             }

             body {
                 background: #fff;
                 padding: 0;
                 margin: 0;
             }

             .report-container {
                 box-shadow: none;
                 border: none;
                 padding: 0;
             }

             .report-header-content img,
             .report-footer-section img {
                 max-height: 60px !important;
                 width: auto !important;
             }

             .img-gallery-item {
                 width: calc(25% - 12px) !important;
             }

             .appendix-img {
                 max-height: none !important;
                 height: 150px !important;
                 width: 100% !important;
                 object-fit: cover !important;
             }

             .pre-inspect-img {
                 height: auto !important;
                 max-height: none !important;
                 width: 100% !important;
                 object-fit: contain !important;
             }

             table,
             tr,
             td {
                 page-break-inside: avoid;
             }

             .page-break-before {
                 page-break-before: always;
             }
         }
     </style>
 @endpush

 @section('content')
     <div class="container py-4">
         <div class="d-flex justify-content-between align-items-center mb-4 no-print">

             @if ($role === Role::Inspector)
                 <a href="{{ route('ins-dashboard') }}" class="btn btn-warning border">
                     <i class="uil uil-arrow-left"></i> กลับ
                 </a>
             @elseif ($role === Role::User)
                 <a href="{{ route('local.home') }}" class="btn btn-warning border">
                     <i class="uil uil-arrow-left"></i> กลับ
                 </a>
             @endif

             <button onclick="window.print()" class="btn btn-secondary">
                 <i class="uil uil-print"></i> พิมพ์ / บันทึก PDF
             </button>
         </div>

         {{-- ========================================== --}}
         {{-- Email submission data: copyable table --}}
         {{-- (Inspector copies this and pastes into email to head office) --}}
         {{-- ========================================== --}}
         <div class="card shadow-sm mb-20 no-print">
             <div class="card-body">
                 <div class="d-flex justify-content-between align-items-center">
                     <span class="fs-18 fw-bold">
                         <i class="uil uil-envelope-edit me-1"></i> ข้อมูลสำหรับส่งตรวจ
                     </span>
                 </div>

                 <div class="border-top my-3"></div>

                 <div class="table-responsive">
                     <table id="emailSubmitTable" class="table table-bordered table-sm align-middle mb-0"
                         style="font-size: 13px;">
                         <thead class="table-light">
                             <tr class="text-center">
                                 <th>บริษัทผู้ขนส่ง</th>
                                 <th>ทะเบียนหัว</th>
                                 <th>ทะเบียนหาง</th>
                                 <th>ชนิดรถ</th>
                                 <th>วันที่จดทะเบียนหัว</th>
                                 <th>น้ำหนักรวมสูงสุด (T)</th>
                                 <th>น้ำหนักรถเปล่าหัว+หาง (T)</th>
                                 <th>วันที่ประกันหมดอายุ</th>
                                 <th>ภาษีหมดอายุ</th>
                                 <th>ประเภทสินค้า</th>
                                 <th>ชนิดเชื้อเพลิง</th>
                                 <th>วันที่ตรวจครั้งที่ 1</th>
                                 <th>วันที่ตรวจครั้งที่ 2</th>
                                 <th>วันที่ตรวจครั้งที่ 3</th>
                                 <th>วันที่ตรวจผ่าน</th>
                                 <th>สรุปผลการตรวจ</th>
                             </tr>
                         </thead>
                         <tbody>
                             <tr class="text-center">
                                 <td>{{ $supply->supply_name ?? '-' }}</td>
                                 <td>{{ $vehicle->car_plate ?? '-' }}</td>
                                 <td>{{ $record->trailer_plate ?? '-' }}</td>
                                 <td>{{ $vehicle->vehicle_type ?? '-' }}</td>
                                 <td>{{ $vehicle->car_register_date ? thai_date($vehicle->car_register_date) : '-' }}</td>
                                 <td>{{ $vehicle->car_total_weight ?? '-' }}</td>
                                 <td>{{ $vehicle->car_weight ?? '-' }}</td>
                                 <td>{{ $vehicle->car_insurance_expire ? thai_date($vehicle->car_insurance_expire) : '-' }}
                                 </td>
                                 <td>{{ $vehicle->car_tax ? thai_date($vehicle->car_tax) : '-' }}</td>
                                 <td>{{ $vehicle->car_product ?? '-' }}</td>
                                 <td>{{ $vehicle->car_fuel_type ?? '-' }}</td>
                                 <td>{{ $roundDates['round1'] ? thai_date($roundDates['round1']) : '-' }}</td>
                                 <td>{{ $roundDates['round2'] ? thai_date($roundDates['round2']) : '-' }}</td>
                                 <td>{{ $roundDates['round3'] ? thai_date($roundDates['round3']) : '-' }}</td>
                                 <td>{{ $passedDate ? thai_date($passedDate) : '-' }}</td>
                                 <td>{{ $evaluateText }}</td>
                             </tr>
                         </tbody>
                     </table>
                 </div>
             </div>
         </div>

         <div id="printable-area">
             <div class="report-container p-2">

                 <div class="report-header-content text-dark ">
                     {!! $reportTemplate->header_html !!}
                 </div>

                 <div class="report-body-content my-4">
                     <span class="fw-bold mb-3 text-dark">ส่วนที่ 2 รายการตรวจสมรรถนะของรถ</span>

                     @foreach ($categories as $cat)
                         @if (isset($results[$cat->category_id]))
                             <div class="category-title">หัวข้อประเมิน : {{ $cat->chk_cats_name }}</div>


                             <table class="table table-bordered ">
                                 <thead>
                                     <tr>
                                         <th width="5%"></th>
                                         <th align="center" width="40%">รายการตรวจ</th>
                                         <th class="text-center" width="20%">ผลประเมิน</th>
                                         <th class="text-center" width="35%">สิ่งที่ตรวจพบ</th>
                                     </tr>
                                 </thead>
                                 <tbody>
                                     @foreach ($results[$cat->category_id] as $res)
                                         <tr>
                                             <td class="text-center text-small">{{ $loop->iteration }}</td>
                                             <td class="text-small">
                                                 <span> {{ $res->item_name }} </span>

                                             </td>
                                             <td align="center" class="text-small">
                                                 @if ($res->result_status == '1')
                                                     <span>ปกติ</span>
                                                 @elseif($res->result_status == '2')
                                                     <span class="text-small">ไม่ปกติ แต่ยังสามารถใช้งานได้</span>
                                                 @elseif($res->result_status == '0')
                                                     <span style="color: red;">ไม่ปกติ</span>
                                                 @endif
                                             </td>
                                             <td>
                                                 @if ($res->result_value)
                                                     <span class="text-dark text-small">{{ $res->result_value }}</span>
                                                 @endif
                                                 @if ($res->user_comment)
                                                     <span
                                                         class="text-dark text-small"><em>{{ $res->user_comment }}</em></span>
                                                 @endif


                                             </td>
                                         </tr>
                                     @endforeach
                                 </tbody>
                             </table>
                         @endif
                     @endforeach
                 </div>

                 <div class="report-footer-section text-dark">
                     {!! $reportTemplate->footer_html !!}
                 </div>

                 {{-- ===== รูปภาพก่อนตรวจรถ (pre-inspection photos) ===== --}}
                 @if ($preInspectPhotos->count() > 0)
                 <div class="mt-5 page-break-before">
                     <div style="border-bottom: 2px solid #333; margin-bottom: 20px; padding-bottom: 5px;">
                         <h5 class="fw-bold text-dark mb-0">รูปภาพก่อนตรวจรถ</h5>
                         <div class="text-dark" style="font-size: 14px;">
                             <strong>ทะเบียนรถ:</strong> {{ $vehicle->car_plate ?? '-' }} |
                             <strong>วันที่ตรวจ:</strong> {{ thai_date($record->inspect_date ?? $record->created_at) }}
                         </div>
                     </div>
                     <div class="img-gallery">
                         @foreach ($preInspectPhotos as $photo)
                             <div class="img-gallery-item text-center">
                                 <img src="{{ asset($photo->file_path) }}" class="pre-inspect-img"
                                      alt="{{ $photo->photo_label }}"
                                      onerror="this.style.display='none'">
                                 <div class="text-dark mt-1" style="font-size: 13px; font-weight: 600;">
                                     {{ $photo->photo_label }}
                                 </div>
                             </div>
                         @endforeach
                     </div>
                 </div>
                 @endif

                 <div class="report-image-appendix mt-5 page-break-before {{ $itemImages->isEmpty() ? 'no-print' : '' }}">
                     <div style="border-bottom: 2px solid #333; margin-bottom: 20px; padding-bottom: 5px;">
                         <h5 class="fw-bold text-dark mb-0">รูปภาพประกอบการตรวจ</h5>
                         <div class="text-dark" style="font-size: 14px;">
                             <strong>ทะเบียนรถ:</strong> {{ $vehicle->car_plate ?? '-' }} |
                             <strong>วันที่ตรวจ:</strong> {{ thai_date($record->inspect_date ?? $record->created_at) }}
                         </div>
                     </div>

                     @php $appendixIndex = 1; @endphp

                     @foreach ($categories as $cat)
                         @foreach ($results[$cat->category_id] ?? [] as $res)
                             @if ($itemImages->has($res->item_id))
                                 <div class="appendix-card">
                                     <div class="appendix-header d-flex justify-content-between align-items-center">
                                         <strong class="text-dark">
                                             ภาพที่ {{ $appendixIndex++ }}. ข้อตรวจ: {{ $res->item_name }}
                                         </strong>
                                         <span>
                                             ผลประเมิน:
                                             @if ($res->result_status == '1')
                                                 <span style="color: green; font-weight: bold;">ปกติ</span>
                                             @elseif($res->result_status == '2')
                                                 <span style="color: rgb(218, 135, 27); font-weight: bold;">ไม่ปกติ
                                                     แต่ยังสามารถใช้งานได้</span>
                                             @elseif($res->result_status == '0')
                                                 <span style="color: red; font-weight: bold;">ไม่ปกติ</span>
                                             @endif
                                         </span>
                                     </div>

                                     <div class="appendix-body">
                                         <div class="img-gallery">
                                             @foreach ($itemImages[$res->item_id] as $img)
                                                 <div class="img-gallery-item text-center">
                                                     <img src="{{ asset($img->image_path) }}" class="appendix-img">
                                                 </div>
                                             @endforeach
                                         </div>
                                     </div>

                                     @if ($res->user_comment || $res->result_value)
                                         <div class="appendix-footer">
                                             <strong class="text-dark">สิ่งที่ตรวจพบ / หมายเหตุ:</strong>
                                             <span style="color: #555;">{{ $res->result_value }}
                                                 {{ $res->user_comment }}</span>
                                         </div>
                                     @endif
                                 </div>
                             @endif
                         @endforeach
                     @endforeach

                     @if ($itemImages->isEmpty())
                         <p class="text-muted text-center" style="font-style: italic;">--- ไม่มีรูปภาพประกอบในรายงานฉบับนี้ ---</p>
                     @endif
                 </div>

                 {{-- ===== เอกสารแนบรถ ===== --}}
                 @if ($vehicleDocs->count() || $preInspectDocs->count())
                     <div class="mt-5 no-print">
                         <div style="border-bottom: 2px solid #333; margin-bottom: 20px; padding-bottom: 5px;">
                             <h5 class="fw-bold text-dark mb-0">เอกสารแนบรถ</h5>
                             <div class="text-dark" style="font-size: 14px;">
                                 <strong>ทะเบียนรถ:</strong> {{ $vehicle->car_plate ?? '-' }}
                             </div>
                         </div>

                         <div class="row g-3">
                             @foreach ($vehicleDocs as $doc)
                                 <div class="col-md-6">
                                     <div class="d-flex align-items-center justify-content-between border rounded p-3">
                                         <div class="d-flex align-items-center gap-3">
                                             <i class="uil uil-file-alt fs-24 text-danger"></i>
                                             <div>
                                                 <div class="fw-500 fs-14">{{ $doc->doc_name }}</div>
                                               
                                             </div>
                                         </div>
                                         <a href="{{ Storage::url($doc->file_path) }}" target="_blank"
                                             class="btn btn-outline-danger btn-sm">
                                             <i class="uil uil-print me-1"></i>เปิด / พิมพ์
                                         </a>
                                     </div>
                                 </div>
                             @endforeach

                             @foreach ($preInspectDocs as $doc)
                                 <div class="col-md-6">
                                     <div class="d-flex align-items-center justify-content-between border rounded p-3">
                                         <div class="d-flex align-items-center gap-3">
                                             <i class="uil uil-file-alt fs-24 text-danger"></i>
                                             <div>
                                                 <div class="fw-500 fs-14">{{ $doc->doc_name }}</div>
                                                 
                                             </div>
                                         </div>
                                         <a href="{{ url($doc->file_path) }}" target="_blank"
                                             class="btn btn-outline-danger btn-sm">
                                             <i class="uil uil-print me-1"></i>เปิด / พิมพ์
                                         </a>
                                     </div>
                                 </div>
                             @endforeach
                         </div>
                     </div>
                 @endif

             </div>
         </div>
     </div>
 @endsection
