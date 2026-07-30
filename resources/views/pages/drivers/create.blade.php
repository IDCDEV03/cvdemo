 @php
     use App\Enums\Role;
     $role = Auth::user()->role;
 @endphp

 @section('title', 'ระบบตรวจมาตรฐานรถ')
 @section('description', 'ID Drives')
 @extends('layout.app')
 @push('styles')
     <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
     <link rel="stylesheet"
         href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
 @endpush
 @section('content')
     <div class="container-fluid">
         <div class="row">
             <div class="col-lg-12">
                 <div class="d-flex align-items-center user-member__title mb-30 mt-30">
                     <h4 class="text-capitalize">เพิ่มพนักงานขับรถ</h4>
                 </div>

                 <div class="card mb-50">
                     <div class="card-body">
                         <form action="{{ route('drivers.store') }}" method="POST" enctype="multipart/form-data">
                             @csrf
                             <div class="row">
                                 <!-- ข้อมูลส่วนตัว -->
                                 <div class="col-md-12">
                                     <h6 class="fw-bold text-primary">ข้อมูลส่วนตัว</h6>
                                 </div>
                                 <div class="border-top my-3"></div>

                                 <div class="row">
                                     @if ($role === Role::Staff)
                                         <div class="col-md-6 mb-3">
                                             <label class="form-label">บริษัทฯว่าจ้าง <span
                                                     class="text-danger">*</span></label>
                                             <select name="company_id" id="company_id" class="form-control select2"
                                                 required>
                                                 <option value="">--- พิมพ์ค้นหาชื่อบริษัท ---</option>
                                                 @foreach ($companies as $com)
                                                     <option value="{{ $com->company_id }}">{{ $com->company_name }}
                                                     </option>
                                                 @endforeach
                                             </select>
                                         </div>
                                         <div class="col-md-6 mb-3">
                                             <label class="form-label">เลือก Supply <span
                                                     class="text-danger">*</span></label>
                                             <select name="supply_id" id="supply_id" class="form-control select2" required>
                                                 <option value="">--- รายชื่อ Supply ---</option>
                                             </select>
                                         </div>
                                     @elseif($role === Role::Company)
                                         <input type="hidden" name="company_id" value="{{ Auth::user()->company_code }}">
                                         <div class="col-md-6 mb-3">
                                             <label class="form-label">เลือก Supply <span
                                                     class="text-danger">*</span></label>
                                             <select name="supply_id" id="supply_id" class="form-control select2" required>
                                                 <option value="">--- รายชื่อ Supply ---</option>
                                                 @foreach ($supplies as $sup)
                                                     <option value="{{ $sup->sup_id }}">{{ $sup->supply_name }}</option>
                                                 @endforeach
                                             </select>
                                         </div>
                                     @elseif($role === Role::Supply)
                                         <input type="hidden" name="company_id" value="{{ Auth::user()->company_code }}">
                                         <input type="hidden" name="supply_id" value="{{ Auth::user()->supply_user_id }}">

                                     @endif
                                 </div>

                                 <div class="row">
                                     <div class="col-md-2 mb-3">
                                         <label class="form-label">คำนำหน้า <span class="text-danger">*</span></label>
                                         <select name="prefix" class="form-control" required>
                                             <option value="นาย" selected>นาย</option>
                                             <option value="นาง">นาง</option>
                                             <option value="นางสาว">นางสาว</option>
                                             <option value="พจส.">พจส.</option>
                                         </select>
                                     </div>
                                     <div class="col-md-5 mb-3">
                                         <label class="form-label">ชื่อ <span class="text-danger">*</span></label>
                                         <input type="text" name="name" class="form-control" required>
                                     </div>
                                     <div class="col-md-5 mb-3">
                                         <label class="form-label">นามสกุล <span class="text-danger">*</span></label>
                                         <input type="text" name="lastname" class="form-control" required>
                                     </div>

                                     <div class="col-md-6 mb-3">
                                         <label class="form-label">เลขที่บัตรประชาชน <span
                                                 class="text-danger">*</span></label>
                                         <input type="text" name="id_card_no" id="id_card_no" class="form-control" maxlength="13"
                                             required>
                                         <div id="id-card-feedback" class="fs-12 mt-1"></div>
                                     </div>
                                     <div class="col-md-6 mb-3">
                                         <label class="form-label">เบอร์โทรศัพท์</label>
                                         <input type="text" name="phone" class="form-control">
                                     </div>

                                     {{-- รูปภาพประจำตัว --}}
                                     <div class="col-md-12 mb-3 mt-2">
                                         <label class="form-label fw-500">รูปภาพประจำตัว</label>
                                         <div class="d-flex align-items-start gap-4 mt-1">
                                             <div class="flex-shrink-0">
                                                 <div id="profile-placeholder"
                                                      class="d-flex align-items-center rounded justify-content-center bg-light border"
                                                      style="width:110px;height:110px;cursor:pointer;"
                                                      onclick="document.getElementById('profile-file-input').click()">
                                                     <i class="fa fa-user fs-36 text-muted"></i>
                                                 </div>
                                                 <img id="profile-preview-img" src="" alt="รูปประจำตัว"
                                                      class="d-none rounded border"
                                                      style="width:110px;height:110px;object-fit:cover;">
                                             </div>
                                             <div>
                                                 <p class="text-muted fs-12 mb-2">รองรับ JPG, PNG · ขนาดสูงสุด 5 MB</p>
                                                 <div class="d-flex gap-2">
                                                     <button type="button" class="btn btn-outline-secondary btn-sm"
                                                             onclick="document.getElementById('profile-file-input').click()">
                                                         <i class="fa fa-upload me-1"></i> เลือกรูปภาพ
                                                     </button>
                                                     <button type="button" id="btn-profile-clear"
                                                             class="btn btn-outline-danger btn-sm d-none">
                                                         <i class="fa fa-trash me-1"></i> ลบ
                                                     </button>
                                                 </div>
                                                 <input type="file" id="profile-file-input" name="driver_profile"
                                                        accept="image/jpeg,image/png,image/webp" class="d-none">
                                                 <div id="profile-feedback" class="fs-12 mt-2"></div>
                                             </div>
                                         </div>
                                     </div>
