@section('title', 'ระบบตรวจมาตรฐานรถ')
@section('description', 'ID Drives')
@extends('layout.app')
@section('content')

    <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-6 mb-4">
                    <div class="mt-25 card card-default card-md shadow-sm text-center">
                        <div class="card-body">

                            <a href="{{route('local.home')}}" class="btn btn-outline-secondary btn-sm mb-3">
                                <i class="fas fa-arrow-left"></i> กลับไปหน้ารายการ
                            </a>

                           
                            <!-- ตารางข้อมูลรถ -->
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped text-start">
                                    <tbody>
                                        <tr>
                                            <th width="30%">ทะเบียนรถ</th>
                                            <td>{{ $vehicle->car_plate }}</td>
                                        </tr>
                                     
                                        <tr>
                                            <th>วันที่ลงทะเบียน</th>
                                            <td>
                                                {{ thai_date($vehicle->created_at) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>สถานะ</th>
                                            <td>
                                                @switch($vehicle->status)
                                                    @case(1)
                                                        <span class="badge badge-round badge-success badge-lg">ใช้งานได้</span>
                                                    @break

                                                    @case(2)
                                                        <span class="badge badge-round badge-warning badge-lg">รอซ่อมแซม</span>
                                                    @break

                                                    @case(3)
                                                        <span class="badge badge-round badge-danger badge-lg">งดใช้งาน</span>
                                                    @break

                                                    @default
                                                        <span class="badge badge-round badge-default badge-lg">ไม่ทราบสถานะ</span>
                                                @endswitch
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="border-top my-3"></div>

                                <a href="{{ route('user.chk_start', $vehicle->car_id) }}"
                                    class="btn btn-block btn-primary fs-18">เริ่มการตรวจรถ <i
                                        class="fas fa-arrow-right"></i></a>
                            </div>


                        </div>
                    </div>
                </div>


                <!-- รายการแจ้งซ่อม -->
                <div class="col-md-6 mb-4">
                    <div class="card mt-25 card-default card-md shadow-sm">
                        <div class="card-header bg-white border-0">
                            <span class="fs-20 fw-bold">รายการแจ้งซ่อม</span>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover mb-0 text-start">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ลำดับ</th>
                                            <th>หนังสือแจ้งซ่อม</th>
                                            <th>วันที่แจ้ง</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>1</td>
                                            <td>หนังสือแจ้งซ่อม 001</td>
                                            <td>**</td>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td>หนังสือแจ้งซ่อม 002</td>
                                            <td>**</td>
                                        </tr>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>


            <div class="row">
                <div class="col-md-12">
                    <div class="card mt-2 mb-25 shadow-sm">
                        <div class="card-header">
                            <span class="fs-20 mb-0">ระเบียนการตรวจรถ</span>
                        </div>
                        <div class="card-body">
                            @if (session('error'))
                                <div class="alert alert-danger fs-20 fw-bold">{{ session('error') }}</div>
                            @endif
                            <div class="table-responsive">
                                <table class="table table-default table-bordered mb-0" id="table-one">
                                    <thead class="table-success">
                                        <tr>
                                            <th>#</th>
                                            <th>วันที่ตรวจ</th>
                                            <th>ฟอร์มตรวจ</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($record as $item)
                                            <tr>
                                                <td> {{ $loop->iteration }} </td>
                                                <td> <a href="{{ route('form_report', ['rec' => $item->record_id]) }}">
                                                        {{ thai_datetime($item->date_check) }} </a></td>
                                                <td> {{ $item->form_name }} </td>
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
