{{-- ============================================ --}}
{{-- Preview Modal - Show data before submit       --}}
{{-- Triggered by: $('#btnPreview').click()        --}}
{{-- ============================================ --}}

<style>
    /* Modal styling */
    #previewModal .modal-dialog {
        max-width: 850px;
    }

    #previewModal .modal-header {
        background: linear-gradient(135deg, #5840ff 0%, #7c5cff 100%);
        color: white;
        border-bottom: none;
        border-radius: 8px 8px 0 0;
    }

    #previewModal .modal-header .btn-close {
        filter: brightness(0) invert(1);
    }

    #previewModal .modal-title {
        font-weight: 600;
        font-size: 18px;
    }

    #previewModal .preview-section {
        margin-bottom: 20px;
    }

    #previewModal .preview-section-title {
        font-size: 14px;
        font-weight: 600;
        color: #5840ff;
        padding-bottom: 8px;
        border-bottom: 2px solid #f0f4ff;
        margin-bottom: 15px;
    }

    #previewModal .preview-row {
        display: flex;
        padding: 8px 0;
        border-bottom: 1px dashed #e3e6ef;
    }

    #previewModal .preview-row:last-child {
        border-bottom: none;
    }

    #previewModal .preview-label {
        flex: 0 0 40%;
        color: #64748b;
        font-size: 14px;
    }

    #previewModal .preview-value {
        flex: 1;
        color: #404659;
        font-weight: 500;
        font-size: 14px;
        word-break: break-word;
    }

    #previewModal .preview-value.highlight {
        color: #5840ff;
        font-weight: 600;
    }

    #previewModal .preview-image-wrap {
        text-align: center;
        background: #f8f9fb;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
    }

    #previewModal .preview-image-wrap img {
        max-width: 100%;
        max-height: 250px;
        border-radius: 6px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    #previewModal .preview-doc-card {
        background: #fef9e7;
        border-left: 4px solid #f5c842;
        padding: 12px 16px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    #previewModal .preview-doc-card i {
        font-size: 32px;
        color: #f5c842;
    }

    #previewModal .preview-doc-info {
        flex: 1;
    }

    #previewModal .preview-doc-filename {
        font-weight: 600;
        color: #404659;
        font-size: 14px;
    }

    #previewModal .preview-doc-name {
        font-size: 13px;
        color: #64748b;
    }

    #previewModal .modal-footer {
        background: #f8f9fb;
        border-top: 1px solid #e3e6ef;
        border-radius: 0 0 8px 8px;
    }

    #previewModal .alert-warning-soft {
        background: #fff7ed;
        border-left: 4px solid #f59e0b;
        color: #92400e;
        padding: 12px 16px;
        border-radius: 6px;
        margin-bottom: 20px;
        font-size: 14px;
    }
</style>

