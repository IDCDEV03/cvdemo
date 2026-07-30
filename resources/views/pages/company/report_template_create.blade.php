@section('title', 'สร้างแบบรายงาน (Report Template)')
@section('description', 'ID Drives')
@extends('layout.app')
@section('content')
    <div class="container-fluid">
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card mb-25">
                    <div class="card-header">
                        <h6 class="card-title">สร้างแบบรายงาน (Report Template)</h6>
                    </div>
                    <div class="card-body">
                        @if (session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif
                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <form action="{{ route('company.report_template.store') }}" method="POST">
                            @csrf

                            <div class="form-group row mb-4">
                                <label class="col-sm-2 col-form-label color-dark">ชื่อแบบ <span
                                        class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <input type="text" name="template_name"
                                        class="form-control ih-medium ip-gray radius-xs" required
                                        placeholder="เช่น แบบรายงานแบบตรวจสมรรถนะของรถตักและรถขุด">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <span class="fw-bold mb-3 text-primary">1. ออกแบบรายงาน </span>
                                    <p class="text-muted small">นำตัวแปร
                                        จากฝั่งขวามาใส่ในช่องว่างที่คุณต้องการให้ระบบเติมข้อมูลอัตโนมัติ</p>
 <div class="border-top my-3"></div>
                                    <div class="form-group mb-4">
                                        <label class="form-label font-weight-bold">หัวกระดาษ (Header)</label>
                                        <textarea name="header_html" id="header_html" class="form-control" rows="8"></textarea>
                                    </div>

                                    <div class="form-group mb-4">
                                        <label class="form-label font-weight-bold">ท้ายกระดาษ (Footer)</label>
                                        <textarea name="footer_html" id="footer_html" class="form-control" rows="5"></textarea>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="p-3 rounded border h-100">
                                        <h6 class="mb-3 text-primary"><i class="fas fa-info-circle"></i> ตัวแปรระบบที่มีให้
                                        </h6>
                                        <p class="text-muted small mb-2">คัดลอกรหัสตัวแปรด้านล่าง
                                            ไปวางในจุดที่คุณต้องการให้ระบบเติมข้อมูลในรายงาน</p>

                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered table-hover mb-0 small">
                                                <thead class="bg-warning text-center">
                                                    <tr>
                                                        <th width="55%">รหัสตัวแปร</th>
                                                        <th width="45%">ข้อมูลที่จะแสดง</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr class="table-secondary">
                                                        <td colspan="2" class="fw-bold text-dark px-2">ข้อมูลทั่วไป
                                                            </td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>[company_name]</code></td>
                                                        <td class="text-muted">ชื่อบริษัท</td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>[inspector_name]</code></td>
                                                        <td class="text-muted">ชื่อผู้ตรวจ</td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>[inspect_date]</code></td>
                                                        <td class="text-muted">วันที่ตรวจ</td>
                                                    </tr>

                                                    <tr class="table-secondary">
                                                        <td colspan="2" class="fw-bold text-dark px-2">ข้อมูลรถ </td>
                                                    </tr>                                                   
                                                    <tr>
                                                        <td><code>[car_plate]</code></td>
                                                        <td class="text-muted">ทะเบียนรถ</td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>[car_brand]</code></td>
                                                        <td class="text-muted">ยี่ห้อรถ</td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>[car_model]</code></td>
                                                        <td class="text-muted">รุ่นรถ</td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>[car_number_record]</code></td>
                                                        <td class="text-muted">เลขประจำตัวรถ</td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>[car_age]</code></td>
                                                        <td class="text-muted">อายุการใช้งาน</td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>[car_tax]</code></td>
                                                        <td class="text-muted">วันหมดอายุภาษี</td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>[car_mileage]</code></td>
                                                        <td class="text-muted">เลขไมล์</td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>[car_insure]</code></td>
                                                        <td class="text-muted">ประกันภัย</td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>[car_type]</code></td>
                                                        <td class="text-muted">ประเภทรถ</td>
                                                    </tr>
                                                </tbody>

                                            </table>
                                        </div>

                                    </div>
                                </div>
                            </div>

                           <div class="border-top my-3"></div>

                            <div class="row">
                                <div class="col-md-12">
                                    <h6 class="mb-3 text-primary"> 2.
                                        เพิ่มข้อมูลก่อนเริ่มงานเฉพาะแบบฟอร์มนี้ </h6>
                                    <p class="text-muted small">หากต้องการข้อมูลที่ระบบไม่มี (เช่น อายุคนขับ, ประสบการณ์ผู้ขับขี่, เชื้อเพลิง, น้ำหนักรถ)
                                        ให้สร้างฟิลด์ด้านล่าง แล้วนำ รหัสตัวแปร ไปวางในช่องออกแบบรายงานด้านบน</p>

                                    <div id="fields_container">
                                    </div>

                                    <button type="button" id="add_field_btn" class="btn btn-info btn-sm mt-2">
                                        <i class="fas fa-plus"></i> เพิ่มฟิลด์ข้อมูล
                                    </button>
                                </div>
                            </div>
 <div class="border-top my-3"></div>
                            <div class="layout-button mt-4">
                                <button type="submit"
                                    class="btn btn-primary btn-default btn-squared">บันทึกแบบรายงาน</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    <script>
        $(document).ready(function() {
            var editorSettings = {
                height: 250, // ความสูงเริ่มต้น
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['font', ['strikethrough', 'superscript', 'subscript']],
                    ['fontsize', ['fontsize']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']], // ปุ่มสำคัญ! สำหรับให้ User วาดตาราง
                    ['insert', ['picture', 'link']], // ใส่รูปหรือลิงก์
                    ['view', ['fullscreen', 'codeview']]
                ]
            };

            // 3. เปลี่ยน Textarea ธรรมดาให้เป็น Rich Text Editor
            $('#header_html').summernote(editorSettings);
            $('#footer_html').summernote(editorSettings);
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let fieldIndex = 0;
            const container = document.getElementById('fields_container');
            const addBtn = document.getElementById('add_field_btn');

            addBtn.addEventListener('click', function() {
                const newRow = `
                <div class="row mb-3 field-row align-items-end p-3 border rounded bg-white shadow-sm">
                    <div class="col-md-3">
                        <label class="form-label">ชื่อฟิลด์สำหรับกรอก <span class="text-danger">*</span></label>
                        <input type="text" name="fields[${fieldIndex}][field_label]" class="form-control" placeholder="เช่น อายุคนขับ, น้ำหนักรถ" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">รหัสตัวแปร (อักษรภาษาอังกฤษเท่านั้น) <span class="text-danger">*</span></label>
                        <input type="text" name="fields[${fieldIndex}][field_key]" class="form-control" placeholder="เช่น driver_age" required pattern="[A-Za-z0-9_]+">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">ประเภทข้อมูล</label>
                        <select name="fields[${fieldIndex}][field_type]" class="form-select">
                            <option value="text">ข้อความทั่วไป</option>
                            <option value="number">ตัวเลข</option>
                            <option value="date">วันที่</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">การบังคับกรอก</label>
                        <select name="fields[${fieldIndex}][is_required]" class="form-select">
                            <option value="1">บังคับ</option>
                            <option value="0">ไม่บังคับ</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
            `;
                container.insertAdjacentHTML('beforeend', newRow);
                fieldIndex++;
            });

            // Event ลบแถว
            container.addEventListener('click', function(e) {
                if (e.target.closest('.remove-row')) {
                    e.target.closest('.field-row').remove();
                }
            });
        });
    </script>
@endpush
