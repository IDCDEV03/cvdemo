@section('title', 'ระบบตรวจมาตรฐานรถ')
@section('description', 'ID Drives')
@extends('layout.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb-main">
                    <h4 class="text-capitalize breadcrumb-title">เพิ่มช่างตรวจใหม่</h4>
                </div>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card mb-25">
            <div class="card-header">
                <span class="fs-20 fw-bold">ข้อมูลรายละเอียดช่าง</span>
            </div>
            <div class="card-body">
                <form action="{{ route('staff.inspectors.store') }}" method="POST">
                    @csrf

                    <div class="form-group mb-4">
                        <label class="fs-16 fw-500 align-center mb-10">บริษัทฯว่าจ้าง <span
                                class="text-danger">*</span></label>
                        <select name="company_code" id="company_code" class="form-control select2" required>
                            <option value="">-- ค้นหาและเลือกบริษัทฯว่าจ้าง --</option>
                            @foreach ($companies as $company)
                                <option value="{{ $company->company_id }}">{{ $company->company_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="a1" class="il-gray fw-bold align-center mb-10">คำนำหน้า<span
                                        class="text-danger">*</span></label>
                                <select class="form-control px-15" name="prefix">
                                    <option value="คุณ">--เลือก--</option>
                                    <option selected value="นาย">นาย</option>
                                    <option value="นางสาว">นางสาว</option>
                                    <option value="นาง">นาง</option>
                                    <option value="คุณ">คุณ</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="a2" class="il-gray fw-bold align-center mb-10">ชื่อ<span
                                        class="text-danger">*</span></label>
                                <input type="text" name="name"
                                    class="form-control ih-small ip-light radius-xs b-light px-15" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="a3" class="il-gray fw-bold align-center mb-10">นามสกุล<span
                                        class="text-danger">*</span></label>
                                <input type="text" name="lastname"
                                    class="form-control ih-small ip-light radius-xs b-light px-15" required>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6 form-group">
                            <label>เลขใบขับขี่<span class="text-danger">*</span></label>
                            <input type="text" name="dl_number" class="form-control ih-small ip-light" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>เบอร์โทร</label>
                            <input type="text" name="ins_phone" class="form-control ih-small ip-light">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>ปี พ.ศ. เกิด</label>
                                <input type="text" name="ins_birthyear" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>ประสบการณ์ผู้ขับขี่ (ระบุเป็นจำนวนปี)</label>
                                <input type="text" name="ins_experience" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="border-top my-3"></div>
                    <div class="row">
                        <div class="mb-3">
                            <label>กำหนด Username สำหรับเข้าใช้งาน<span class="text-danger">*</span></label>
                            <button type="button" class="btn btn-xs btn-outline-secondary mb-2 mt-2"
                                onclick="generateRandom('company_user')">สุ่ม username</button>
                            <input type="text" name="company_user" id="company_user" class="form-control" required>
                            <div id="username-alert" class="alert alert-danger mt-2" style="display: none;"></div>

                        </div>

                        <div class="mb-3">
                            <label>กำหนด Password สำหรับเข้าใช้งาน<span class="text-danger">*</span></label>
                            <input type="text" name="inspector_password" id="inspector_password" class="form-control"
                                required>
                        </div>
                    </div>
                    <div class="border-top my-3"></div>

                    <!-- ประเภทช่าง -->
                    <div class="form-group mb-4">
                        <label class="fs-16 fw-500 align-center mb-10">ประเภทช่างตรวจ <span
                                class="text-danger">*</span></label>
                        <select name="inspector_type" id="inspector_type" class="form-control select2" required>
                            <option value="">-- ค้นหาและเลือกประเภทช่างตรวจ --</option>
                            <option value="1">1. ช่างประจำบริษัทฯว่าจ้าง </option>
                            <option value="2">2. ช่างประจำ Supply </option>
                            <option value="3">3. ช่างนอกหน่วยงาน (ตรวจรถได้เฉพาะ Supply ที่กำหนด)</option>
                        </select>
                    </div>


                    <div class="form-group mb-4" id="supply_single_div" style="display: none;">
                        <label class="fs-16 fw-500 align-center mb-10">เลือก Supply<span
                                class="text-danger">*</span></label>
                        <select name="sup_id" id="sup_id" class="form-control select2" style="width: 100%;">
                            <option value="">-- กรุณาเลือกบริษัทฯว่าจ้างก่อน --</option>
                        </select>
                    </div>

                    <!-- กล่องสำหรับ ช่างประเภทที่ 3 (เลือกได้หลาย Supply) -->
                    <div class="form-group mb-4" id="supply_multiple_div" style="display: none;">
                        <label class="fs-16 fw-500 align-center mb-10">กำหนดสิทธิ์การตรวจให้ Supply
                            (เลือกได้หลายที่)<span class="text-danger">*</span></label>
                        <!-- ใช้ Select2 แบบ Multiple -->
                        <select name="outsource_supplies[]" id="outsource_supplies" class="form-control select2"
                            multiple="multiple" style="width: 100%;">
                            <!-- Options จะถูกใส่มาทาง JavaScript -->
                        </select>
                    </div>

                    <div class="layout-button mt-25">
                        <button type="submit" class="btn btn-primary btn-default btn-squared px-30">บันทึกข้อมูล</button>
                        <a href="{{ route('staff.inspectors.index') }}"
                            class="btn btn-light btn-default btn-squared px-30">ยกเลิก</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function generateRandom(fieldId) {
            const chars = 'ABCDEFGHIJKLMNPQRSTUVWXYZ0123456789';
            let result = '';
            for (let i = 0; i < 6; i++) {
                result += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            document.getElementById(fieldId).value = result;
        }
    </script>
    <script>
        $(document).ready(function() {
            $('#company_user').blur(function() {
                var username = $(this).val();
                if (!username) {
                    $('#username-alert').hide();
                    return;
                }

                $.ajax({
                    url: '/check-username',
                    method: 'GET',
                    data: {
                        company_user: username
                    },
                    success: function(data) {
                        if (data.exists) {
                            $('#username-alert')
                                .text('Username นี้ถูกใช้แล้ว กรุณาใช้ชื่ออื่น')
                                .show();
                        } else {
                            $('#username-alert').hide();
                        }
                    }
                });
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            // 1. กำหนดค่าเริ่มต้นให้ Select2 (รองรับ Hexadash)
            $('.select2').select2({
                placeholder: function() {
                    $(this).data('placeholder');
                },
                width: '100%'
            });

            // 2. ดึงข้อมูล Supply ทั้งหมดมาจาก Backend ในรูปแบบ JSON
            const allSupplies = @json($supplies);

            // 3. เมื่อเลือก Company แม่ ให้กรอง Supply ออกมา
            $('#company_code').on('change', function() {
                let selectedCompany = $(this).val();

                // กรองเฉพาะ Supply ที่ company_code ตรงกัน
                let filteredSupplies = allSupplies.filter(sup => sup.company_code === selectedCompany);

                // สร้าง Options ใหม่
                let optionsHtml = '<option value="">-- เลือก Supply --</option>';
                let multiOptionsHtml = ''; // สำหรับ multi select ไม่ต้องมี placeholder ว่าง

                filteredSupplies.forEach(sup => {
                    optionsHtml += `<option value="${sup.sup_id}">${sup.supply_name}</option>`;
                    multiOptionsHtml += `<option value="${sup.sup_id}">${sup.supply_name}</option>`;
                });

                // อัปเดต Dropdown
                $('#sup_id').html(optionsHtml);
                $('#outsource_supplies').html(multiOptionsHtml);

                // รีเซ็ตการแสดงผลของ Select2
                $('#sup_id').val(null).trigger('change');
                $('#outsource_supplies').val(null).trigger('change');
            });

            // 4. เมื่อเลือกประเภทช่าง ให้ซ่อน/แสดงกล่อง Supply
            $('#inspector_type').on('change', function() {
                let type = $(this).val();

                if (type == '2') {
                    $('#supply_single_div').show();
                    $('#supply_multiple_div').hide();
                    $('#sup_id').prop('required', true);
                    $('#outsource_supplies').prop('required', false);
                } else if (type == '3') {
                    $('#supply_single_div').hide();
                    $('#supply_multiple_div').show();
                    $('#sup_id').prop('required', false);
                    $('#outsource_supplies').prop('required', true);
                } else {
                    $('#supply_single_div').hide();
                    $('#supply_multiple_div').hide();
                    $('#sup_id').prop('required', false);
                    $('#outsource_supplies').prop('required', false);
                    // เคลียร์ค่าทิ้งกรณีเปลี่ยนกลับไปมา
                    $('#sup_id').val(null).trigger('change');
                    $('#outsource_supplies').val(null).trigger('change');
                }
            });
        });
    </script>
@endpush
