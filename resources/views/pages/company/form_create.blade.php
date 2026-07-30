@section('title', 'ระบบตรวจมาตรฐานรถ')
@section('description', 'ID Drives')
@extends('layout.app')
@section('content')
    <div class="container-fluid">
        <div class="social-dash-wrap">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb-main">
                        <h4 class="text-capitalize breadcrumb-title">สร้างฟอร์มใหม่</h4>

                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card mb-25">
                        <div class="card-body">
                            @if (session('error'))
                                <div class="alert alert-big alert-danger">
                                    <div class="alert-content">
                                        {{ session('error') }}
                                    </div>
                                </div>
                            @endif
                            <form action="{{ route('company.form_store1') }}" method="POST">
                                @csrf
                                <div class="form-group row mb-25">
                                    <div class="col-sm-3 d-flex aling-items-center">
                                        <label class=" col-form-label color-dark align-center">รหัสฟอร์ม (ถ้ามี) </label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control ih-medium ip-gray radius-xs"
                                            id="form_code" name="form_code">
                                    </div>
                                </div>
                                <div class="form-group row mb-25">
                                    <div class="col-sm-3 d-flex aling-items-center">
                                        <label class="col-form-label color-dark align-center">ชื่อฟอร์ม<span
                                                class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control ih-medium ip-gray radius-xs"
                                            id="form_name" name="form_name" required>
                                    </div>
                                </div>

                                <div class="form-group row mb-25">
                                    <div class="col-sm-3 d-flex aling-items-center">
                                        <label class="col-form-label color-dark align-center">ประเภทรถ<span
                                                class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-sm-9">
                                        <select name="vehicle_type_id" id="vehicle_type_id"
                                            class="form-select ih-medium ip-gray radius-xs" required>
                                            <option value="" disabled selected>-- เลือกประเภทรถ --</option>
                                            @foreach ($vehicle_types as $vtype)
                                                <option value="{{ $vtype->id }}">{{ $vtype->vehicle_type }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row mb-25">
                                    <div class="col-sm-3 d-flex aling-items-center">
                                        <label class="col-form-label  color-dark align-center">ตั้งค่าฟอร์ม<span
                                                class="text-danger">*</span></label>
                                    </div>

                                    <div class="col-sm-9">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="form_setting"
                                                id="form_setting" value="1" checked>
                                            <label class="form-check-label" for="form_setting">เปิดการใช้</label>
                                        </div>

                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="form_setting"
                                                id="form_setting1" value="0">
                                            <label class="form-check-label" for="form_setting1">ปิดการใช้</label>
                                        </div>
                                    </div>
                                </div>




                                <div class="form-group row mb-25">
                                    <div class="col-sm-3 d-flex aling-items-center">
                                        <label class="col-form-label  color-dark align-center">กำหนดค่าฟอร์ม<span
                                                class="text-danger">*</span></label>
                                    </div>

                                    <div class="col-sm-9">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="form_scope"
                                                id="scope_public" value="public" required>
                                            <label class="form-check-label" for="scope_public">ทุกหน่วยงาน</label>
                                        </div>

                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="form_scope"
                                                id="scope_specific" value="specific">
                                            <label class="form-check-label" for="scope_specific">เฉพาะบางหน่วยงาน</label>
                                        </div>
                                    </div>
                                </div>

                                <!--hide-->
                                <div class="mb-3 d-none" id="supply_select_box">
                                    <div class="border-top my-3"></div>
                                    <div class="table4 table-responsive">
                                        <table class="table mb-0">
                                            <thead>
                                                <tr class="userDatatable-header">
                                                    <th>
                                                        <span class="fs-16">เลือก Supply</span>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($supply_list as $data)
                                                    <tr>
                                                        <td>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="supply_ids[]" value="{{ $data->sup_id }}"
                                                                    id="supply_{{ $data->sup_id }}">
                                                                <label class="form-check-label"
                                                                    for="supply_{{ $data->sup_id }}">{{ $data->supply_name }}</label>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!--EndHide-->

                                <div class="col-sm-9">
                                    <div class="layout-button mt-25">
                                        <button type="submit"
                                            class="btn btn-primary btn-default btn-squared">ถัดไป</button>
                                    </div>
                                </div>

                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const radios = document.querySelectorAll('input[name="form_scope"]');
            const agencyBox = document.getElementById('supply_select_box');

            radios.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.value === 'specific') {
                        agencyBox.classList.remove('d-none');
                    } else {
                        agencyBox.classList.add('d-none');

                        // เคลียร์ checkbox ทุกช่อง
                        document.querySelectorAll('input[name="supply_ids[]"]').forEach(cb => cb
                            .checked = false);
                    }
                });
            });
        });
    </script>
@endpush
