@section('title', 'ระบบตรวจมาตรฐานรถ')
@section('description', 'ID Drives')
@extends('layout.LayoutAdmin')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">

         <div class="card mt-20 mb-25 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="fw-bold fs-20 ">
                                รายการบริษัทขนส่ง (Supply)
                            </div>
                            <a href="{{ route('admin.cp_list') }}" class="btn btn-sm btn-info">
                               + ลงทะเบียน Supply
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
                                    <th>ชื่อ Supply</th>
                                    <th>สถานะ</th>
                                    <th>บริษัทว่าจ้าง</th>
                                    <th>วันที่เพิ่ม</th>
                                    <th>จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($supply_list as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td> {{ $item->name }} </td>
                                        <td>
                                            @if ($item->user_status == '1')
                                                <label class="badge badge-round badge-success">ใช้งาน</label>
                                            @elseif ($item->user_status == '0')
                                                <label class="badge badge-round badge-warning">ปิด</label>
                                            @endif
                                        </td>
                                        <td>{{ $item->company_name }}</td>
                                        <td> {{ thai_date($item->created_at) }}</td>
                                        <td>

                                            <div class="dropdown">
                                                <button type="button"
                                                    class="btn btn-light btn-outlined btn-outline-light color-light dropdown-toggle"
                                                    id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true"
                                                    aria-expanded="false">
                                                    ตัวเลือก
                                                    <i class="la la-angle-down"></i>
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.sup_edit', $item->user_id) }}">แก้ไขข้อมูล</a>
                                                    <a class="dropdown-item" href="#">ลบ</a>
                                                </div>

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
