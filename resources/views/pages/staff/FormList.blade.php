@section('title', 'ระบบตรวจมาตรฐานรถ')
@section('description', 'ID Drives')
@extends('layout.app')
@section('content')
    <div class="container-fluid">

        <div class="col-md-12">
            <div class="card mt-20 mb-25 shadow-sm">
                <div class="card-body">
                    <div class="card-body">

                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <a href="{{route('staff.form-group.index')}}"
                                        class="btn btn-primary btn-default btn-shadow-primary px-30">
                                        <i class="uil uil-plus-circle me-1"></i> สร้างกลุ่มฟอร์ม (Form Group)
                                    </a>
                                </div>

                                <div class="py-10 mb-10 border-top">
                                    <span
                                        class="text-light fs-13 fw-500 text-uppercase letter-spacing-1">การจัดการส่วนประกอบย่อย</span>
                                </div>

                                <div class="btn-group" role="group">

                                    <a href="{{ route('staff.report_template.index') }}"
                                        class="btn btn-info btn-default btn-squared btn-transparent-info btn-sm px-20">
                                        จัดการ Template รายงาน
                                    </a>
                                    <a href="{{ route('staff.form_new') }}"
                                        class="btn btn-secondary btn-default btn-squared btn-transparent-secondary btn-sm px-20">
                                        สร้างฟอร์มใหม่
                                    </a>
                                    <a href="{{ route('staff.pre_inspection.index') }}"
                                        class="btn btn-warning btn-default btn-squared btn-transparent-warning btn-sm px-20">
                                        จัดการ Template ก่อนตรวจ
                                    </a>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <div class="alert-content">
                                <p>{{ session('success') }}</p>
                                <button type="button" class="btn-close text-capitalize" data-bs-dismiss="alert"
                                    aria-label="Close">
                                    <i class="uil uil-times"></i>
                                </button>
                            </div>
                        </div>
                    @endif
                    <div class="card">
                        <div class="card-header">
                            <h6> รายการฟอร์มตรวจ</h6>
                        </div>
                        <div class="card-body">

                            <table class="table table-bordered" id="forms-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>รหัสฟอร์ม</th>
                                        <th>ชื่อฟอร์ม</th>
                                        <th>สถานะ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($form_list as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->form_code }}</td>
                                            <td><a
                                                    href="{{ route('staff.form_step3', ['id' => $item->form_id]) }}">{{ $item->form_name }}</a>
                                            </td>
                                            <td>
                                                **
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>



                        </div>
                    </div>
                </div>
            </div>

        </div>
    @endsection

    @push('scripts')
        <!-- DataTables  -->
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

        <script>
            $(document).ready(function() {
                $('#forms-table').DataTable({
                    responsive: true,
                    pageLength: 25,
                    language: {
                        search: "ค้นหา:",
                        lengthMenu: "แสดง _MENU_ รายการ",
                        info: "แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ",
                        paginate: {
                            next: "ถัดไป",
                            previous: "ก่อนหน้า"
                        }
                    }
                });
            });
        </script>
    @endpush
