@section('title', 'แก้ไขข้อมูลช่างตรวจ')
@section('description', 'ID Drives')
@extends('layout.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb-main">
                    <h4 class="text-capitalize breadcrumb-title">แก้ไขข้อมูลช่างตรวจ</h4>
                </div>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card mb-25">
            <div class="card-header">
                <span class="fs-20 fw-bold">แก้ไขข้อมูลช่างตรวจ: {{ $inspector->ins_prefix }}{{ $inspector->ins_name }} {{ $inspector->ins_lastname }}</span>
            </div>
            <div class="card-body">
                <form action="{{ route('staff.inspectors.update', $inspector->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group mb-4">
                        <label class="fs-16 fw-500 align-center mb-10">บริษัทฯว่าจ้าง <span class="text-danger">*</span></label>
                        <select name="company_code" id="company_code" class="form-control select2" required>
                            <option value="">-- ค้นหาและเลือกบริษัทฯว่าจ้าง --</option>
                            @foreach ($companies as $company)
                                <option value="{{ $company->company_id }}"
                                    {{ $inspector->company_code == $company->company_id ? 'selected' : '' }}>
                                    {{ $company->company_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="il-gray fw-bold align-center mb-10">คำนำหน้า</label>
                                <select class="form-control px-15" name="prefix">
                                    @foreach (['นาย','นางสาว','นาง','คุณ'] as $p)
                                        <option value="{{ $p }}" {{ $inspector->ins_prefix == $p ? 'selected' : '' }}>{{ $p }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="il-gray fw-bold align-center mb-10">ชื่อ <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control ih-small ip-light radius-xs b-light px-15"
                                    value="{{ old('name', $inspector->ins_name) }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="il-gray fw-bold align-center mb-10">นามสกุล</label>
                                <input type="text" name="lastname" class="form-control ih-small ip-light radius-xs b-light px-15"
                                    value="{{ old('lastname', $inspector->ins_lastname) }}">
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6 form-group">
                            <label>เลขใบขับขี่</label>
                            <input type="text" name="dl_number" class="form-control ih-small ip-light"
                                value="{{ old('dl_number', $inspector->dl_number) }}">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>เบอร์โทร</label>
                            <input type="text" name="ins_phone" class="form-control ih-small ip-light"
                                value="{{ old('ins_phone', $inspector->ins_phone) }}">
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label>ปี พ.ศ. เกิด</label>
                            <input type="text" name="ins_birthyear" class="form-control"
                                value="{{ old('ins_birthyear', $inspector->ins_birthyear) }}">
                        </div>
                        <div class="col-md-6">
                            <label>ประสบการณ์ผู้ขับขี่ (จำนวนปี)</label>
                            <input type="text" name="ins_experience" class="form-control"
                                value="{{ old('ins_experience', $inspector->ins_experience) }}">
                        </div>
                    </div>

                    <div class="border-top my-3"></div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label>Username</label>
                            <input type="text" class="form-control" value="{{ $user->username ?? '' }}" readonly disabled>
                            <small class="text-muted">Username ไม่สามารถเปลี่ยนได้</small>
                        </div>
                        <div class="col-md-6">
                            <label>Password ใหม่ <span class="text-muted fw-normal">(เว้นว่างถ้าไม่เปลี่ยน)</span></label>
                            <input type="text" name="inspector_password" class="form-control" placeholder="ระบุรหัสผ่านใหม่">
                        </div>
                    </div>

                    <div class="border-top my-3"></div>

                    <div class="form-group mb-4">
                        <label class="fs-16 fw-500 align-center mb-10">ประเภทช่างตรวจ <span class="text-danger">*</span></label>
                        <select name="inspector_type" id="inspector_type" class="form-control select2" required>
                            <option value="">-- เลือกประเภทช่างตรวจ --</option>
                            <option value="1" {{ $inspector->inspector_type == '1' ? 'selected' : '' }}>1. ช่างประจำบริษัทฯว่าจ้าง</option>
                            <option value="2" {{ $inspector->inspector_type == '2' ? 'selected' : '' }}>2. ช่างประจำ Supply</option>
                            <option value="3" {{ $inspector->inspector_type == '3' ? 'selected' : '' }}>3. ช่างนอกหน่วยงาน (ตรวจรถได้เฉพาะ Supply ที่กำหนด)</option>
                        </select>
                    </div>

                    <div class="form-group mb-4" id="supply_single_div" style="display: none;">
                        <label class="fs-16 fw-500 align-center mb-10">เลือก Supply <span class="text-danger">*</span></label>
                        <select name="sup_id" id="sup_id" class="form-control select2" style="width: 100%;">
                            <option value="">-- กรุณาเลือกบริษัทฯว่าจ้างก่อน --</option>
                            @foreach ($supplies as $sup)
                                <option value="{{ $sup->sup_id }}"
                                    {{ $inspector->sup_id == $sup->sup_id ? 'selected' : '' }}>
                                    {{ $sup->supply_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-4" id="supply_multiple_div" style="display: none;">
                        <label class="fs-16 fw-500 align-center mb-10">กำหนดสิทธิ์การตรวจให้ Supply (เลือกได้หลายที่) <span class="text-danger">*</span></label>
                        <select name="outsource_supplies[]" id="outsource_supplies" class="form-control select2"
                            multiple="multiple" style="width: 100%;">
                            @foreach ($supplies as $sup)
                                <option value="{{ $sup->sup_id }}"
                                    {{ in_array($sup->sup_id, $outsourceAccess) ? 'selected' : '' }}>
                                    {{ $sup->supply_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="border-top my-3"></div>

                    <div class="form-group mb-4">
                        <label class="fs-16 fw-500 mb-10">สถานะ</label>
                        <select name="ins_status" class="form-control" style="max-width:200px;">
                            <option value="1" {{ $inspector->ins_status == '1' ? 'selected' : '' }}>ใช้งาน</option>
                            <option value="0" {{ $inspector->ins_status == '0' ? 'selected' : '' }}>ระงับ</option>
                        </select>
                    </div>

                    <div class="layout-button mt-25">
                        <button type="submit" class="btn btn-primary btn-default btn-squared px-30">บันทึกการเปลี่ยนแปลง</button>
                        <a href="{{ route('staff.inspectors.index') }}" class="btn btn-light btn-default btn-squared px-30">ยกเลิก</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('.select2').select2({ width: '100%' });

            const allSupplies = @json($supplies);
            const currentType = '{{ $inspector->inspector_type }}';

            function filterSupplies(selectedCompany) {
                let filtered = allSupplies.filter(s => s.company_code === selectedCompany);
                let optHtml = '<option value="">-- เลือก Supply --</option>';
                let multiHtml = '';
                filtered.forEach(s => {
                    optHtml   += `<option value="${s.sup_id}">${s.supply_name}</option>`;
                    multiHtml += `<option value="${s.sup_id}">${s.supply_name}</option>`;
                });
                $('#sup_id').html(optHtml);
                $('#outsource_supplies').html(multiHtml);
            }

            function toggleSupplyDiv(type) {
                if (type == '2') {
                    $('#supply_single_div').show();
                    $('#supply_multiple_div').hide();
                } else if (type == '3') {
                    $('#supply_single_div').hide();
                    $('#supply_multiple_div').show();
                } else {
                    $('#supply_single_div').hide();
                    $('#supply_multiple_div').hide();
                }
            }

            // Show correct div on load
            toggleSupplyDiv(currentType);

            $('#inspector_type').on('change', function() {
                toggleSupplyDiv($(this).val());
            });

            $('#company_code').on('change', function() {
                filterSupplies($(this).val());
                $('#sup_id').val(null).trigger('change');
                $('#outsource_supplies').val(null).trigger('change');
            });
        });
    </script>
@endpush
