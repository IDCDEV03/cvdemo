@section('title', 'ระบบตรวจมาตรฐานรถ')
@section('description', 'ID Drives')
@extends('layout.LayoutAdmin')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">

                <div class="card mt-20 mb-25 shadow-sm">
                    <div class="card-body">
                        <span class="fs-20 fw-bold">บริษัทว่าจ้าง : {{ $company_name->name }} </span>
                        <div class="border-top my-3"></div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="fw-bold fs-18 ">
                                รายการบริษัทขนส่ง (Supply)
                            </div>
                            <a href="{{ route('admin.sup_create', [$company_name->company_code]) }}"
                                class="btn btn-sm btn-info">
                                + ลงทะเบียนบริษัทขนส่ง (Supply)
                            </a>

                        </div>


                    </div>
                </div>


                <div class="card shadow-sm">
                    <div class="card-body">

                        <table class="table table-bordered" id="forms-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>ชื่อ</th>
                                    <th>สถานะ</th>
                                    <th>วันที่เพิ่ม</th>
                                    <th>จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($supply_list as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td> {{ $item->supply_name }} </td>
                                        <td>
                                            @if ($item->supply_status == '1')
                                                <label class="badge badge-round badge-success">ใช้งาน</label>
                                            @elseif ($item->supply_status == '0')
                                                <label class="badge badge-round badge-warning">ปิด</label>
                                            @endif
                                        </td>
                                        <td> {{ thai_date($item->created_at) }}</td>
                                        <td></td>
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
