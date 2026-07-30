@section('title', 'รายการรถ')
@section('description', 'ID Drives')
@extends('layout.app')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<style>
    /* Base: set 16px at cell level — children inherit via cascade */
    .veh-table th,
    .veh-table td {
        font-size: 16px !important;
        vertical-align: middle;
    }
    .veh-table th {
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        white-space: nowrap;
        border: none;
    }
    .veh-table td {
        border-color: #F0F1F3;
    }
    .veh-table tbody tr:hover {
        background-color: #f5f7ff;
    }
    .plate-badge {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        background: #EEF2FF;
        color: #3730A3;
        border: 1.5px solid #C7D2FE;
        border-radius: 10px;
        padding: 5px 13px;
        font-weight: 700;
        letter-spacing: 0.06em;
        white-space: nowrap;
        text-decoration: none;
        transition: all 0.18s ease;
    }
    .plate-badge:hover {
        background: linear-gradient(135deg, #5F63F2, #7B7FF5);
        color: #fff;
        border-color: transparent;
        box-shadow: 0 4px 12px rgba(95,99,242,0.35);
        transform: translateY(-1px);
    }
    .plate-badge:hover .plate-icon {
        color: #fff;
    }
    .plate-icon {
        font-size: 18px !important;
        color: #6366F1;
        flex-shrink: 0;
    }
    /* Higher specificity than .veh-table td — intentionally smaller secondary line */
    .veh-table td .veh-sub-text {
        font-size: 14px !important;
        color: #aaa;
    }
    .supply-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #F0FDF4;
        color: #15803D;
        border: 1.5px solid #BBF7D0;
        border-radius: 8px;
        padding: 4px 11px;
        font-weight: 600;
        white-space: nowrap;
    }
    .veh-table td .supply-badge {
        font-size: 14px !important;
    }
    /* Higher specificity than .veh-table td — badge stays compact */
    .veh-table td .badge {
        font-size: 13px !important;
    }
    /* Higher specificity than .veh-table td — button label slightly smaller */
    .veh-table td .veh-action-btn {
        font-size: 14px !important;
    }
    .veh-action-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 16px;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        border: none;
        color: #5F63F2;
        background: #EEF2FF;
        transition: all 0.18s ease;
        white-space: nowrap;
    }
    .veh-action-btn:hover {
        background: linear-gradient(135deg, #5F63F2, #7B7FF5);
        color: #fff;
        box-shadow: 0 4px 12px rgba(95,99,242,0.35);
        transform: translateY(-1px);
    }
</style>
@endpush

@section('content')
<div class="container-fluid">

    <div class="row mb-3">
        <div class="col-12">
            <div class="breadcrumb-main user-member justify-content-sm-between">
                <div class="d-flex align-items-center user-member__title">
                    <h4 class="text-capitalize fw-500 breadcrumb-title">รายการรถทั้งหมด</h4>
                </div>
                <a href="{{ route('vehicles.create') }}" class="btn btn-primary">
                    <i class="uil uil-plus me-1"></i> ลงทะเบียนรถใหม่
                </a>
            </div>
        </div>
    </div>

    <div class="card mb-20 mt-10" style="border-radius:14px; box-shadow:0 2px 12px rgba(0,0,0,0.07); border:none;">
        <div class="card-body p-3">
            <div class="table-responsive">
                <table class="table table-hover veh-table mb-0" id="veh-table">
                    <thead>
                        <tr style="background: linear-gradient(135deg,#5F63F2,#7B7FF5); color:#fff;">
                            <th class="ps-3" style="width:130px;">ทะเบียนรถ</th>
                            <th>ยี่ห้อ / รุ่น</th>
                            <th>ประเภทรถ</th>
                            <th>บริษัทฯ / Supply</th>
                            <th class="text-center" style="width:110px;">สถานะ</th>
                            <th class="text-center pe-3" style="width:110px;">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($veh_list as $item)
                        <tr>
                            <td class="ps-3">
                                <a href="{{ route('vehicles.show', ['veh_id' => $item->car_id]) }}" class="plate-badge">
                                    <i class="uil uil-car-sideview plate-icon"></i>
                                    {{ $item->car_plate }}
                                </a>
                            </td>
                            <td>
                                @if($item->car_brand || $item->car_model)
                                    <span style="font-weight:600; color:#333;">{{ $item->car_brand }}</span>
                                    @if($item->car_model)
                                        <span class="text-muted"> {{ $item->car_model }}</span>
                                    @endif
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="text-muted">{{ $item->vehicle_type ?? '-' }}</span>
                            </td>
                            <td>
                                @if($item->supply_name)
                                    <div>
                                        <span class="supply-badge">
                                            <i class="uil uil-truck"></i>{{ $item->supply_name }}
                                        </span>
                                    </div>
                                @endif
                                @if($item->name)
                                    <div class="veh-sub-text mt-1"><i class="uil uil-building me-1"></i>{{ $item->name }}</div>
                                @endif
                                @if(!$item->name && !$item->supply_name)
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($item->status == '1')
                                    <span class="badge rounded-pill px-3 py-2" style="background:#D1FAE5; color:#065F46;">ปกติ</span>
                                @elseif($item->status == '2')
                                    <span class="badge rounded-pill px-3 py-2" style="background:#FEF3C7; color:#92400E;">รอซ่อม</span>
                                @elseif($item->status == '0')
                                    <span class="badge rounded-pill px-3 py-2" style="background:#FEE2E2; color:#991B1B;">งดใช้งาน</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center pe-3">
                                <a href="{{ route('vehicles.show', ['veh_id' => $item->car_id]) }}" class="veh-action-btn">
                                     รายละเอียด
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
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
        $('#veh-table').DataTable({
            responsive: true,
            pageLength: 25,
            order: [], // preserve DB sort (vehicles_detail.id ASC)
            columnDefs: [
                { orderable: false, targets: -1 }
            ],
            language: {
                search: "ค้นหา:",
                lengthMenu: "แสดง _MENU_ รายการ",
                info: "แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ",
                infoEmpty: "ไม่มีข้อมูล",
                zeroRecords: "ไม่พบข้อมูลที่ค้นหา",
                paginate: { next: "ถัดไป", previous: "ก่อนหน้า" }
            }
        });
    });
</script>
@endpush