<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">

            {{-- ============================================ --}}
            {{-- Modal Header                                   --}}
            {{-- ============================================ --}}
            <div class="modal-header">
                <h5 class="modal-title text-white" id="previewModalLabel">
                    <i class="uil uil-file-search-alt"></i> ตรวจสอบข้อมูลก่อนบันทึก
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            {{-- ============================================ --}}
            {{-- Modal Body                                     --}}
            {{-- ============================================ --}}
            <div class="modal-body">

                {{-- Warning notice --}}
                <div class="alert-warning-soft">
                    <i class="uil uil-exclamation-triangle"></i>
                    <strong>กรุณาตรวจสอบข้อมูลให้ถูกต้องก่อนกดยืนยัน</strong>
                    หลังจากบันทึกแล้วจะสามารถแก้ไขได้ภายหลังในหน้าจัดการรถ
                </div>

                <div class="row" id="prev_layout_row">
                    {{-- Left column: Vehicle image + document --}}
                    <div class="col-md-5" id="prev_left_col">
                        <div id="prev_image_box" style="display:none;">
                            <div class="preview-section-title">ภาพถ่ายหน้ารถ</div>
                            <div class="preview-image-wrap">
                                <img id="prev_image" src="" alt="Vehicle Preview">
                            </div>
                        </div>

                        {{-- Document info --}}
                        <div id="prev_doc_box" style="display:none;">
                            <div class="preview-section-title">เอกสารแนบ</div>
                            <div class="preview-doc-card">
                                <i class="uil uil-file-alt"></i>
                                <div class="preview-doc-info">
                                    <div class="preview-doc-filename" id="prev_doc_filename">-</div>
                                    <div class="preview-doc-name">ชื่อเอกสาร: <span id="prev_doc_name">-</span></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Right column: Data --}}
                    <div class="col-md-7" id="prev_right_col">
                        {{-- ... ส่วนข้อมูลทั้งหมด (ไม่เปลี่ยน) ... --}}

                        {{-- Right column: Data --}}
                        <div class="col-md-7">

                            {{-- Section 1: Owner --}}
                            <div class="preview-section">
                                <div class="preview-section-title">ข้อมูลเจ้าของรถ</div>
                                <div class="preview-row">
                                    <div class="preview-label">บริษัท</div>
                                    <div class="preview-value highlight" id="prev_company">-</div>
                                </div>
                                <div class="preview-row">
                                    <div class="preview-label">Supply</div>
                                    <div class="preview-value highlight" id="prev_supply">-</div>
                                </div>
                            </div>

                            {{-- Section 2: Vehicle main info --}}
                            <div class="preview-section">
                                <div class="preview-section-title">ข้อมูลรถ</div>
                                <div class="preview-row">
                                    <div class="preview-label">ทะเบียนรถ</div>
                                    <div class="preview-value highlight" id="prev_car_plate">-</div>
                                </div>
                                <div class="preview-row">
                                    <div class="preview-label">ยี่ห้อ</div>
                                    <div class="preview-value" id="prev_car_brand">-</div>
                                </div>
                                <div class="preview-row">
                                    <div class="preview-label">ประเภทรถ</div>
                                    <div class="preview-value" id="prev_car_type">-</div>
                                </div>
                                <div class="preview-row">
                                    <div class="preview-label">รุ่นรถ</div>
                                    <div class="preview-value" id="prev_car_model">-</div>
                                </div>
                                <div class="preview-row">
                                    <div class="preview-label">หมายเลขรถ</div>
                                    <div class="preview-value" id="prev_car_number_record">-</div>
                                </div>
                                <div class="preview-row">
                                    <div class="preview-label">ทะเบียนหาง</div>
                                    <div class="preview-value" id="prev_car_trailer_plate">-</div>
                                </div>
                            </div>

                            {{-- Section 3: Specs --}}
                            <div class="preview-section">
                                <div class="preview-section-title">ข้อมูลเทคนิค</div>
                                <div class="preview-row">
                                    <div class="preview-label">ปีจดทะเบียน (พ.ศ.)</div>
                                    <div class="preview-value" id="prev_car_age">-</div>
                                </div>
                                <div class="preview-row">
                                    <div class="preview-label">เลขไมล์</div>
                                    <div class="preview-value" id="prev_car_mileage">-</div>
                                </div>
                                <div class="preview-row">
                                    <div class="preview-label">น้ำหนักรถเปล่า</div>
                                    <div class="preview-value" id="prev_car_weight">-</div>
                                </div>
                                <div class="preview-row">
                                    <div class="preview-label">น้ำหนักรถรวม</div>
                                    <div class="preview-value" id="prev_car_total_weight">-</div>
                                </div>
                                <div class="preview-row">
                                    <div class="preview-label">เชื้อเพลิง</div>
                                    <div class="preview-value" id="prev_car_fuel_type">-</div>
                                </div>
                                <div class="preview-row">
                                    <div class="preview-label">ชนิดสินค้า</div>
                                    <div class="preview-value" id="prev_car_product">-</div>
                                </div>
                            </div>

                            {{-- Section 4: Dates --}}
                            <div class="preview-section">
                                <div class="preview-section-title">ข้อมูลวันที่</div>
                                <div class="preview-row">
                                    <div class="preview-label">วันจดทะเบียน</div>
                                    <div class="preview-value" id="prev_car_register_date">-</div>
                                </div>
                                <div class="preview-row">
                                    <div class="preview-label">วันหมดอายุภาษี</div>
                                    <div class="preview-value" id="prev_car_tax">-</div>
                                </div>
                                <div class="preview-row">
                                    <div class="preview-label">วันประกันหมดอายุ</div>
                                    <div class="preview-value" id="prev_car_insurance_expire">-</div>
                                </div>
                            </div>

                            {{-- Section 5: Insurance + Status --}}
                            <div class="preview-section">
                                <div class="preview-section-title">สถานะ</div>
                                <div class="preview-row">
                                    <div class="preview-label">บริษัทประกัน</div>
                                    <div class="preview-value" id="prev_car_insure">-</div>
                                </div>
                                <div class="preview-row">
                                    <div class="preview-label">สถานะใช้งาน</div>
                                    <div class="preview-value highlight" id="prev_status">-</div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>

                {{-- ============================================ --}}
                {{-- Modal Footer                                   --}}
                {{-- ============================================ --}}
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="uil uil-arrow-left"></i> ย้อนกลับไปแก้ไข
                    </button>
                    <button type="button" id="btnConfirmSubmit" class="btn btn-success">
                        <i class="uil uil-check-circle"></i> ยืนยันบันทึกข้อมูล
                    </button>
                </div>

            </div>
        </div>
    </div>
