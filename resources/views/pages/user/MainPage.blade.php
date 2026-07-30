@section('title', 'ระบบตรวจมาตรฐานรถ')
@section('description', 'ID Drives')
@extends('layout.app')
@section('content')

    <div class="container-fluid">


        <div class="row">
            <div class="col-md-12">
                <div class="card mt-4">
                    <div class="card-body">
                        <a href="{{ route('user.inspection.index') }}" class="btn btn-lg btn-info fs-18">เริ่มตรวจรถ</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card mt-4 mb-25">
                            <div class="card-header">
                                <span class="fs-20 mb-0">รถที่ลงทะเบียน</span>
                            </div>
                            <div class="card-body">

                                <div class="table-responsive">
                                    <table class="table table-default table-bordered mb-0" id="table-one">
                                        <thead class="table-info">
                                            <tr>
                                                <th class="text-sm fw-bold">#</th>
                                                <th class="text-sm fw-bold">ทะเบียนรถ</th>
                                                <th class="text-sm fw-bold">ยี่ห้อรถ/หมายเลขข้างรถ</th>
                                                <th class="text-sm fw-bold">ประเภทรถ</th>
                                                <th class="text-sm fw-bold">รายงาน</th>
                                                <th class="text-sm fw-bold">สถานะการตรวจ</th>
                                                <th>จัดการรถ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($vehicles as $item)
                                                <tr>
                                                    <td> {{ $loop->iteration }} </td>
                                                    <td>
                                                        <a href="{{ route('user.veh_detail', [$item->car_id]) }}"
                                                            class="fw-bold">
                                                            {{ $item->car_plate }}
                                                        </a>
                                                        @if ($item->chk_status === '2')
                                                            <span class="dm-tag tag-warning tag-transparented fs-18">
                                                                บันทึกแบบร่าง</span>
                                                        @endif

                                                    </td>
                                                    <td> {{ $item->car_brand }}
                                                        ({{ blank($item->car_number_record) ? '-' : $item->car_number_record }})
                                                    </td>


                                                    <td> {{ $item->vehicle_type }} </td>

                                                    <td>
                                                        @if ($item->chk_status === '1')
                                                            <a href="{{ route('user.inspection.report', $item->chk_primary_id) }}"
                                                                class="btn btn-info btn-xs shadow-sm">
                                                                <i class="uil uil-file-alt"></i> Report
                                                            </a>
                                                             @else
                                                            <span class="text-muted small">ยังไม่มี Report</span>
                                                            
                                                        @endif
                                                    </td>

                                                    <td class="text-center">
                                                        @if ($item->chk_status === '1')
                                                            <span
                                                                class="dm-tag tag-success tag-transparented fs-18">บันทึกสมบูรณ์
                                                            </span>
                                                        @elseif($item->chk_status === '2')
                                                            <div class="d-flex flex-column gap-1 align-items-center">
                                                                <a href="{{ route('user.inspection.step3', [$item->chk_primary_id]) }}"
                                                                    class="btn btn-dark btn-default btn-squared btn-transparent-dark btn-xs">
                                                                    ตรวจต่อ <i class="uil uil-arrow-right"
                                                                        style="font-size:13px;line-height:1;"></i>
                                                                </a>
                                                            </div>
                                                        @else
                                                            <span class="text-muted small">ยังไม่มีประวัติการตรวจ</span>
                                                        @endif
                                                    </td>

                                                    <td>
                                                        <a href="{{ route('user.veh_edit', [$item->car_id]) }}"
                                                            class="btn btn-primary btn-xs">แก้ไขข้อมูลรถ</a>
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
            $('#table-one').DataTable({
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
