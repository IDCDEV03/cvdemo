@section('title', 'ระบบตรวจมาตรฐานรถ : รายละเอียดเทมเพลตก่อนตรวจรถ')
@section('description', 'ID Drives')
@extends('layout.app')
@section('content')
<div class="container-fluid">
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="breadcrumb-main d-flex justify-content-between align-items-center mb-3">
                <h4 class="text-capitalize breadcrumb-title">รายละเอียดเทมเพลต</h4>
                <a href="{{ route('company.pre_inspection.index') }}" class="btn btn-primary btn-default btn-squared">
                    <i class="fas fa-arrow-left"></i> กลับไปหน้ารายการ
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 mb-25">
            <div class="card">
                <div class="card-header">
                    <h6>ข้อมูลทั่วไปของ Templete</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <span class="text-muted d-block mb-1">ชื่อ Templete:</span>
                            <h5 class="m-0">{{ $template->template_name }}</h5>
                        </div>
                        <div class="col-md-3 mb-3">
                            <span class="text-muted d-block mb-1">สถานะ:</span>
                            @if($template->is_active == 1)
                                <span class="badge bg-success rounded-pill">เปิดใช้งาน</span>
                            @else
                                <span class="badge bg-danger rounded-pill">ปิดใช้งาน</span>
                            @endif
                        </div>
                        <div class="col-md-3 mb-3">
                            <span class="text-muted d-block mb-1">วันที่สร้าง:</span>
                            <span>{{ \Carbon\Carbon::parse($template->created_at)->addYears(543)->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="card mb-25">
                <div class="card-header">
                    <h4>รายการหัวข้อที่ต้องระบุ ({{ $fields->count() }} หัวข้อ)</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <thead>
                                <tr class="userDatatable-header bg-light">
                                    <th width="10%">ที่</th>
                                    <th width="45%">ชื่อหัวข้อ</th>
                                    <th width="25%">ประเภทการกรอกข้อมูล</span></th>
                                    <th width="20%">การบังคับกรอก</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($fields as $index => $field)
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td><strong>{{ $field->field_label }}</strong></td>
                                        <td>
                                            @if($field->field_type == 'text')
                                                 <span class="dm-tag tag-primary tag-transparented fs-18"> ข้อความ</span>
                                            @elseif($field->field_type == 'image')
                                           <span class="text-info"><i class="far fa-image"></i>
                                                  รูปภาพ</span> 
                                            @elseif($field->field_type == 'gps')
                                                 <span class="text-success"><i class="fas fa-map-marker-alt"></i> พิกัด GPS</span>
                                            @else
                                                {{ $field->field_type }}
                                            @endif
                                        </td>
                                        <td>
                                            @if($field->is_required == 1)
                                                <span class="text-danger"><i class="fas fa-asterisk"></i> บังคับ</span>
                                            @else
                                                <span class="text-muted">ไม่บังคับ</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">ไม่พบข้อมูลหัวข้อ</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection