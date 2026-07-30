@section('title', 'ลงทะเบียนรถใหม่')
@section('description', 'ID Drives')
@extends('layout.app')

@push('styles')
    {{-- Step indicator styles --}}
    <style>
        .step-wizard {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            position: relative;
        }

        .step-wizard::before {
            content: '';
            position: absolute;
            top: 22px;
            left: 0;
            right: 0;
            height: 3px;
            background: #e2e8f0;
            z-index: 1;
        }

        .step-wizard .step {
            position: relative;
            z-index: 2;
            text-align: center;
            flex: 1;
        }

        .step-wizard .step-circle {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: #e2e8f0;
            color: #64748b;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 18px;
            margin-bottom: 10px;
            transition: all 0.3s;
        }

        .step-wizard .step.active .step-circle {
            background: #5840ff;
            color: white;
        }

        .step-wizard .step.completed .step-circle {
            background: #20c997;
            color: white;
        }

        .step-wizard .step-label {
            font-size: 14px;
            color: #64748b;
            font-weight: 500;
        }

        .step-wizard .step.active .step-label {
            color: #5840ff;
            font-weight: 600;
        }

        .step-content {
            display: none;
        }

        .step-content.active {
            display: block;
        }

        .date-feedback {
            font-size: 13px;
            margin-top: 4px;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb-main">
                    <span class="fs-24 fw-bold breadcrumb-title">ลงทะเบียนรถใหม่</span>
                    <div class="breadcrumb-action justify-content-center flex-wrap">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('vehicles.index') }}">รายการรถ</a></li>
                                <li class="breadcrumb-item active" aria-current="page">ลงทะเบียนรถใหม่</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-sm mb-25">
                    <div class="card-body">

                        {{-- Flash messages --}}
                        @if (session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- ============================================ --}}
                        {{-- STEP WIZARD INDICATOR                          --}}
                        {{-- ============================================ --}}
                        <div class="step-wizard">
                            <div class="step active" data-step="1">
                                <div class="step-circle">1</div>
                                <div class="step-label">เลือกบริษัท</div>
                            </div>
                            <div class="step" data-step="2">
                                <div class="step-circle">2</div>
                                <div class="step-label">เลือก Supply</div>
                            </div>
                            <div class="step" data-step="3">
                                <div class="step-circle">3</div>
                                <div class="step-label">ข้อมูลรถ</div>
                            </div>
                        </div>

                        {{-- ============================================ --}}
                        {{-- MAIN FORM                                      --}}
                        {{-- ============================================ --}}
                        <form action="{{ route('vehicles.store') }}" method="POST" enctype="multipart/form-data"
                            id="vehicleForm">
                            @csrf

                            {{-- ============================================ --}}
                            {{-- STEP 1: SELECT COMPANY                         --}}
                            {{-- ============================================ --}}
                            <div class="step-content active" data-step-content="1">
                                <h5 class="mb-3">ขั้นตอนที่ 1: เลือกบริษัทฯว่าจ้าง</h5>

                                <div class="mb-3">
                                    <label class="form-label">บริษัทฯว่าจ้าง <span class="text-danger">*</span></label>
                                    <select name="company_id" id="company_id" class="form-control" required>
                                        <option value="" selected disabled>-- กรุณาเลือกบริษัท --</option>
                                        @foreach ($companies as $company)
                                            <option value="{{ $company->company_id }}">
                                                {{ $company->company_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="d-flex justify-content-end mt-4">
                                    <button type="button" class="btn btn-primary btn-next" data-next-step="2">
                                        ถัดไป <i class="uil uil-arrow-right"></i>
                                    </button>
                                </div>
                            </div>

                            {{-- ============================================ --}}
                            {{-- STEP 2: SELECT SUPPLY                          --}}
                            {{-- ============================================ --}}
                            <div class="step-content" data-step-content="2">
                                <h5 class="mb-3">ขั้นตอนที่ 2: เลือกบริษัท Supply</h5>

                                <div class="mb-3">
                                    <label class="form-label">บริษัทฯว่าจ้าง ที่เลือก:</label>
                                    <div class="fw-bold text-primary fs-16" id="selected_company_name">-</div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Supply <span class="text-danger">*</span></label>
                                    <select name="supply_id" id="supply_id" class="form-control" required>
                                        <option value="" selected disabled>-- กรุณาเลือก Supply --</option>
                                    </select>
                                </div>




                                <div class="d-flex justify-content-between mt-4">
                                    <button type="button" class="btn btn-outline-secondary btn-prev" data-prev-step="1">
                                        <i class="uil uil-arrow-left"></i> ย้อนกลับ
                                    </button>
                                    <button type="button" class="btn btn-primary btn-next" data-next-step="3"
                                        id="btn_to_step3" disabled>
                                        ถัดไป <i class="uil uil-arrow-right"></i>
                                    </button>
                                </div>
                            </div>

                            {{-- ============================================ --}}
                            {{-- STEP 3: VEHICLE DETAILS                        --}}
                            {{-- ============================================ --}}
                            <div class="step-content" data-step-content="3">
                                <h5 class="mb-3">ขั้นตอนที่ 3: ข้อมูลรถ</h5>

                                {{-- Display selected company + supply --}}
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <small class="text-muted">บริษัท</small>
                                        <div class="fw-bold text-primary" id="step3_company_name">-</div>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted">Supply</small>
                                        <div class="fw-bold text-primary" id="step3_supply_name">-</div>
                                    </div>
                                </div>

                                <div class="border-top my-3"></div>

                                {{-- License Plate + Province --}}
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">ทะเบียนรถ <span class="text-danger">*</span></label>
                                        <input type="text" name="plate" id="plate" class="form-control"
                                            maxlength="20" placeholder="เช่น 1กก-1234" required>
                                        <div id="plate_feedback" class="mt-1"></div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">จังหวัดทะเบียน <span
                                                class="text-danger">*</span></label>
                                        <select name="province" id="province" class="form-control" required>
                                            <option value="" selected disabled>-- กรุณาเลือกจังหวัด --</option>
                                            @foreach ($provinces as $prov)
                                                <option value="{{ $prov->name_th }}">{{ $prov->name_th }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                {{-- Brand + Vehicle Type --}}
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">ยี่ห้อรถ <span class="text-danger">*</span></label>
                                        <select name="car_brand" id="car_brand" class="form-control" required>
                                            <option value="" selected disabled>-- เลือกยี่ห้อรถ --</option>
                                            @foreach ($car_brands as $brand)
                                                <option value="{{ $brand->brand_name }}">{{ $brand->brand_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">ประเภทรถ <span class="text-danger">*</span></label>
                                        <select name="car_type" id="car_type" class="form-control" required>
                                            <option value="" selected disabled>-- เลือกประเภทรถ --</option>
                                            @foreach ($vehicle_types as $type)
                                                <option value="{{ $type->id }}">{{ $type->vehicle_type }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                {{-- Model + Number Record --}}
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">รุ่นรถ <span class="text-danger">*</span></label>
                                        <input type="text" name="car_model" class="form-control"
                                            placeholder="เช่น Victor500, FXZ320 (ถ้าไม่มีใส่ -)" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">หมายเลขรถ</label>
                                        <input type="text" name="car_number_record" class="form-control"
                                            placeholder="เลขประจำตัวรถ (ถ้ามี)">
                                    </div>
                                </div>

                                {{-- Age + Mileage --}}
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">ปีที่จดทะเบียนครั้งแรก</label>
                                        <input type="text" name="car_age" class="form-control"
                                            placeholder="ระบุปี พ.ศ. เช่น 2560 (ถ้าไม่มีใส่ -)">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">เลขไมล์</label>
                                        <input type="text" name="car_mileage" class="form-control"
                                            placeholder="เลขไมล์ ณ วันที่บันทึก">
                                    </div>
                                </div>

                                {{-- Trailer Plate + Fuel Type --}}
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">ทะเบียนหาง</label>
                                        <input type="text" name="car_trailer_plate" class="form-control"
                                            placeholder="ระบุทะเบียนหาง (ถ้ามี)">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">ชนิดเชื้อเพลิง</label>
                                        <input type="text" name="car_fuel_type" class="form-control"
                                            placeholder="เช่น น้ำมัน, NGV, LNG">
                                    </div>
                                </div>

                                {{-- Weight (empty + total) --}}
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">น้ำหนักรถเปล่าหัว+หาง (T)</label>
                                        <input type="text" name="car_weight" class="form-control"
                                            >
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">น้ำหนักรวมสูงสุด (T)</label>
                                        <input type="text" name="car_total_weight" class="form-control"
                                            >
                                    </div>
                                </div>

                                {{-- Product type + Insurance company --}}
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">ชนิดสินค้าที่บรรทุก</label>
                                        <input type="text" name="car_product" class="form-control"
                                            placeholder="เช่น ปูนถุง, ปูนเม็ด, ปูนผง">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">บริษัทประกันภัย</label>
                                        <select name="car_insurance_id" class="form-select">
                                            <option value="">-- ไม่ระบุ --</option>
                                            @foreach ($insuranceCompanies as $ins)
                                                <option value="{{ $ins->id }}" {{ old('car_insurance_id') == $ins->id ? 'selected' : '' }}>
                                                    {{ $ins->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="border-top my-3"></div>

                                {{-- Date fields (Buddhist Era input) --}}
                                <h6 class="mb-3 fw-bold">ข้อมูลวันที่ (กรอกเป็น พ.ศ.)</h6>

                                <div class="row">
                                    {{-- Tax expire date --}}
                                    <div class="col-12 col-md-6 mb-3">
                                        <label class="form-label">วันหมดอายุภาษี</label>
                                        <input type="text" class="form-control date-th-input"
                                            data-hidden-id="real_car_tax" placeholder="วว/ดด/ปปปป (พ.ศ.)"
                                            inputmode="numeric" maxlength="10">
                                        <div class="date-feedback"></div>
                                        <input type="hidden" name="car_tax" id="real_car_tax">
                                    </div>

                                    {{-- Register date --}}
                                    <div class="col-12 col-md-6 mb-3">
                                        <label class="form-label">วันที่จดทะเบียน</label>
                                        <input type="text" class="form-control date-th-input"
                                            data-hidden-id="real_car_register_date" placeholder="วว/ดด/ปปปป (พ.ศ.)"
                                            inputmode="numeric" maxlength="10">
                                        <div class="date-feedback"></div>
                                        <input type="hidden" name="car_register_date" id="real_car_register_date">
                                    </div>
                                </div>

                                <div class="row">
                                    {{-- Insurance start date --}}
                                    <div class="col-12 col-md-6 mb-3">
                                        <label class="form-label">วันที่เริ่มประกัน</label>
                                        <input type="text" class="form-control date-th-input"
                                            data-hidden-id="real_car_insurance_start" placeholder="วว/ดด/ปปปป (พ.ศ.)"
                                            inputmode="numeric" maxlength="10">
                                        <div class="date-feedback"></div>
                                        <input type="hidden" name="car_insurance_start" id="real_car_insurance_start">
                                    </div>
                                    {{-- Insurance expire date --}}
                                    <div class="col-12 col-md-6 mb-3">
                                        <label class="form-label">วันที่ประกันหมดอายุ</label>
                                        <input type="text" class="form-control date-th-input"
                                            data-hidden-id="real_car_insurance_expire" placeholder="วว/ดด/ปปปป (พ.ศ.)"
                                            inputmode="numeric" maxlength="10">
                                        <div class="date-feedback"></div>
                                        <input type="hidden" name="car_insurance_expire" id="real_car_insurance_expire">
                                    </div>
                                </div>

                                <div class="border-top my-3"></div>

                                {{-- Status --}}
                                <div class="mb-3">
                                    <label class="form-label fw-bold">สถานะการใช้งาน <span class="text-danger">*</span></label>
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="status" value="0">
                                        <input class="form-check-input" type="checkbox" id="status" name="status"
                                            value="1" checked>
                                        <label class="form-check-label" for="status">เปิดใช้งาน (Active)</label>
                                    </div>
                                </div>

                                {{-- Vehicle image upload --}}
                                <div class="mb-3">
                                    <label class="form-label fw-bold">ภาพถ่ายหน้ารถ</label>
                                    <input type="file" name="vehicle_image" id="vehicle_image" class="form-control"
                                        accept="image/*">
                                    <small class="text-muted">ขนาดไฟล์ไม่เกิน 5 MB (jpg, png)</small>
                                    <div class="mt-2">
                                        <img id="vehicle_preview"
                                            style="max-width: 250px; display: none; border-radius: 6px;">
                                    </div>
                                </div>
<div class="border-top my-3"></div>
                                {{-- Document upload (PDF/DOCX) --}}
                                <div class="mb-3">
                                    <label class="form-label fw-bold">เอกสารประกอบ (ถ้ามี)</label>
                                    <input type="file" name="vehicle_document" id="vehicle_document"
                                        class="form-control"
                                        accept=".pdf,.docx,application/pdf,application/vnd.openxmlformats-officedocument.wordprocessingml.document">
                                    <small class="text-muted">
                                        PDF หรือ .docx เท่านั้น (สูงสุด 10 MB) — รวมเอกสารประกัน, สำเนาทะเบียนรถ ฯลฯ
                                    </small>
                                    <div id="document_info" class="mt-2" style="display:none;">
                                        <span class="dm-tag tag-info tag-transparented" id="document_filename"></span>
                                        <span class="text-muted ms-2" id="document_filesize"></span>
                                    </div>
                                </div>

                                {{-- Document name (only show if document uploaded) --}}
                                <div class="mb-3" id="doc_name_box" style="display:none;">
                                    <label class="form-label">ชื่อเอกสาร</label>
                                    <input type="text" name="doc_name" class="form-control"
                                        placeholder="เช่น เอกสารประจำปี 2568">
                                </div>

                                <div class="border-top my-3"></div>

                                <div class="d-flex justify-content-between mt-4">
                                    <button type="button" class="btn btn-outline-secondary btn-prev" data-prev-step="2">
                                        <i class="uil uil-arrow-left"></i> ย้อนกลับ
                                    </button>
                                    <button type="button" id="btnPreview" class="btn btn-success">
                                        <i class="uil uil-eye"></i> ดูตัวอย่างก่อนบันทึก
                                    </button>
                                </div>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Preview Modal (Part 3.3) --}}
    @include('pages.vehicles.partials._preview_modal')

@endsection

{{-- ============================================ --}}
{{-- JAVASCRIPT (Part 3.2)                          --}}
{{-- ============================================ --}}
@push('scripts')
    @include('pages.vehicles.partials._create_scripts')
@endpush
