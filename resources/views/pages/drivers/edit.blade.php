@php
    use App\Enums\Role;
    $role = Auth::user()->role;
    $roleString = is_object($role) ? $role->value : $role;
@endphp

@section('title', 'แก้ไขข้อมูลพนักงานขับรถ')
@section('description', 'ID Drives')
@extends('layout.app')
@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="d-flex align-items-center user-member__title mb-30 mt-30">
                <h4 class="text-capitalize">แก้ไขข้อมูลพนักงานขับรถ </h4>
            </div>

            <div class="row g-3">

            {{-- Left column: edit form --}}
            <div class="col-md-6">
            <div class="card mb-50">
                <div class="card-body">
                    <form action="{{ route('drivers.update', $driver->driver_id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT') <!-- สำคัญสำหรับ Update -->
                        
                        <div class="row">
                            <div class="col-md-12"><h6 class="fw-bold text-primary">ข้อมูลส่วนตัว</h6></div>
                            <div class="border-top my-3"></div>

                            <!-- ส่วนจัดการ Role: Staff, Company, Supply -->
                            <div class="row">
                                @if ($roleString === 'staff' || $roleString === Role::Staff->value)
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">บริษัทฯว่าจ้าง <span class="text-danger">*</span></label>
                                        <select name="company_id" id="company_id" class="form-control select2" required>
                                            <option value="">--- พิมพ์ค้นหาชื่อบริษัท ---</option>
                                            @foreach ($companies as $com)
                                                <option value="{{ $com->company_id }}" {{ $driver->company_id == $com->company_id ? 'selected' : '' }}>
                                                    {{ $com->company_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">เลือก Supply <span class="text-danger">*</span></label>
                                        <select name="supply_id" id="supply_id" class="form-control select2" required>
                                            <option value="">--- รายชื่อ Supply ---</option>
                                            @foreach ($staffSupplies as $sup)
                                                <option value="{{ $sup->sup_id }}" {{ $driver->supply_id == $sup->sup_id ? 'selected' : '' }}>
                                                    {{ $sup->supply_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @elseif($roleString === 'company' || $roleString === Role::Company->value)
                                    <input type="hidden" name="company_id" value="{{ Auth::user()->company_code }}">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">เลือก Supply <span class="text-danger">*</span></label>
                                        <select name="supply_id" id="supply_id" class="form-control select2" required>
                                            <option value="">--- รายชื่อ Supply ---</option>
                                            @foreach ($supplies as $sup)
                                                <option value="{{ $sup->sup_id }}" {{ $driver->supply_id == $sup->sup_id ? 'selected' : '' }}>
                                                    {{ $sup->supply_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @elseif($roleString === 'supply' || $roleString === Role::Supply->value)
                                    <input type="hidden" name="company_id" value="{{ Auth::user()->company_code }}">
                                    <input type="hidden" name="supply_id" value="{{ Auth::user()->supply_user_id }}">
                                @endif
                            </div>

                            <div class="row">
                                <div class="col-md-2 mb-3">
                                    <label class="form-label">คำนำหน้า <span class="text-danger">*</span></label>
                                    <select name="prefix" class="form-control" required>
                                        <option value="นาย" {{ $driver->prefix == 'นาย' ? 'selected' : '' }}>นาย</option>
                                        <option value="นาง" {{ $driver->prefix == 'นาง' ? 'selected' : '' }}>นาง</option>
                                        <option value="นางสาว" {{ $driver->prefix == 'นางสาว' ? 'selected' : '' }}>นางสาว</option>
                                        <option value="พจส." {{ $driver->prefix == 'พจส.' ? 'selected' : '' }}>พจส.</option>
                                    </select>
                                </div>
                                <div class="col-md-5 mb-3">
                                    <label class="form-label">ชื่อ <span class="text-danger">*</span></label>
                                    <input type="text" name="name" value="{{ $driver->name }}" class="form-control" required>
                                </div>
                                <div class="col-md-5 mb-3">
                                    <label class="form-label">นามสกุล <span class="text-danger">*</span></label>
                                    <input type="text" name="lastname" value="{{ $driver->lastname }}" class="form-control" required>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">เลขที่บัตรประชาชน <span class="text-danger">*</span></label>
                                    <input type="text" name="id_card_no" id="id_card_no" value="{{ $driver->id_card_no }}" class="form-control" maxlength="13" required>
                                    <div id="id-card-feedback" class="fs-12 mt-1"></div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">เบอร์โทรศัพท์</label>
                                    <input type="text" name="phone" value="{{ $driver->phone }}" class="form-control">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">สถานะการทำงาน</label>
                                    <select name="driver_status" class="form-control">
                                        <option value="1" {{ $driver->driver_status == 1 ? 'selected' : '' }}>ปกติ</option>
                                        <option value="2" {{ $driver->driver_status == 2 ? 'selected' : '' }}>ลาออก / พักงาน</option>
                                    </select>
                                </div>

                                {{-- รูปภาพประจำตัว --}}
                                <div class="col-md-12 mb-3 mt-2">
                                    <label class="form-label fw-500">รูปภาพประจำตัว</label>
                                    <div class="d-flex align-items-start gap-4 mt-1">
                                        <div class="flex-shrink-0">
                                            <img id="profile-preview-img"
                                                 src="{{ $driver->driver_profile ? Storage::url($driver->driver_profile) : asset('user.png') }}"
                                                 alt="รูปประจำตัว"
                                                 class="rounded border"
                                                 style="width:110px;height:110px;object-fit:cover;cursor:pointer;"
                                                 onclick="document.getElementById('profile-file-input').click()">
                                        </div>
                                        <div>
                                            <p class="text-muted fs-12 mb-2">รองรับ JPG, PNG, WEBP · ขนาดสูงสุด 5 MB<br>คลิกที่รูปหรือกดปุ่มด้านล่างเพื่อเปลี่ยน</p>
                                            <button type="button" class="btn btn-outline-secondary btn-sm"
                                                    onclick="document.getElementById('profile-file-input').click()">
                                                <i class="fa fa-upload me-1"></i> เปลี่ยนรูป
                                            </button>
                                            <input type="file" id="profile-file-input" name="driver_profile"
                                                   accept="image/jpeg,image/png,image/webp" class="d-none">
                                            <div id="profile-feedback" class="fs-12 mt-2"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- ข้อมูลการทำงาน -->
                                <div class="col-md-12 mt-4"><h6 class="fw-bold text-primary">ข้อมูลการทำงานและ ใบขับขี่</h6></div>
                                <div class="border-top my-3"></div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">เลขที่ใบอนุญาตขับขี่</label>
                                    <input type="text" name="driver_license_no" id="driver_license_no" value="{{ $driver->driver_license_no }}" class="form-control">
                                    <div id="license-feedback" class="fs-12 mt-1"></div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">วันที่ใบขับขี่หมดอายุ</label>
                                    <input type="text" class="form-control date-th-input" data-hidden-id="real_license_date" value="{{ $driver->license_expire_date_show }}" placeholder="วว/ดด/ปปปป (พ.ศ.)" maxlength="10">
                                    <input type="hidden" name="license_expire_date" id="real_license_date" value="{{ $driver->license_expire_date }}">
                                    <div class="fs-12 mt-1 date-feedback"></div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">ทะเบียนรถที่ขับประจำ</label>
                                    <input type="text" name="assigned_car_id" value="{{ $driver->assigned_car_id }}" class="form-control">
                                </div>
                             <div class="border-top my-3"></div>

                                {{-- ===== ประวัติการอบรม ===== --}}
                                <div class="col-md-12 mt-2">
                                    <h6 class="fw-bold text-primary">
                                        ข้อมูลการอบรม
                                        <span class="dm-tag tag-primary tag-transparented ms-1">{{ $trainings->count() }} ครั้ง</span>
                                    </h6>
                                </div>
                                <div class="border-top my-3"></div>

                                @if ($trainings->count())
                                <div class="col-md-12 mb-3">
                                    <div class="table-responsive">
                                        <table class="table table-sm mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="fs-14 ps-2">#</th>
                                                    <th class="fs-14">อบรมโดยบริษัท</th>
                                                    <th class="fs-14">วันที่อบรม</th>
                                                    <th class="fs-14">วันหมดอายุ</th>
                                                    <th class="fs-14">สถานะ</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($trainings as $ti => $t)
                                                @php
                                                    $expireDate = \Carbon\Carbon::parse($t->training_expire_date);
                                                    $isExpired  = $expireDate->isPast();
                                                    $isWarn     = !$isExpired && $expireDate->diffInDays(now()) <= 30;
                                                @endphp
                                                <tr>
                                                    <td class="fs-14 ps-2 text-muted">{{ $ti + 1 }}</td>
                                                    <td class="fs-14">{{ $t->training_company }}</td>
                                                    <td class="fs-14">{{ thai_date($t->training_date) }}</td>
                                                    <td class="fs-14">{{ thai_date($t->training_expire_date) }}</td>
                                                    <td class="fs-14">
                                                        @if ($isExpired)
                                                            <span class="dm-tag tag-danger tag-transparented">หมดอายุ</span>
                                                        @elseif ($isWarn)
                                                            <span class="dm-tag tag-warning tag-transparented">ใกล้หมดอายุ</span>
                                                        @else
                                                            <span class="dm-tag tag-success tag-transparented">ปกติ</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                @else
                                <div class="col-md-12 mb-3">
                                    <div class="text-center text-muted fs-14 py-2">
                                        <i class="fa fa-graduation-cap fs-20 d-block mb-1"></i>
                                        ยังไม่มีประวัติการอบรม
                                    </div>
                                </div>
                                @endif

                                {{-- Add new training record --}}
                                <div class="col-md-12 mt-1 mb-2">
                                    <span class="fs-16 fw-500 text-primary">เพิ่มรอบการอบรมใหม่</span>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">อบรมโดยบริษัท</label>
                                    <select name="training_company" class="form-control">
                                        <option value="">-- เลือก --</option>
                                        <option value="ID Driver">ID Driver</option>
                                        <option value="SCCC">SCCC</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">วันที่อบรม</label>
                                    <input type="text" class="form-control date-th-input"
                                        data-hidden-id="real_training_date"
                                        placeholder="วว/ดด/ปปปป (พ.ศ.) เช่น 01/01/2568" maxlength="10">
                                    <input type="hidden" name="training_date" id="real_training_date">
                                    <div class="fs-12 mt-1 date-feedback"></div>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label class="form-label">หมายเหตุเพิ่มเติม</label>
                                    <textarea name="remark" class="form-control" rows="3">{{ $driver->remark }}</textarea>
                                </div>
                            </div>

                            <div class="border-top my-3"></div>
                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <a href="{{ route('drivers.index') }}" class="btn btn-light btn-squared">ยกเลิก</a>
                                <button type="submit" class="btn btn-warning btn-squared fw-bold">อัปเดตข้อมูล</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            </div>{{-- end left col --}}

            {{-- Right column: documents --}}
            <div class="col-md-6">
            <div class="card mb-50">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa fa-files text-muted fs-16"></i>
                        <span class="fw-bold fs-16">เอกสารแนบ</span>
                        <span class="dm-tag tag-primary tag-transparented">{{ $documents->count() }} ไฟล์</span>
                    </div>
                    @if (in_array($roleString, ['staff', 'company']))
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                            data-bs-target="#modalUploadDoc">
                            <i class="fa fa-upload me-1"></i>เพิ่มเอกสาร
                        </button>
                    @endif
                </div>
                <div class="card-body">

                    @if ($documents->count())
                        <div class="fs-16 fw-500 text-muted text-uppercase mb-2" style="letter-spacing:.04em">
                            เอกสารที่อัปโหลดแล้ว
                        </div>
                        @foreach ($documents as $doc)
                            <div class="d-flex align-items-center gap-3 py-2 border-bottom border-light-subtle">
                                <div class="d-flex align-items-center justify-content-center rounded flex-shrink-0"
                                    style="width:34px;height:34px;background:{{ $doc->file_extension === 'pdf' ? '#FCEBEB' : '#E6F1FB' }}">
                                    <i class="fs-16 {{ $doc->file_extension === 'pdf' ? 'lar la-file-pdf' : 'lar la-file-alt' }}"
                                        style="color:{{ $doc->file_extension === 'pdf' ? '#A32D2D' : '#185FA5' }}"
                                        aria-hidden="true"></i>
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="fs-16 text-truncate">{{ $doc->doc_name }}</div>
                                    <div class="text-muted fs-16">
                                        {{ number_format($doc->file_size / 1024, 1) }} KB
                                    </div>
                                </div>
                                <div class="d-flex gap-2 flex-shrink-0">
                                    @if ($doc->file_extension === 'pdf')
                                        <button type="button"
                                            class="btn btn-sm btn-outline-primary btn-view-doc"
                                            data-url="{{ Storage::url($doc->file_path) }}"
                                            data-name="{{ $doc->doc_name }}" title="ดูไฟล์">
                                            <i class="fa fa-eye fs-14"></i>
                                        </button>
                                    @endif
                                    <a href="{{ Storage::url($doc->file_path) }}" download
                                        class="btn btn-sm btn-outline-secondary" title="ดาวน์โหลด">
                                        <i class="fa fa-download fs-14"></i>
                                    </a>
                                    @if (in_array($roleString, ['staff', 'company']))
                                        <form method="POST"
                                            action="{{ route('drivers.documents.destroy', [$driver->driver_id, $doc->id]) }}"
                                            onsubmit="return confirm('ยืนยันการลบเอกสารนี้?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="ลบ">
                                                <i class="fa fa-trash fs-14"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center text-muted fs-16 py-3">
                            <i class="fa fa-file-off fs-24 d-block mb-1"></i>
                            ยังไม่มีเอกสาร
                        </div>
                    @endif

                    {{-- Show missing required document types --}}
                    @php
                        $uploadedTypes = $documents->pluck('doc_type')->toArray();
                        $allTypes = [
                            'medical' => 'ใบรับรองแพทย์',
                            'license' => 'สำเนาใบขับขี่',
                            'id_card' => 'สำเนาบัตรประชาชน',
                            'cert'    => 'Certificate การอบรม',
                        ];
                        $missingTypes = array_diff_key($allTypes, array_flip($uploadedTypes));
                    @endphp
                    @if (count($missingTypes))
                        <div class="mt-3 p-3 rounded" style="background:var(--color-background-secondary)">
                            <div class="fs-16 fw-500 text-muted mb-2">เอกสารที่ยังไม่ได้อัปโหลด</div>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach ($missingTypes as $label)
                                    <span class="dm-tag tag-warning tag-transparented">
                                        <i class="fa fa-exclamation-circle text-warning me-1"></i>{{ $label }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                </div>
            </div>
            </div>{{-- end right col --}}

            </div>{{-- end row --}}
        </div>
    </div>
</div>

{{-- ===== Modal Upload Document (staff and company) ===== --}}
@if (in_array($roleString, ['staff', 'company']))
<div class="modal fade" id="modalUploadDoc" tabindex="-1" aria-labelledby="modalUploadDocLabel">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('drivers.documents.store', $driver->driver_id) }}"
            enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fs-15" id="modalUploadDocLabel">
                        <i class="fa fa-upload me-2"></i>เพิ่มเอกสาร
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fs-16">ประเภทเอกสาร</label>
                        <select name="doc_type" class="form-select form-select-sm" required>
                            <option value="">-- เลือกประเภท --</option>
                            <option value="medical">ใบรับรองแพทย์</option>
                            <option value="license">สำเนาใบขับขี่</option>
                            <option value="id_card">สำเนาบัตรประชาชน</option>
                            <option value="cert">Certificate การอบรม</option>
                            <option value="other">อื่นๆ</option>
                        </select>
                    </div>
                    <div class="mb-3" id="wrap-doc-name" style="display:none">
                        <label class="form-label fs-16">ชื่อเอกสาร</label>
                        <input type="text" name="doc_name" class="form-control form-control-sm"
                            maxlength="200" placeholder="ระบุชื่อเอกสาร">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fs-16">ไฟล์เอกสาร</label>
                        <input type="file" name="doc_file" id="modal-file-input" accept=".pdf,.docx"
                            class="form-control form-control-sm" required>
                        <div class="text-muted fs-16 mt-1">
                            <i class="fa fa-info-circle me-1"></i>รองรับ PDF และ DOCX · สูงสุด 10 MB
                        </div>
                    </div>
                    <div id="modal-preview" class="d-none p-2 rounded"
                        style="background:var(--color-background-secondary)">
                        <div class="d-flex align-items-center gap-2">
                            <i id="modal-file-icon" class="fs-16 flex-shrink-0"></i>
                            <span id="modal-file-name" class="fs-16 text-truncate flex-grow-1"></span>
                            <span id="modal-file-size" class="fs-16 text-muted flex-shrink-0"></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm"
                        data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fa fa-upload me-1"></i>อัปโหลด
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endif

{{-- ===== Modal View Document (PDF) ===== --}}
<div class="modal fade" id="modalViewDoc" tabindex="-1" aria-labelledby="modalViewDocLabel">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content" style="height:90vh;">
            <div class="modal-header py-2">
                <h6 class="modal-title fs-15" id="modalViewDocLabel">
                    <i class="fa fa-file-pdf me-2 text-danger"></i>
                    <span id="viewDocName"></span>
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0 flex-grow-1" style="overflow:hidden;">
                <iframe id="viewDocFrame" src=""
                    style="width:100%;height:100%;border:none;display:block;"></iframe>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
(function() {
    const MAX = 10 * 1024 * 1024;
    const EXT = /\.(pdf|docx)$/i;

    // Toggle doc-name field for "other" type
    var docTypeSelect = document.querySelector('select[name="doc_type"]');
    if (docTypeSelect) {
        docTypeSelect.addEventListener('change', function() {
            var wrap = document.getElementById('wrap-doc-name');
            var nameInput = document.querySelector('input[name="doc_name"]');
            wrap.style.display = this.value === 'other' ? 'block' : 'none';
            nameInput.required = this.value === 'other';
        });
    }

    // File preview in upload modal
    var modalFileInput = document.getElementById('modal-file-input');
    if (modalFileInput) {
        modalFileInput.addEventListener('change', function() {
            var file = this.files[0];
            var preview = document.getElementById('modal-preview');
            if (!file) { preview.classList.add('d-none'); return; }

            if (!EXT.test(file.name)) {
                Swal.fire('ประเภทไฟล์ไม่ถูกต้อง', 'รองรับเฉพาะ PDF และ DOCX เท่านั้น', 'warning');
                this.value = ''; preview.classList.add('d-none'); return;
            }
            if (file.size > MAX) {
                Swal.fire('ไฟล์ใหญ่เกินไป', 'ขนาดไฟล์ต้องไม่เกิน 10 MB', 'warning');
                this.value = ''; preview.classList.add('d-none'); return;
            }

            var icon = document.getElementById('modal-file-icon');
            icon.className = (/\.pdf$/i.test(file.name) ? 'fa fa-file-type-pdf' : 'fa fa-file-type-doc') + ' fs-16 flex-shrink-0';
            icon.style.color = /\.pdf$/i.test(file.name) ? '#A32D2D' : '#185FA5';
            document.getElementById('modal-file-name').textContent = file.name;
            document.getElementById('modal-file-size').textContent =
                file.size < 1048576 ? (file.size / 1024).toFixed(1) + ' KB' : (file.size / 1048576).toFixed(1) + ' MB';
            preview.classList.remove('d-none');
        });
    }

    // Open PDF preview modal
    document.querySelectorAll('.btn-view-doc').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.getElementById('viewDocName').textContent = this.dataset.name;
            document.getElementById('viewDocFrame').src = this.dataset.url;
            new bootstrap.Modal(document.getElementById('modalViewDoc')).show();
        });
    });

    // Clear iframe on modal close to stop PDF rendering
    document.getElementById('modalViewDoc').addEventListener('hidden.bs.modal', function() {
        document.getElementById('viewDocFrame').src = '';
    });
})();
</script>

<!-- สคริปต์จัดการวันที่ พ.ศ. (เหมือนหน้า Create เป๊ะๆ) -->
<script>
$(document).ready(function() {
    $('.date-th-input').on('input', function(e) {
        let input = $(this).val().replace(/[^0-9]/g, ''); 
        let formattedDate = '';
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
        
        let feedback = $(this).siblings('.date-feedback');
        let hiddenInputId = $(this).data('hidden-id');
        let hiddenInput = $('#' + hiddenInputId);

        if (formattedDate.length === 10) {
            let parts = formattedDate.split('/');
            let day = parts[0];
            let month = parts[1];
            let yearBE = parseInt(parts[2]);

            if(yearBE > 2500) {
                let yearCE = yearBE - 543;
                let finalDateForDB = yearCE + '-' + month + '-' + day;
                hiddenInput.val(finalDateForDB);
                feedback.html('<span class="text-success"><i class="uil uil-check-circle"></i> รูปแบบวันที่ถูกต้อง</span>');
                $(this).addClass('is-valid').removeClass('is-invalid');
            } else {
                hiddenInput.val('');
                feedback.html('<span class="text-danger"><i class="uil uil-times-circle"></i> ใส่ปี พ.ศ. ให้ถูกต้อง</span>');
                $(this).addClass('is-invalid').removeClass('is-valid');
            }
        } else {
            // สำคัญ! ในหน้า Edit ถ้าผู้ใช้ลบวันที่จนหมด เราก็ลบค่าใน hidden ทิ้ง เพื่อให้ DB อัปเดตเป็น null ได้
            hiddenInput.val(''); 
            feedback.html('');
            $(this).removeClass('is-valid is-invalid');
        }

        if($('.is-invalid').length > 0) {
            $('button[type="submit"]').attr('disabled', true);
        } else {
            $('button[type="submit"]').attr('disabled', false);
        }
    });
});
</script>

<!-- สคริปต์ Select2 และการเช็คซ้ำ -->
<script>
$(document).ready(function() {
    $('.select2').select2({ theme: "bootstrap-5", width: '100%' });

    $('#company_id').on('change', function() {
        let comId = $(this).val();
        let supplySelect = $('#supply_id');
        supplySelect.empty().append('<option value="">--- กำลังโหลดข้อมูล ---</option>').trigger('change');

        if (comId) {
            $.ajax({
                url: "{{ route('drivers.getSupplies') }}",
                method: "GET",
                data: { company_id: comId },
                success: function(data) {
                    supplySelect.empty().append('<option value="">--- เลือกหน่วยงานย่อย ---</option>');
                    $.each(data, function(key, val) {
                        supplySelect.append('<option value="' + val.sup_id + '">' + val.supply_name + '</option>');
                    });
                    supplySelect.trigger('change');
                }
            });
        } else {
            supplySelect.empty().append('<option value="">--- เลือกหน่วยงานย่อย ---</option>').trigger('change');
        }
    });

    // แนบ ID ปัจจุบันไปกับการเช็คข้อมูลซ้ำด้วย
    let currentDriverId = "{{ $driver->id }}"; 

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
                value: val,
                ignore_id: currentDriverId // ส่ง ID ตัวเองไปยกเว้น
            },
            beforeSend: function() { feedbackElement.html('<span class="text-info">กำลังตรวจสอบ...</span>'); },
            success: function(response) {
                if (response.exists) {
                    feedbackElement.html('<span class="text-danger"><i class="uil uil-times-circle"></i> ข้อมูลนี้มีในระบบแล้ว</span>');
                    inputElement.addClass('is-invalid').removeClass('is-valid');
                    checkOverallStatus();
                } else {
                    feedbackElement.html('<span class="text-success"><i class="uil uil-check-circle"></i> สามารถใช้งานได้</span>');
                    inputElement.addClass('is-valid').removeClass('is-invalid');
                    checkOverallStatus();
                }
            }
        });
    }

    function checkOverallStatus() {
        if ($('.is-invalid').length > 0) {
            $('button[type="submit"]').attr('disabled', true);
        } else {
            $('button[type="submit"]').attr('disabled', false);
        }
    }

    $('#id_card_no').on('blur', function() {
        checkDuplicate($(this), $('#id-card-feedback'), 'id_card_no');
    });

    $('#driver_license_no').on('blur', function() {
        checkDuplicate($(this), $('#license-feedback'), 'driver_license_no');
    });
});
</script>
<script>
(function() {
    var fileInput  = document.getElementById('profile-file-input');
    var previewImg = document.getElementById('profile-preview-img');
    var feedback   = document.getElementById('profile-feedback');
    var MAX_IMG    = 5 * 1024 * 1024;

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
            feedback.innerHTML = '<span class="text-success"><i class="fa fa-check-circle"></i> พร้อมอัปโหลด — กด "อัปเดตข้อมูล" เพื่อบันทึก</span>';
        };
        reader.readAsDataURL(file);
    });
})();
</script>
@endpush