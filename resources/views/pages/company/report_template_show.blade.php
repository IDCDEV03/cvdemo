@section('title', 'ระบบตรวจมาตรฐานรถ : รายละเอียดแบบรายงาน')
@section('description', 'ID Drives')
@extends('layout.app')
@section('content')
<div class="container-fluid">
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="breadcrumb-main d-flex justify-content-between align-items-center mb-3">
                <h4 class="text-capitalize breadcrumb-title">รายละเอียดแบบรายงาน: {{ $template->template_name }}</h4>
                <a href="{{ route('company.report_template.index') }}" class="btn btn-light btn-default btn-squared">
                    <i class="fas fa-arrow-left"></i> กลับไปหน้ารายการ
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-5 mb-25">
            <div class="card h-100">
                <div class="card-header">
                    <h6>ตัวแปรและข้อมูลที่ต้องกรอก</h6>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <span class="text-muted d-block mb-1">สถานะการใช้งาน:</span>
                        @if($template->is_active == 1)
                            <span class="badge bg-success rounded-pill">เปิดใช้งาน</span>
                        @else
                            <span class="badge bg-danger rounded-pill">ปิดใช้งาน</span>
                        @endif
                    </div>

                    <h6 class="mb-3 text-primary"><i class="fas fa-list-ul"></i> รายการฟิลด์ข้อมูลที่สร้างไว้</h6>
                    
                    @if($fields->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead class="">
                                    <tr>
                                        <th>ชื่อที่แสดง </th>
                                        <th>รหัสตัวแปร </th>
                                        <th>รูปแบบ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($fields as $field)
                                        <tr>
                                            <td>{{ $field->field_label }}
                                                @if($field->is_required)
                                                    <span class="text-danger">*</span>
                                                @endif
                                            </td>
                                            <td><code>[{{ $field->field_key }}]</code></td>
                                            <td>
                                                @if($field->field_type == 'text') ข้อความ
                                                @elseif($field->field_type == 'number') ตัวเลข
                                                @elseif($field->field_type == 'date') วันที่
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <p class="text-muted small mt-2">* เมื่อนำแม่แบบนี้ไปใช้ ระบบจะเด้งหน้าต่างให้เจ้าหน้าที่กรอกข้อมูลเหล่านี้ก่อนพิมพ์รายงาน</p>
                    @else
                        <div class="alert alert-light text-center text-muted">
                            ไม่มีการสร้างฟิลด์ข้อมูลเพิ่มเติมสำหรับแม่แบบนี้ (ใช้เฉพาะข้อมูล Default)
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-7 mb-25">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="fs-18 fw-bold text-primary">พรีวิวโครงสร้างรายงาน (Preview)</span>               
                </div>
                <div class="card-body  d-flex justify-content-center p-4">
                    
                    <div class="bg-white shadow-sm border" style="width: 100%; max-width: 800px; min-height: 900px; padding: 40px; position: relative;">
                        
                        <div class="report-header pb-3 mb-4" style="border-bottom: 2px solid #333;">
                            <span class="text-muted small d-block mb-2">ส่วนหัวกระดาษ (Header)</span>
                          <div class="header-content preview-html">
                                @if(!empty($template->header_html))
                                    {!! $template->header_html !!}
                                @else
                                    <div class="text-center text-muted py-3 border-dashed">ไม่ได้กำหนดหัวกระดาษ</div>
                                @endif
                            </div>
                        </div>

                        <div class="report-body text-center text-muted border-dashed p-5 my-5" style="border: 2px dashed #ccc; background-color: #fcfcfc; border-radius: 8px;">
                            <h4 class="text-muted"><i class="fas fa-file-invoice text-light mb-3" style="font-size: 50px;"></i><br>พื้นที่แสดงผลการตรวจรถ</h4>
                            <p class="mb-0">ระบบจะนำตารางสรุปผลการตรวจและรูปถ่าย<br>มาแทรกในพื้นที่นี้โดยอัตโนมัติ</p>
                        </div>

                        <div class="report-footer pt-3 mt-4" style="border-top: 2px solid #333; position: absolute; bottom: 40px; left: 40px; right: 40px;">
                            <span class="text-muted small d-block mb-2">ส่วนท้ายกระดาษ (Footer)</span>
                            <div class="footer-content" style="min-height: 80px;">
                                @if(!empty($template->footer_html))
                                    {!! $template->footer_html !!}
                                @else
                                    <div class="text-center text-muted py-3 ">ไม่ได้กำหนดท้ายกระดาษ</div>
                                @endif
                            </div>
                        </div>

                    </div>
                    </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<style>  
 
    .preview-html {
        color: #333;
        width: 100%;
        overflow-x: auto; 
        display: block; 
    }

    .preview-html table {
        width: 100% !important;
        min-width: 800px; 
        border-collapse: collapse;
    }
    
 
    .preview-html col {
        width: auto !important;
    }
    
    .preview-html td, .preview-html th {
        padding: 8px 10px !important;
        font-size: 12px !important;
  
    }
</style>
<script>
    document.addEventListener("DOMContentLoaded", function() {  
        const previewAreas = document.querySelectorAll('.preview-html');
        previewAreas.forEach(area => {
            area.innerHTML = area.innerHTML.replace(/(\[[a-zA-Z0-9_]+\])/g, '<code>$1</code>');
        });
    });
</script>
@endpush