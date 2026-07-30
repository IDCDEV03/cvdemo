@section('title', 'รายละเอียด Supply')
@section('description', 'ID Drives')
@extends('layout.app')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
<div class="container-fluid">

    <div class="row pt-4 pb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div class="breadcrumb-main mb-0">
                <h4 class="text-capitalize breadcrumb-title">
                    รายละเอียด Supply:
                    <span class="text-primary">{{ $supply->supply_name }}</span>
                </h4>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('staff.supplies.edit', $supply->sup_id) }}" class="btn btn-outline-warning btn-sm">
                    <i class="uil uil-edit"></i> แก้ไข
                </a>
                <a href="{{ route('staff.supplies.index') }}" class="btn btn-outline-dark btn-sm">
                    <i class="uil uil-arrow-left"></i> ย้อนกลับ
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card mb-4 border-0 radius-xl">
                <div class="card-body">

                    <div class="dm-tab tab-large">

                        <ul class="nav nav-tabs vertical-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="vehicles-tab" data-bs-toggle="tab" href="#vehicles"
                                    role="tab" aria-selected="true">
                                    <i class="uil uil-truck"></i> ข้อมูลรถ
                                    <span class="badge bg-primary ms-2">{{ $vehicles->count() }}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="drivers-tab" data-bs-toggle="tab" href="#drivers" role="tab"
                                    aria-selected="false">
                                    <i class="uil uil-users-alt"></i> พนักงานขับรถ
                                    <span class="badge bg-info ms-2">{{ $drivers->count() }}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="info-tab" data-bs-toggle="tab" href="#info" role="tab"
                                    aria-selected="false">
                                    <i class="uil uil-info-circle"></i> ข้อมูลบริษัท
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content mt-4">

                            {{-- Tab: รถ --}}
                            <div class="tab-pane fade show active" id="vehicles" role="tabpanel"
                                aria-labelledby="vehicles-tab">

                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="fs-15 fw-500 text-light">
                                        จำนวนรถที่ลงทะเบียน:
                                        <span class="dm-tag tag-success tag-transparented fs-18">{{ $vehicles->count() }}</span>
                                    </div>
                                    <a href="{{ route('vehicles.create') }}" class="btn btn-primary btn-sm">
                                        <i class="uil uil-plus"></i> ลงทะเบียนรถใหม่
                                    </a>
                                </div>

                                <div class="table-responsive">
                                    <table id="vehiclesTable" class="table table-hover align-middle w-100">
                                        <thead>
                                            <tr style="background: linear-gradient(135deg,#5F63F2,#7B7FF5); color:#fff;">
                                                <th class="border-0 ps-3">ทะเบียนรถ</th>
                                                <th class="border-0">ยี่ห้อ / รุ่น / เลขรถ</th>
                                                <th class="border-0">ประเภทรถ</th>
                                                <th class="border-0 text-center">สถานะ</th>
                                                <th class="border-0 text-center">จัดการ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($vehicles as $car)
                                                <tr style="border-bottom:1px solid #F0F1F3;">
                                                    <td class="ps-3" style="font-weight:700; font-size:15px; color:#1a1a1a;">
                                                        {{ $car->car_plate }}
                                                    </td>
                                                    <td class="text-muted" style="font-size:14px;">
                                                        {{ implode(' / ', array_filter([$car->car_brand, $car->car_model, $car->car_number_record])) ?: '-' }}
                                                    </td>
                                                    <td style="font-size:14px;">{{ $car->type_name ?? '-' }}</td>
                                                    <td class="text-center">
                                                        @if ($car->status == '1')
                                                            <span class="badge rounded-pill" style="background:#D1FAE5; color:#065F46; font-size:12px;">ปกติ</span>
                                                        @elseif ($car->status == '2')
                                                            <span class="badge rounded-pill" style="background:#FEF3C7; color:#92400E; font-size:12px;">รอซ่อม</span>
                                                        @else
                                                            <span class="badge rounded-pill" style="background:#FEE2E2; color:#991B1B; font-size:12px;">งดใช้งาน</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="{{ route('vehicles.show', $car->car_id) }}"
                                                            class="btn btn-sm btn-outline-secondary px-3">
                                                            รายละเอียด
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted py-4">ไม่มีข้อมูลรถ</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- Tab: พนักงานขับรถ --}}
                            <div class="tab-pane fade" id="drivers" role="tabpanel" aria-labelledby="drivers-tab">
                                <div class="d-flex justify-content-end mb-3">
                                    <a href="{{ route('drivers.create') }}" class="btn btn-info btn-sm text-white">
                                        <i class="uil uil-plus"></i> เพิ่มพนักงานขับรถ
                                    </a>
                                </div>
                                <div class="table-responsive">
                                    <table id="driversTable" class="table table-hover align-middle w-100">
                                        <thead>
                                            <tr style="background: linear-gradient(135deg,#0EA5E9,#38BDF8); color:#fff;">
                                                <th class="border-0 ps-3">ชื่อ-นามสกุล</th>
                                                <th class="border-0">เลขบัตรประชาชน</th>
                                                <th class="border-0">เลขที่ใบขับขี่</th>
                                                <th class="border-0 text-center">สถานะ</th>
                                                <th class="border-0 text-center">จัดการ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($drivers as $driver)
                                                <tr style="border-bottom:1px solid #F0F1F3;">
                                                    <td class="ps-3" style="font-weight:700; font-size:15px; color:#1a1a1a;">
                                                        {{ $driver->prefix }}{{ $driver->name }} {{ $driver->lastname }}
                                                    </td>
                                                    <td class="text-muted" style="font-size:14px;">{{ $driver->id_card_no ?? '-' }}</td>
                                                    <td class="text-muted" style="font-size:14px;">{{ $driver->driver_license_no ?? '-' }}</td>
                                                    <td class="text-center">
                                                        @if ($driver->driver_status == '1')
                                                            <span class="badge rounded-pill" style="background:#D1FAE5; color:#065F46; font-size:12px;">ปกติ</span>
                                                        @else
                                                            <span class="badge rounded-pill" style="background:#FEE2E2; color:#991B1B; font-size:12px;">ปิดใช้งาน</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="btn-group" role="group">
                                                            <a href="{{ route('drivers.show', $driver->driver_id) }}"
                                                                class="btn btn-sm btn-outline-secondary" title="รายละเอียด">
                                                                <i class="uil uil-eye"></i>
                                                            </a>
                                                            <a href="{{ route('drivers.edit', $driver->driver_id) }}"
                                                                class="btn btn-sm btn-outline-warning" title="แก้ไข">
                                                                <i class="uil uil-edit"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted py-4">ไม่มีข้อมูลพนักงานขับรถ</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- Tab: ข้อมูลบริษัท --}}
                            <div class="tab-pane fade" id="info" role="tabpanel" aria-labelledby="info-tab">
                                <div class="row">
                                    <div class="col-md-3 text-center mb-3">
                                        @if ($supply->supply_logo)
                                            <img src="{{ asset($supply->supply_logo) }}"
                                                class="img-thumbnail border-0 shadow-sm" style="max-width: 150px;">
                                        @else
                                            <div class="bg-light d-flex align-items-center justify-content-center mx-auto rounded shadow-sm"
                                                style="width: 150px; height: 150px;">
                                                <i class="uil uil-image text-muted fs-1"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-md-9">
                                        <h5 class="mb-3">{{ $supply->supply_name }}</h5>
                                        <div class="row mb-2">
                                            <div class="col-sm-3 text-muted">บริษัทแม่:</div>
                                            <div class="col-sm-9">{{ $supply->company_name ?? '-' }}</div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-sm-3 text-muted">Username:</div>
                                            <div class="col-sm-9"><code>{{ $supply->username ?? '-' }}</code></div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-sm-3 text-muted">ที่อยู่:</div>
                                            <div class="col-sm-9">{{ $supply->supply_address ?? '-' }}</div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-sm-3 text-muted">เบอร์โทรติดต่อ:</div>
                                            <div class="col-sm-9">{{ $supply->supply_phone ?? '-' }}</div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-sm-3 text-muted">อีเมล:</div>
                                            <div class="col-sm-9">{{ $supply->supply_email ?? '-' }}</div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-sm-3 text-muted">สถานะ:</div>
                                            <div class="col-sm-9">
                                                @if ($supply->supply_status == '1')
                                                    <span class="badge rounded-pill" style="background:#D1FAE5; color:#065F46;">เปิดใช้งาน</span>
                                                @else
                                                    <span class="badge rounded-pill" style="background:#FEE2E2; color:#991B1B;">ปิดใช้งาน</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>{{-- end tab-content --}}
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#vehiclesTable').DataTable({
            responsive: true,
            pageLength: 25,
            order: [],
            columnDefs: [{ orderable: false, targets: [4] }],
            language: {
                search: "ค้นหา:",
                lengthMenu: "แสดง _MENU_ รายการ",
                info: "แสดง _START_ ถึง _END_ จาก _TOTAL_ รายการ",
                infoEmpty: "ไม่มีข้อมูล",
                zeroRecords: "ไม่พบข้อมูลที่ค้นหา",
                paginate: { next: "ถัดไป", previous: "ก่อนหน้า" }
            }
        });

        $('#driversTable').DataTable({
            responsive: true,
            pageLength: 25,
            order: [],
            columnDefs: [{ orderable: false, targets: [4] }],
            language: {
                search: "ค้นหา:",
                lengthMenu: "แสดง _MENU_ รายการ",
                info: "แสดง _START_ ถึง _END_ จาก _TOTAL_ รายการ",
                infoEmpty: "ไม่มีข้อมูล",
                zeroRecords: "ไม่พบข้อมูลที่ค้นหา",
                paginate: { next: "ถัดไป", previous: "ก่อนหน้า" }
            }
        });
    });
</script>
@endpush
