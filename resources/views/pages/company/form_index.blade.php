@section('title', 'ระบบตรวจมาตรฐานรถ')
@section('description', 'ID Drives')
@extends('layout.app')
@section('content')
    <div class="container-fluid">

        <div class="col-md-12">
            <div class="card mt-20  mb-25">
                <div class="card-body">
                    <div class="dm-button-list d-flex flex-wrap gap-2">
                        <a href="{{route('company.form-groups.index')}}" class="btn btn-primary btn-transparent-primary fs-16">สร้างกลุ่มฟอร์ม (Form
                            Group)</a>
                        |
                        <a href="{{ route('company.form.create') }}"
                            class="btn btn-secondary btn-transparent-secondary fs-16">สร้างฟอร์มใหม่</a>
                        <a href="{{route('company.report_template.index')}}" class="btn btn-info btn-transparent-info fs-16">จัดการ Template รายงาน</a>                      
                        <a href="{{route('company.pre_inspection.index')}}" class="btn btn-warning btn-transparent-warning fs-16">จัดการ Template ก่อนตรวจ</a>
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
                                    <th>ประเภทรถ</th>
                                    <th>สถานะ</th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($form_list as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->form_code }}</td>
                                        <td><a
                                                href="{{ route('company.form.create3', ['id' => $item->form_id]) }}">{{ $item->form_name }}</a>
                                        </td>
                                        <td>{{ $item->vehicle_type ?? '-' }}</td>
                                        <td>
                                            @if ($item->form_status == 1)
                                                <span class="badge bg-success rounded-pill">Active</span>
                                            @else
                                                <span class="badge bg-danger rounded-pill">InActive</span>
                                            @endif
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