<div class="border-top"></div>
                                     <!-- ข้อมูลการทำงาน -->
                                     <div class="col-md-12 mt-4">
                                         <h6 class="fw-bold text-primary">ข้อมูลการทำงานและ ใบขับขี่</h6>
                                     </div>
                                     <div class="border-top my-3"></div>

                                     <div class="col-md-6 mb-3">
                                         <label class="form-label">เลขที่ใบอนุญาตขับขี่<span
                                                 class="text-danger">*</span></label>
                                         <input type="text" name="driver_license_no" id="driver_license_no" class="form-control">
                                         <div id="license-feedback" class="fs-12 mt-1"></div>
                                     </div>
                                     <div class="col-md-6 mb-3">
                                         <label class="form-label">วันที่ใบขับขี่หมดอายุ</label>
                                         <input type="text" class="form-control date-th-input"
                                             data-hidden-id="real_license_date"
                                             placeholder="วว/ดด/ปปปป (พ.ศ.) เช่น 11/02/2570" maxlength="10">
                                         <input type="hidden" name="license_expire_date" id="real_license_date">
                                         <div class="fs-12 mt-1 date-feedback"></div>
                                     </div>

                                     <div class="col-md-6 mb-3">
                                         <label class="form-label">ทะเบียนรถที่ขับประจำ (ถ้ามี)</label>
                                         <input type="text" name="assigned_car_id" class="form-control">
                                     </div>
                                     <div class="col-md-6 mb-3">
                                         {{-- placeholder col --}}
                                     </div>

                                     {{-- ===== ข้อมูลการอบรม ===== --}}
                                     <div class="col-md-12 mt-2">
                                         <h6 class="fw-bold text-primary">ข้อมูลการอบรม</h6>
                                     </div>
                                     <div class="border-top my-3"></div>

                                     <div class="col-md-6 mb-3">
                                         <label class="form-label">อบรมโดยบริษัท <span class="text-danger">*</span></label>
                                         <select name="training_company" class="form-control" required>
                                             <option value="">-- เลือกบริษัท --</option>
                                             <option value="ID Driver">ID Driver</option>
                                             <option value="SCCC">SCCC</option>
                                         </select>
                                     </div>
                                     <div class="col-md-6 mb-3">
                                         <label class="form-label">วันที่อบรม <span class="text-danger">*</span></label>
                                         <input type="text" class="form-control date-th-input"
                                             data-hidden-id="real_training_date"
                                             placeholder="วว/ดด/ปปปป (พ.ศ.) เช่น 01/01/2568" maxlength="10" required>
                                         <input type="hidden" name="training_date" id="real_training_date">
                                         <div class="fs-12 mt-1 date-feedback"></div>
                                         <div class="fs-12 text-muted mt-1">วันหมดอายุการอบรมจะถูกคำนวณอัตโนมัติ +1 ปี</div>
                                     </div>

                                     {{-- ===== Driver Documents Section ===== --}}
                                     <div class="col-md-12 mb-3">
                                         <label class="form-label fw-500">เอกสารประจำตัวพนักงาน</label>
                                         <p class="text-muted fs-13 mb-3">
                                             เลือกอัปโหลดเอกสารที่มี —
                                             รองรับ <strong>PDF</strong> และ <strong>DOCX</strong> ขนาดสูงสุด 10 MB ต่อไฟล์
                                         </p>

                                         {{-- 4 preset slots --}}
                                         <div class="row g-3 mb-3">

                                             {{-- 1. ใบรับรองแพทย์ --}}
                                             <div class="col-md-6">
                                                 <div class="border rounded p-3 h-100" id="slot-medical">
                                                     <div class="d-flex align-items-start gap-3">
                                                         <div class="slot-icon bg-success-transparent rounded d-flex align-items-center justify-content-center flex-shrink-0"
                                                             style="width:38px;height:38px">
                                                             <i class="fa fa-stethoscope text-success"
                                                                 aria-hidden="true"></i>

                                                         </div>
                                                         <div class="flex-grow-1 min-w-0">
                                                             <div class="d-flex align-items-center gap-2 mb-1">
                                                                 <span class="fw-500 fs-14">ใบรับรองแพทย์</span>
                                                                 <span
                                                                     class="dm-tag tag-success tag-transparented">ถ้ามี</span>
                                                             </div>
                                                             <p class="text-muted fs-12 mb-2">PDF / DOCX · สูงสุด 10 MB</p>

                                                             {{-- ปุ่มเลือกไฟล์ (แสดงตอนยังไม่มีไฟล์) --}}
                                                             <button type="button"
                                                                 class="btn btn-outline-secondary btn-sm btn-slot-pick"
                                                                 data-slot="medical">
                                                                 <i class="fa fa-upload" aria-hidden="true"></i> เลือกไฟล์
                                                             </button>
                                                             <input type="file" name="doc_medical" accept=".pdf,.docx"
                                                                 class="d-none slot-file-input" data-slot="medical">

                                                             {{-- preview (ซ่อนไว้ก่อน) --}}
                                                             <div class="slot-preview d-none mt-2" data-slot="medical">
                                                                 <div
                                                                     class="d-flex align-items-center gap-2 p-2 rounded alert alert-info">
                                                                     <i class="slot-file-icon fs-16 flex-shrink-0"></i>
                                                                     <span
                                                                         class="slot-file-name fs-13 text-truncate flex-grow-1"></span>
                                                                     <span
                                                                         class="slot-file-size fs-12 text-muted flex-shrink-0"></span>
                                                                     <button type="button"
                                                                         class="btn-slot-clear btn btn-sm p-0 text-muted ms-1"
                                                                         data-slot="medical" style="line-height:1">
                                                                         <i class="fa fa-trash" aria-hidden="true"></i>
                                                                     </button>
                                                                 </div>
                                                                 <p class="text-success fs-12 mt-1 mb-0">
                                                                     <i class="fa fa-check-circle" aria-hidden="true"></i>
                                                                     พร้อมอัปโหลด
                                                                 </p>
                                                             </div>
                                                         </div>
                                                     </div>
                                                 </div>
                                             </div>

                                             {{-- 2. สำเนาใบขับขี่ --}}
                                             <div class="col-md-6">
                                                 <div class="border rounded p-3 h-100" id="slot-license">
                                                     <div class="d-flex align-items-start gap-3">
                                                         <div class="slot-icon bg-primary-transparent rounded d-flex align-items-center justify-content-center flex-shrink-0"
                                                             style="width:38px;height:38px">
                                                             <i class="fa fa-id-badge text-primary"></i>
                                                         </div>
                                                         <div class="flex-grow-1 min-w-0">
                                                             <div class="d-flex align-items-center gap-2 mb-1">
                                                                 <span class="fw-500 fs-14">สำเนาใบขับขี่</span>
                                                                 <span
                                                                     class="dm-tag tag-success tag-transparented">ถ้ามี</span>
                                                             </div>
                                                             <p class="text-muted fs-12 mb-2">PDF / DOCX · สูงสุด 10 MB</p>

                                                             <button type="button"
                                                                 class="btn btn-outline-secondary btn-sm btn-slot-pick"
                                                                 data-slot="license">
                                                                 <i class="fa fa-upload me-1" aria-hidden="true"></i>
                                                                 เลือกไฟล์
                                                             </button>
                                                             <input type="file" name="doc_license" accept=".pdf,.docx"
                                                                 class="d-none slot-file-input" data-slot="license">

                                                             <div class="slot-preview d-none mt-2" data-slot="license">
                                                                 <div
                                                                     class="d-flex align-items-center gap-2 p-2 rounded alert alert-info">
                                                                     <i class="slot-file-icon fs-16 flex-shrink-0"></i>
                                                                     <span
                                                                         class="slot-file-name fs-13 text-truncate flex-grow-1"></span>
                                                                     <span
                                                                         class="slot-file-size fs-12 text-muted flex-shrink-0"></span>
                                                                     <button type="button"
                                                                         class="btn-slot-clear btn btn-sm p-0 text-muted ms-1"
                                                                         data-slot="license" style="line-height:1">
                                                                         <i class="fa fa-trash" aria-hidden="true"></i>
                                                                     </button>
                                                                 </div>
                                                                 <p class="text-success fs-12 mt-1 mb-0">
                                                                     <i class="fa fa-check-circle" aria-hidden="true"></i>
                                                                     พร้อมอัปโหลด
                                                                 </p>
                                                             </div>
                                                         </div>
                                                     </div>
                                                 </div>
                                             </div>

                                             {{-- 3. สำเนาบัตรประชาชน --}}
                                             <div class="col-md-6">
                                                 <div class="border rounded p-3 h-100" id="slot-idcard">
                                                     <div class="d-flex align-items-start gap-3">
                                                         <div class="slot-icon bg-info-transparent rounded d-flex align-items-center justify-content-center flex-shrink-0"
                                                             style="width:38px;height:38px">
                                                             <i class="fa fa-id-card text-info"></i>
                                                         </div>
                                                         <div class="flex-grow-1 min-w-0">
                                                             <div class="d-flex align-items-center gap-2 mb-1">
                                                                 <span class="fw-500 fs-14">สำเนาบัตรประชาชน</span>
                                                                 <span
                                                                     class="dm-tag tag-success tag-transparented">ถ้ามี</span>
                                                             </div>
                                                             <p class="text-muted fs-12 mb-2">PDF / DOCX · สูงสุด 10 MB</p>

                                                             <button type="button"
                                                                 class="btn btn-outline-secondary btn-sm btn-slot-pick"
                                                                 data-slot="idcard">
                                                                 <i class="fa fa-upload me-1" aria-hidden="true"></i>
                                                                 เลือกไฟล์
                                                             </button>
                                                             <input type="file" name="doc_idcard" accept=".pdf,.docx"
                                                                 class="d-none slot-file-input" data-slot="idcard">

                                                             <div class="slot-preview d-none mt-2" data-slot="idcard">
                                                                 <div
                                                                     class="d-flex align-items-center gap-2 p-2 rounded alert alert-info">
                                                                     <i class="slot-file-icon fs-16 flex-shrink-0"></i>
                                                                     <span
                                                                         class="slot-file-name fs-13 text-truncate flex-grow-1"></span>
                                                                     <span
                                                                         class="slot-file-size fs-12 text-muted flex-shrink-0"></span>
                                                                     <button type="button"
                                                                         class="btn-slot-clear btn btn-sm p-0 text-muted ms-1"
                                                                         data-slot="idcard" style="line-height:1">
                                                                         <i class="fa fa-trash" aria-hidden="true"></i>
                                                                     </button>
                                                                 </div>
                                                                 <p class="text-success fs-12 mt-1 mb-0">
                                                                     <i class="fa fa-check-circle" aria-hidden="true"></i>
                                                                     พร้อมอัปโหลด
                                                                 </p>
                                                             </div>
                                                         </div>
                                                     </div>
                                                 </div>
                                             </div>

                                             {{-- 4. Certificate การอบรม --}}
                                             <div class="col-md-6">
                                                 <div class="border rounded p-3 h-100" id="slot-cert">
                                                     <div class="d-flex align-items-start gap-3">
                                                         <div class="slot-icon bg-warning-transparent rounded d-flex align-items-center justify-content-center flex-shrink-0"
                                                             style="width:38px;height:38px">
                                                             <i class="fa fa-certificate text-warning"></i>
                                                         </div>
                                                         <div class="flex-grow-1 min-w-0">
                                                             <div class="d-flex align-items-center gap-2 mb-1">
                                                                 <span class="fw-500 fs-14">Certificate การอบรม</span>
                                                                 <span
                                                                     class="dm-tag tag-success tag-transparented">ถ้ามี</span>
                                                             </div>
                                                             <p class="text-muted fs-12 mb-2">PDF / DOCX · สูงสุด 10 MB</p>

                                                             <button type="button"
                                                                 class="btn btn-outline-secondary btn-sm btn-slot-pick"
                                                                 data-slot="cert">
                                                                 <i class="fa fa-upload me-1" aria-hidden="true"></i>
                                                                 เลือกไฟล์
                                                             </button>
                                                             <input type="file" name="doc_cert" accept=".pdf,.docx"
                                                                 class="d-none slot-file-input" data-slot="cert">

                                                             <div class="slot-preview d-none mt-2" data-slot="cert">
                                                                 <div
                                                                     class="d-flex align-items-center gap-2 p-2 rounded alert alert-info">
                                                                     <i class="slot-file-icon fs-16 flex-shrink-0"></i>
                                                                     <span
                                                                         class="slot-file-name fs-13 text-truncate flex-grow-1"></span>
                                                                     <span
                                                                         class="slot-file-size fs-12 text-muted flex-shrink-0"></span>
                                                                     <button type="button"
                                                                         class="btn-slot-clear btn btn-sm p-0 text-muted ms-1"
                                                                         data-slot="cert" style="line-height:1">
                                                                         <i class="fa fa-trash" aria-hidden="true"></i>
                                                                     </button>
                                                                 </div>
                                                                 <p class="text-success fs-12 mt-1 mb-0">
                                                                     <i class="fa fa-check-circle" aria-hidden="true"></i>
                                                                     พร้อมอัปโหลด
                                                                 </p>
                                                             </div>
                                                         </div>
                                                     </div>
                                                 </div>
                                             </div>

                                         </div>{{-- end .row --}}

                                         {{-- เอกสารอื่นๆ --}}
                                         <div class="border rounded p-3">
                                             <div class="d-flex align-items-center gap-2 mb-2">
                                                 <i class="fa fa-paperclip text-muted"></i>
                                                 <span class="fw-500 fs-14">เอกสารอื่นๆ</span>
                                                 <span class="dm-tag tag-success tag-transparented">ถ้ามี</span>
                                             </div>
                                             <div class="d-flex gap-2 align-items-start">
                                                 <input type="text" id="extra-doc-name" name="doc_other_name"
                                                     class="form-control form-control-sm"
                                                     placeholder="ระบุชื่อเอกสาร เช่น สัญญาจ้าง" maxlength="200"
                                                     style="max-width:280px">
                                                 <button type="button" class="btn btn-outline-secondary btn-sm"
                                                     id="btn-extra-pick">
                                                     <i class="fa fa-upload me-1" aria-hidden="true"></i> เลือกไฟล์
                                                 </button>
                                                 <input type="file" name="doc_other" accept=".pdf,.docx"
                                                     class="d-none" id="extra-file-input">
                                             </div>
                                             <p class="text-muted fs-12 mt-1 mb-2">
                                                 <i class="ti ti-info-circle me-1"></i>กรอกชื่อเอกสารก่อน แล้วจึงเลือกไฟล์
                                             </p>

                                             {{-- preview เอกสารอื่นๆ --}}
                                             <div id="extra-preview" class="d-none">
                                                 <div class="d-flex align-items-center gap-2 p-2 rounded alert alert-info">
                                                     <i id="extra-file-icon" class="fs-16 flex-shrink-0"></i>
                                                     <span id="extra-file-name"
                                                         class="fs-13 text-truncate flex-grow-1"></span>
                                                     <span id="extra-file-size"
                                                         class="fs-12 text-muted flex-shrink-0"></span>
                                                     <button type="button" id="btn-extra-clear"
                                                         class="btn btn-sm p-0 text-muted ms-1" style="line-height:1">
                                                         <i class="fa fa-trash" aria-hidden="true"></i>
                                                     </button>
                                                 </div>
                                                 <p class="text-success fs-12 mt-1 mb-0">
                                                     <i class="fa fa-check-circle" aria-hidden="true"></i> พร้อมอัปโหลด
                                                 </p>
                                             </div>
                                         </div>

                                     </div>
                                     {{-- ===== End Driver Documents Section ===== --}}

                                     <div class="col-md-12 mb-3">
                                         <label class="form-label">หมายเหตุเพิ่มเติม</label>
                                         <textarea name="remark" class="form-control" rows="3"></textarea>
                                     </div>

                                 </div>
                                 <div class="border-top my-3"></div>
                                 <div class="d-flex justify-content-end gap-2 mt-4">
                                     <a href="{{ route('drivers.index') }}" class="btn btn-light btn-squared">ยกเลิก</a>
                                     <button type="submit" class="btn btn-primary btn-squared">บันทึกข้อมูล</button>
                                 </div>
                         </form>
                     </div>
                 </div>
             </div>
         </div>
     </div>
 @endsection
 @push('scripts')
     <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
     <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
     <script>
         $(document).ready(function() {
             // ใช้ Class .date-th-input เพื่อรองรับหลายๆ ช่องในหน้าเดียว
             $('.date-th-input').on('input', function(e) {
                 let input = $(this).val().replace(/[^0-9]/g, '');
                 let formattedDate = '';

                 // เติม Slash (/) อัตโนมัติ
                 if (input.length > 2) {
                     formattedDate += input.substring(0, 2) + '/';
                     if (input.length > 4) {
                         formattedDate += input.substring(2, 4) + '/';
                         formattedDate += input.substring(4, 8);
                     } else {
                         formattedDate += input.substring(2);
                     }
                 } else {
                     formattedDate = input;
                 }

                 $(this).val(formattedDate);

                 // ค้นหาช่องแจ้งเตือนและช่องซ่อนที่ "อยู่คู่กับช่องที่กำลังพิมพ์"
                 let feedback = $(this).siblings('.date-feedback');
                 let hiddenInputId = $(this).data('hidden-id');
                 let hiddenInput = $('#' + hiddenInputId);

                 if (formattedDate.length === 10) {
                     let parts = formattedDate.split('/');
                     let day = parts[0];
                     let month = parts[1];
                     let yearBE = parseInt(parts[2]);

                     if (yearBE > 2500) {
                         let yearCE = yearBE - 543;
                         let finalDateForDB = yearCE + '-' + month + '-' + day;

                         hiddenInput.val(finalDateForDB);
                         feedback.html(
                             '<span class="text-success"><i class="uil uil-check-circle"></i> รูปแบบวันที่ถูกต้อง </span>'
                         );
                         $(this).addClass('is-valid').removeClass('is-invalid');
                     } else {
                         hiddenInput.val('');
                         feedback.html(
                             '<span class="text-danger"><i class="uil uil-times-circle"></i> ใส่ปี พ.ศ. ให้ถูกต้อง</span>'
                         );
                         $(this).addClass('is-invalid').removeClass('is-valid');
                     }
                 } else {
                     hiddenInput.val('');
                     feedback.html('');
                     $(this).removeClass('is-valid is-invalid');
                 }

                 // submit
                 if ($('.is-invalid').length > 0) {
                     $('button[type="submit"]').attr('disabled', true);
                 } else {
                     $('button[type="submit"]').attr('disabled', false);
                 }
             });
         });
     </script>
     <script>
         $(document).ready(function() {
             // 1. เรียกใช้งาน Select2
             $('.select2').select2({
                 placeholder: "กรุณาเลือกข้อมูล",
                 theme: "bootstrap-5",
                 allowClear: true,
                 width: '100%' // ให้กว้างเต็มคอลัมน์
             });

             // 2. Logic เมื่อ Staff เปลี่ยนบริษัทแม่
             $('#company_id').on('change', function() {
                 let comId = $(this).val();
                 let supplySelect = $('#supply_id');

                 // ล้างข้อมูลในช่อง Supply ก่อน
                 supplySelect.empty().append('<option value="">--- กำลังโหลดข้อมูล ---</option>').trigger(
                     'change');

                 if (comId) {
                     $.ajax({
                         url: "{{ route('drivers.getSupplies') }}",
                         method: "GET",
                         data: {
                             company_id: comId
                         },
                         success: function(data) {
                             supplySelect.empty().append(
                                 '<option value="">--- เลือกหน่วยงานย่อย ---</option>');
                             $.each(data, function(key, val) {
                                 supplySelect.append('<option value="' + val.sup_id +
                                     '">' + val.supply_name + '</option>');
                             });
                             // สำคัญ: ต้องสั่ง trigger change เพื่อให้ Select2 อัปเดตหน้าตาตามข้อมูลใหม่
                             supplySelect.trigger('change');
                         }
                     });
                 } else {
                     supplySelect.empty().append('<option value="">--- เลือกหน่วยงานย่อย ---</option>')
                         .trigger('change');
                 }
             });
         });
     </script>
     <script>
         $(document).ready(function() {
          
             function checkDuplicate(inputElement, feedbackElement, columnName) {
                 let val = inputElement.val();

                 if (val.length < 5) { 
                     feedbackElement.html('');
                     return;
                 }

                 $.ajax({
                     url: "{{ route('drivers.checkDuplicate') }}",
                     method: "POST",
                     data: {
                         _token: "{{ csrf_token() }}",
                         column: columnName,
                         value: val
                     },
                     beforeSend: function() {
                         feedbackElement.html('<span class="text-info">กำลังตรวจสอบ...</span>');
                     },
                     success: function(response) {
                         if (response.exists) {
                             feedbackElement.html(
                                 '<span class="text-danger"><i class="uil uil-times-circle"></i> ข้อมูลนี้มีในระบบแล้ว</span>'
                             );
                             inputElement.addClass('is-invalid').removeClass('is-valid');
                             checkOverallStatus(); // ตรวจสอบสถานะปุ่มบันทึก
                         } else {
                             feedbackElement.html(
                                 '<span class="text-success"><i class="uil uil-check-circle"></i> สามารถใช้งานได้</span>'
                             );
                             inputElement.addClass('is-valid').removeClass('is-invalid');
                             checkOverallStatus();
                         }
                     }
                 });
             }

             // ฟังก์ชันคุมปุ่มบันทึก (ถ้ามีช่องไหน error ให้กดบันทึกไม่ได้)
             function checkOverallStatus() {
                 if ($('.is-invalid').length > 0) {
                     $('button[type="submit"]').attr('disabled', true);
                 } else {
                     $('button[type="submit"]').attr('disabled', false);
                 }
             }

             // เมื่อพิมพ์เลขบัตรประชาชน
             $('#id_card_no').on('blur', function() {
                 checkDuplicate($(this), $('#id-card-feedback'), 'id_card_no');
             });

             // เมื่อพิมพ์เลขใบขับขี่
             $('#driver_license_no').on('blur', function() {
                 checkDuplicate($(this), $('#license-feedback'), 'driver_license_no');
             });
         });
     </script>
     <script>
         (function() {
             const MAX_BYTES = 10 * 1024 * 1024;
             const EXT_RE = /\.(pdf|docx)$/i;

             // --- helper: format bytes ---
             function fmtSize(bytes) {
                 if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
                 return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
             }

             // --- helper: icon class by extension ---
             function iconClass(filename) {
                 return /\.pdf$/i.test(filename) ?
                     'ti ti-file-type-pdf text-danger' :
                     'ti ti-file-type-doc text-primary';
             }

             // --- validate file ---
             function validate(file) {
                 if (!EXT_RE.test(file.name)) {
                     Swal.fire('ประเภทไฟล์ไม่ถูกต้อง', 'รองรับเฉพาะ PDF และ DOCX เท่านั้น', 'warning');
                     return false;
                 }
                 if (file.size > MAX_BYTES) {
                     Swal.fire('ไฟล์ใหญ่เกินไป', 'ขนาดไฟล์ต้องไม่เกิน 10 MB', 'warning');
                     return false;
                 }
                 return true;
             }

             // ==========================================
             // Preset slots (medical / license / idcard / cert)
             // ==========================================
             document.querySelectorAll('.btn-slot-pick').forEach(function(btn) {
                 btn.addEventListener('click', function() {
                     var slot = this.dataset.slot;
                     document.querySelector('.slot-file-input[data-slot="' + slot + '"]').click();
                 });
             });

             document.querySelectorAll('.slot-file-input').forEach(function(input) {
                 input.addEventListener('change', function() {
                     var slot = this.dataset.slot;
                     var file = this.files[0];
                     if (!file) return;
                     if (!validate(file)) {
                         this.value = '';
                         return;
                     }

                     var preview = document.querySelector('.slot-preview[data-slot="' + slot + '"]');
                     preview.querySelector('.slot-file-icon').className = iconClass(file.name) +
                         ' slot-file-icon fs-16 flex-shrink-0';
                     preview.querySelector('.slot-file-name').textContent = file.name;
                     preview.querySelector('.slot-file-size').textContent = fmtSize(file.size);

                     // ซ่อนปุ่ม แสดง preview
                     document.querySelector('.btn-slot-pick[data-slot="' + slot + '"]').classList.add(
                         'd-none');
                     preview.classList.remove('d-none');
                 });
             });

             document.querySelectorAll('.btn-slot-clear').forEach(function(btn) {
                 btn.addEventListener('click', function() {
                     var slot = this.dataset.slot;
                     var input = document.querySelector('.slot-file-input[data-slot="' + slot + '"]');
                     input.value = '';

                     document.querySelector('.slot-preview[data-slot="' + slot + '"]').classList.add(
                         'd-none');
                     document.querySelector('.btn-slot-pick[data-slot="' + slot + '"]').classList.remove(
                         'd-none');
                 });
             });

             // ==========================================
             // Extra slot (เอกสารอื่นๆ)
             // ==========================================
             document.getElementById('btn-extra-pick').addEventListener('click', function() {
                 var docName = document.getElementById('extra-doc-name').value.trim();
                 if (!docName) {
                     Swal.fire('แจ้งเตือน', 'กรุณากรอกชื่อเอกสารก่อนเลือกไฟล์', 'info');
                     return;
                 }
                 document.getElementById('extra-file-input').click();
             });

             document.getElementById('extra-file-input').addEventListener('change', function() {
                 var file = this.files[0];
                 if (!file) return;
                 if (!validate(file)) {
                     this.value = '';
                     return;
                 }

                 document.getElementById('extra-file-icon').className = iconClass(file.name) +
                     ' fs-16 flex-shrink-0';
                 document.getElementById('extra-file-name').textContent = file.name;
                 document.getElementById('extra-file-size').textContent = fmtSize(file.size);
                 document.getElementById('extra-preview').classList.remove('d-none');
             });

             document.getElementById('btn-extra-clear').addEventListener('click', function() {
                 document.getElementById('extra-file-input').value = '';
                 document.getElementById('extra-preview').classList.add('d-none');
             });

         })();
     </script>
     <script>
         // Profile photo preview and validation
         (function() {
             var fileInput   = document.getElementById('profile-file-input');
             var previewImg  = document.getElementById('profile-preview-img');
             var placeholder = document.getElementById('profile-placeholder');
             var clearBtn    = document.getElementById('btn-profile-clear');
             var feedback    = document.getElementById('profile-feedback');
             var MAX_IMG     = 5 * 1024 * 1024;

             fileInput.addEventListener('change', function() {
                 var file = this.files[0];
                 if (!file) return;

                 if (!file.type.startsWith('image/')) {
                     Swal.fire('ประเภทไฟล์ไม่ถูกต้อง', 'รองรับเฉพาะไฟล์รูปภาพเท่านั้น', 'warning');
                     this.value = '';
                     return;
                 }
                 if (file.size > MAX_IMG) {
                     Swal.fire('ไฟล์ใหญ่เกินไป', 'ขนาดรูปต้องไม่เกิน 5 MB', 'warning');
                     this.value = '';
                     return;
                 }

                 var reader = new FileReader();
                 reader.onload = function(e) {
                     previewImg.src = e.target.result;
                     previewImg.classList.remove('d-none');
                     placeholder.classList.add('d-none');
                     clearBtn.classList.remove('d-none');
                     feedback.innerHTML = '<span class="text-success"><i class="fa fa-check-circle"></i> พร้อมอัปโหลด</span>';
                 };
                 reader.readAsDataURL(file);
             });

             clearBtn.addEventListener('click', function() {
                 fileInput.value = '';
                 previewImg.src = '';
                 previewImg.classList.add('d-none');
                 placeholder.classList.remove('d-none');
                 clearBtn.classList.add('d-none');
                 feedback.innerHTML = '';
             });
         })();
     </script>
 @endpush
