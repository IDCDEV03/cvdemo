@section('title', 'ระบบตรวจมาตรฐานรถ')
@section('description', 'ID Drives')
@extends('layout.app')
@section('content')

    <div class="container-fluid">

        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb-main">
                    <span class="fs-24 fw-bold breadcrumb-title">รายการตรวจรถ</span>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header">
                        <h6>ค้นหารายการตรวจรถ</h6>
                    </div>
                    <div class="card-body">

                        <form method="GET" action="{{ route('supply.chk_list') }}" class="row g-3 align-items-end">

                            {{-- วันที่เริ่ม --}}
                            <div class="col-md-4 mt-4">
                                <label class="form-label">ตั้งแต่วันที่</label>
                                <input type="date" name="date_from" class="form-control"
                                    value="{{ request('date_from') }}">
                            </div>

                            {{-- ถึงวันที่ --}}
                            <div class="col-md-4">
                                <label class="form-label">ถึงวันที่</label>
                                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                            </div>

                            {{-- ปุ่มกด --}}
                            <div class="col-md-4 d-flex gap-2">
                                <button type="submit" class="btn btn-outline-primary w-50">
                                    ค้นหา
                                </button>
                                <a href="{{ route('supply.chk_list') }}" class="btn btn-outline-dark w-50">
                                    ล้างค่า
                                </a>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-body">
                        <table class="table table-bordered" id="forms-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>ผู้บันทึก</th>
                                    <th>วันที่บันทึกการตรวจ</th>
                                    <th>ทะเบียนรถ</th>
                                    <th>ยี่ห้อ/รุ่นรถ</th>
                                    <th>ประเภทรถ</th>
                                    <th>จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($record_all as $row)
                                    <tr>
                                        
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $row->ins_prefix }} {{$row->ins_name }} {{$row->ins_lastname}}</td>
                                        <td>{{ thai_datetime($row->date_check) }}</td>
                                        <td><a
                                                href="{{ route('user.chk_result', [$row->record_id]) }}">{{ $row->car_plate }}</a>
                                        </td>
                                        <td> {{ $row->car_brand }} {{ $row->car_model }} </td>
                                        <td> {{ $row->veh_type_name }} </td>
                                        <td>
                                            <div class="dm-button-list d-flex flex-wrap gap-3">

                                                <a href="{{ route('form_report', ['rec' => $row->record_id]) }}"
                                                    class="btn btn-xs btn-info btn-shadow-info">
                                                    ฟอร์มรายงาน
                                                </a>

                                              

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
                    emptyTable: "ไม่มีข้อมูล",
                    zeroRecords: "ไม่พบข้อมูลที่ค้นหา",
                    search: "ค้นหา:",
                    lengthMenu: "แสดง _MENU_ รายการ",
                    info: "แสดง _START_ ถึง _END_ จาก ทั้งหมด _TOTAL_ รายการ",
                    infoEmpty: "แสดง 0 ถึง 0 จาก ทั้งหมด 0 รายการ",
                    infoFiltered: "(คัดกรองจากทั้งหมด _MAX_ รายการ)",
                    loadingRecords: "กำลังโหลด...",
                    processing: "กำลังประมวลผล...",
                    paginate: {
                        next: "ถัดไป",
                        previous: "ก่อนหน้า"
                    }
                }
            });
        });
    </script>
@endpush
