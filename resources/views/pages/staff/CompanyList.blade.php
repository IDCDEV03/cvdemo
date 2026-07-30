@section('title', 'ระบบตรวจมาตรฐานรถ')
@section('description', 'ID Drives')
@extends('layout.app')
@section('content')
    <div class="container-fluid">

        <div class="row">
            <div class="col-md-12">

                <div class="card mt-20 mb-25 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="fw-bold fs-20 ">
                                รายการบริษัทฯว่าจ้าง
                            </div>
                          
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
                                @foreach ($company_list as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td> 
                                            <a href="{{route('staff.cp_sup',['id'=>$item->company_code])}}">
                                            {{ $item->name }} </a>
                                        </td>
                                        <td>
                                            @if ($item->user_status == '1')
                                                <label class="badge badge-round badge-success">ใช้งาน</label>
                                            @elseif ($item->user_status == '0')
                                                <label class="badge badge-round badge-warning">รอยืนยัน</label>
                                            @elseif ($item->user_status == '2')
                                                <label class="badge badge-round badge-danger">ปิด</label>
                                            @endif
                                        </td>
                                        <td>
                                            {{ thai_date($item->created_at) }}
                                        </td>
                                        <td>
                                          
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
