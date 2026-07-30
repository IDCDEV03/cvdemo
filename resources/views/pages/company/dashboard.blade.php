@section('title', 'ระบบตรวจมาตรฐานรถ')
@section('description', 'ID Drives')
@extends('layout.app')
@section('content')

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb-main">
                    <h5 class="text-capitalize breadcrumb-title">{{ $companyDetails->company_name ?? $user->name }}</h5>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 text-center">
                <span class="fs-24 fw-bold" style="color: #0609aa;">รายงานระบบตรวจมาตรฐานรถ</span>
                <br>
                <span class="fs-18 text-warning fw-bold">ข้อมูล ณ วันที่ {{ thai_date(now(), true) }}</span>
                <div class="border-top my-3"></div>
            </div>
        </div>

        {{-- ─────────────────────────────────────────
            Donut Charts Row
        ───────────────────────────────────────── --}}
        <div class="row mb-10">

            {{-- Vehicle Chart Card --}}
            <div class="col-xxl-6 col-lg-6 col-12 mb-25">
                <div class="card radius-xl border-0" style="box-shadow: 0 5px 20px rgba(146,153,184,0.18);">
                    <div class="card-header d-flex align-items-center gap-10 border-0 pb-0">
                        <div class="wh-42 bg-opacity-primary-20 content-center rounded-circle flex-shrink-0">
                            <i class="uil uil-truck fs-18 color-primary"></i>
                        </div>
                        <div>
                            <h6 class="fw-600 mb-0">สถานะรถบรรทุก {{ now()->year + 543 }}</h6>
                            <span class="fs-12 color-gray">ผลการตรวจตามมาตรฐาน</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-15">
                            <div id="vehicle-chart" class="d-inline-block"></div>
                            <p class="fs-16 color-gray mt-10 mb-0">รถทั้งหมด <strong
                                    class="color-dark fs-15">{{ $totalVehicles }} คัน</strong></p>
                        </div>
                        <div class="border-top pt-15">
                            <div class="d-flex align-items-center justify-content-between mb-12">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="wh-28 bg-opacity-primary-20 content-center rounded-circle">
                                        <i class="uil uil-clipboard-alt fs-13 color-primary"></i>
                                    </span>
                                    <span class="fs-16 color-gray">จำนวนรถที่ตรวจ</span>
                                </div>
                                <span class="fs-16 fw-700 color-primary">{{ $totalInspected }} <small
                                        class="fw-400 color-gray">คัน</small></span>
                            </div>
                            <div class="border-top my-3"></div>
                            <div class="mb-12">
                                <a href="{{ route('company.vehicles.inform', ['filter' => 'passed']) }}"
                                   class="d-flex align-items-center justify-content-between text-decoration-none p-2 rounded"
                                   style="transition: background 0.15s;"
                                   onmouseover="this.style.background='rgba(54,174,124,0.08)'"
                                   onmouseout="this.style.background='transparent'">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="wh-28 bg-opacity-success-20 content-center rounded-circle">
                                            <i class="uil uil-check fs-13 color-success"></i>
                                        </span>
                                        <span class="fs-16 color-gray">ตรวจผ่าน</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="fs-16 fw-700 color-success">{{ $passCount }} <small
                                                class="fw-400 color-gray">คัน ({{ $passPercent }}%)</small></span>
                                        <i class="uil uil-angle-right color-success fs-18"></i>
                                    </div>
                                </a>
                            </div>
                            <div class="border-top my-3"></div>
                            <div>
                                <a href="{{ route('company.vehicles.inform', ['filter' => 'failed']) }}"
                                   class="d-flex align-items-center justify-content-between text-decoration-none p-2 rounded"
                                   style="transition: background 0.15s;"
                                   onmouseover="this.style.background='rgba(235,83,83,0.08)'"
                                   onmouseout="this.style.background='transparent'">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="wh-28 bg-opacity-danger-20 content-center rounded-circle">
                                            <i class="uil uil-times fs-13 color-danger"></i>
                                        </span>
                                        <span class="fs-16 color-gray">ตรวจไม่ผ่าน</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="fs-16 fw-700 color-danger">{{ $failCount }} <small
                                                class="fw-400 color-gray">คัน ({{ $failPercent }}%)</small></span>
                                        <i class="uil uil-angle-right color-danger fs-18"></i>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Driver Chart Card --}}
            <div class="col-xxl-6 col-lg-6 col-12 mb-25">
                <div class="card radius-xl border-0" style="box-shadow: 0 5px 20px rgba(146,153,184,0.18);">
                    <div class="card-header d-flex align-items-center gap-10 border-0 pb-0">
                        <div class="wh-42 bg-opacity-success-20 content-center rounded-circle flex-shrink-0">
                            <i class="uil uil-user-circle fs-18 color-success"></i>
                        </div>
                        <div>
                            <h6 class="fw-600 mb-0">พนักงานขับรถ</h6>
                            <span class="fs-12 color-gray">สถานะการอบรม ปี {{ now()->year + 543 }}</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-15">
                            <div id="driver-chart" class="d-inline-block"></div>
                            <p class="fs-16 color-gray mt-10 mb-0">พนักงานทั้งหมด <strong
                                    class="color-dark fs-16">{{ $totalDrivers }} คน</strong></p>
                        </div>
                        <div class="border-top pt-15">
                            <div class="d-flex align-items-center justify-content-between mb-12">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="wh-28 bg-opacity-info-20 content-center rounded-circle">
                                        <i class="uil uil-users-alt fs-13 color-info"></i>
                                    </span>
                                    <span class="fs-16 color-gray">พนักงานขับรถทั้งหมด</span>
                                </div>
                                <span class="fs-16 fw-700 color-info">{{ $totalDrivers }} <small
                                        class="fw-400 color-gray">คน</small></span>
                            </div>
                            <div class="border-top my-3"></div>
                            <div class="mb-12">
                                <a href="{{ route('drivers.index', ['training_filter' => 'passed']) }}"
                                   class="d-flex align-items-center justify-content-between text-decoration-none p-2 rounded"
                                   style="transition: background 0.15s;"
                                   onmouseover="this.style.background='rgba(54,174,124,0.08)'"
                                   onmouseout="this.style.background='transparent'">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="wh-28 bg-opacity-success-20 content-center rounded-circle">
                                            <i class="uil uil-graduation-cap fs-13 color-success"></i>
                                        </span>
                                        <span class="fs-16 color-gray">ผ่านการอบรม</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="fs-16 fw-700 color-success">{{ $certDriverIds }} <small
                                                class="fw-400 color-gray">คน ({{ $certPercent }}%)</small></span>
                                        <i class="uil uil-angle-right color-success fs-18"></i>
                                    </div>
                                </a>
                            </div>
                            <div class="border-top my-3"></div>
                            <div class="mb-12">
                                <a href="{{ route('drivers.index', ['training_filter' => 'no_data']) }}"
                                   class="d-flex align-items-center justify-content-between text-decoration-none p-2 rounded"
                                   style="transition: background 0.15s;"
                                   onmouseover="this.style.background='rgba(235,83,83,0.08)'"
                                   onmouseout="this.style.background='transparent'">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="wh-28 bg-opacity-danger-20 content-center rounded-circle">
                                            <i class="uil uil-exclamation-triangle fs-13 color-danger"></i>
                                        </span>
                                        <span class="fs-16 color-gray">ไม่มีข้อมูล</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="fs-16 fw-700 color-danger">{{ $noCertDrivers }} <small
                                                class="fw-400 color-gray">คน ({{ $noPercent }}%)</small></span>
                                        <i class="uil uil-angle-right color-danger fs-18"></i>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ─────────────────────────────────────────
    Action Buttons
───────────────────────────────────────── --}}
        <div class="row mb-25">
            <div class="col-12 d-flex justify-content-center gap-10 flex-wrap">
                <a href="{{ route('company.vehicles.inform') }}" class="btn btn-primary btn-default btn-squared fs-16">
                    <i class="uil uil-clipboard-alt me-2"></i>รายการทั้งหมด
                </a>
                <a href="{{ route('company.vehicles.inform') . '?' . http_build_query(['filter' => 'passed']) }}"
                    class="btn btn-success btn-squared fs-16 {{ request('filter') == 'passed' ? 'active' : 'btn-default' }}">
                    <i class="uil uil-check me-2"></i>รถตรวจผ่าน
                </a>

                <a href="{{ route('company.vehicles.inform') . '?' . http_build_query(['filter' => 'waiting']) }}"
                    class="btn btn-warning btn-squared fs-16 {{ request('filter') == 'waiting' ? 'active' : 'btn-default' }}">
                    <i class="uil uil-clock me-2"></i>รอตรวจอีกครั้ง
                </a>

                <a href="{{ route('company.vehicles.inform') . '?' . http_build_query(['filter' => 'failed']) }}"
                    class="btn btn-danger btn-squared fs-16 {{ request('filter') == 'failed' ? 'active' : 'btn-default' }}">
                    <i class="uil uil-times me-2"></i>รถที่ตรวจไม่ผ่าน
                </a>
            </div>
        </div>
<div class="border-top my-3"></div>
        {{-- ─────────────────────────────────────────
    การตรวจรถรายวัน
───────────────────────────────────────── --}}
        <div class="row mb-25">
            {{-- สถิติวันนี้ --}}
            <div class="col-12 mb-3">
                <span class="fs-18 fw-bold mb-10">
                    <i class="uil uil-calendar-alt me-2 color-primary"></i>การตรวจรถวันนี้
                </span>
            </div>
            <div class="col-md-4 mb-3">
                <a href="{{ route('company.vehicles.inform', ['start_date' => now()->format('Y-m-d'), 'end_date' => now()->format('Y-m-d'), 'filter' => 'all']) }}" class="text-decoration-none d-block card-hover-lift">
                    <div class="card border-0 radius-xl" style="border-left: 4px solid #5F63F2 !important;">
                        <div class="card-body d-flex align-items-center gap-3 py-20 px-25">
                            <div class="wh-50 bg-opacity-primary-20 content-center rounded-circle flex-shrink-0">
                                <i class="uil uil-clipboard-alt fs-22 color-primary"></i>
                            </div>
                            <div>
                                <h3 class="mb-0 fw-700">{{ $todayTotal }}</h3>
                                <span class="fs-13 color-gray">ตรวจทั้งหมดวันนี้</span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-4 mb-3">
                <a href="{{ route('company.vehicles.inform', ['start_date' => now()->format('Y-m-d'), 'end_date' => now()->format('Y-m-d'), 'filter' => 'passed']) }}" class="text-decoration-none d-block card-hover-lift">
                    <div class="card border-0 radius-xl" style="border-left: 4px solid #36AE7C !important;">
                        <div class="card-body d-flex align-items-center gap-3 py-20 px-25">
                            <div class="wh-50 bg-opacity-success-20 content-center rounded-circle flex-shrink-0">
                                <i class="uil uil-check-circle fs-22 color-success"></i>
                            </div>
                            <div>
                                <h3 class="mb-0 fw-700">{{ $todayPass }}</h3>
                                <span class="fs-13 color-gray">ผ่านวันนี้</span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-4 mb-3">
                <a href="{{ route('company.vehicles.inform', ['start_date' => now()->format('Y-m-d'), 'end_date' => now()->format('Y-m-d'), 'filter' => 'failed']) }}" class="text-decoration-none d-block card-hover-lift">
                    <div class="card border-0 radius-xl" style="border-left: 4px solid #EB5353 !important;">
                        <div class="card-body d-flex align-items-center gap-3 py-20 px-25">
                            <div class="wh-50 bg-opacity-danger-20 content-center rounded-circle flex-shrink-0">
                                <i class="uil uil-times-circle fs-22 color-danger"></i>
                            </div>
                            <div>
                                <h3 class="mb-0 fw-700">{{ $todayFail }}</h3>
                                <span class="fs-13 color-gray">ไม่ผ่านวันนี้</span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            {{-- Bar Chart 7 วัน --}}
            <div class="col-12">
                <div class="card border-0 radius-xl">
                    <div class="card-header">
                        <span class="fs-18 fw-bold text-dark mb-0">
                            <i class="uil uil-chart-bar me-2 color-primary"></i>ข้อมูลการตรวจรถ 7 วันย้อนหลัง
                        </span>
                    </div>
                    <div class="card-body">
                        <div id="daily-inspection-chart"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="pt-4 pb-4">
            <div class="row mb-3">

                {{-- Card: รถที่ลงทะเบียน (Blue pastel) --}}
                <div class="col-md-4 mb-3">
                    <a href="{{ route('company.vehicles.list') }}" class="text-decoration-none d-block stat-card-link">
                        <div class="radius-xl p-25 stat-card"
                            style="background:#EFF6FF; border-top:4px solid #3B82F6;
                                   box-shadow:0 2px 12px rgba(59,130,246,.10); transition:all .2s;">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="content-center rounded-circle flex-shrink-0"
                                    style="width:52px;height:52px;background:#DBEAFE;">
                                    <img class="svg" src="{{ asset('assets/img/svg/truck.svg') }}" alt=""
                                        style="width:26px;height:26px;filter:invert(33%) sepia(90%) saturate(800%) hue-rotate(200deg);">
                                </div>
                                <div>
                                    <div class="fw-bold" style="font-size:28px;color:#1D4ED8;line-height:1.1;">{{ $totalVehicles ?? 0 }}</div>
                                    <div class="fw-bold" style="font-size:18px;color:#2d63bb;">รถที่ลงทะเบียน</div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-1" style="font-size:18px;color:#365f91;">
                                <i class="uil uil-arrow-right"></i> ดูรายการรถทั้งหมด
                            </div>
                        </div>
                    </a>
                </div>

                {{-- Card: Supply (Green pastel) --}}
                <div class="col-md-4 mb-3">
                    <a href="{{ route('company.supplies.index') }}" class="text-decoration-none d-block stat-card-link">
                        <div class="radius-xl p-25 stat-card"
                            style="background:#F0FDF4; border-top:4px solid #22C55E;
                                   box-shadow:0 2px 12px rgba(34,197,94,.10); transition:all .2s;">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="content-center rounded-circle flex-shrink-0"
                                    style="width:52px;height:52px;background:#DCFCE7;">
                                    <img class="svg" src="{{ asset('assets/img/svg/building.svg') }}" alt=""
                                        style="width:26px;height:26px;filter:invert(40%) sepia(80%) saturate(500%) hue-rotate(100deg);">
                                </div>
                                <div>
                                    <div class="fw-bold" style="font-size:28px;color:#10612e;line-height:1.1;">{{ $supplyCount ?? 0 }}</div>
                                    <div class="fw-bold" style="font-size:18px;color:#188a41;">บริษัทในเครือ (Supply)</div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-1" style="font-size:18px;color:#2e8b51;">
                                <i class="uil uil-arrow-right"></i> ดูรายการ Supply ทั้งหมด
                            </div>
                        </div>
                    </a>
                </div>

                {{-- Card: พนักงานขับรถ (Amber pastel) --}}
                <div class="col-md-4 mb-3">
                    <a href="{{ route('drivers.index') }}" class="text-decoration-none d-block stat-card-link">
                        <div class="radius-xl p-25 stat-card"
                            style="background:#FFFBEB; border-top:4px solid #F59E0B;
                                   box-shadow:0 2px 12px rgba(245,158,11,.10); transition:all .2s;">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="content-center rounded-circle flex-shrink-0"
                                    style="width:52px;height:52px;background:#FEF3C7;">
                                    <img class="svg" src="{{ asset('assets/img/svg/users-01.svg') }}" alt=""
                                        style="width:26px;height:26px;filter:invert(55%) sepia(90%) saturate(600%) hue-rotate(5deg);">
                                </div>
                                <div>
                                    <div class="fw-bold" style="font-size:28px;color:#B45309;line-height:1.1;">{{ $driverCount ?? 0 }}</div>
                                    <div class="fw-bold" style="font-size:18px;color:#b9790a;">พนักงานขับรถ</div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-1" style="font-size:18px;color:#99802f;">
                                <i class="uil uil-arrow-right"></i> ดูรายการพนักงานทั้งหมด
                            </div>
                        </div>
                    </a>
                </div>

            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card mb-25 border-0 radius-xl">

                        <div class="card-header border-bottom">
                            <h6 class="mb-0">Developed by</h6>
                        </div>
                        <div class="card-body p-25">
                            <div class="d-flex align-items-center mb-3">

                                <img src="{{ asset('iddrives.png') }}" alt="Logo"
                                    class="img-thumbnail me-3 border-0"
                                    style="max-height: 80px; box-shadow: 0 0 5px rgba(0,0,0,0.1);">

                                <img src="{{ asset('tz.png') }}" alt="Logo" class="img-thumbnail me-3 border-0"
                                    style="max-height: 80px; box-shadow: 0 0 5px rgba(0,0,0,0.1);">

                                <img src="{{ asset('id_inspection.png') }}" alt="Logo"
                                    class="img-thumbnail me-3 border-0"
                                    style="max-height: 80px; box-shadow: 0 0 5px rgba(0,0,0,0.1);">

                            </div>

                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>
@endsection

@push('styles')
<style>
    .card-hover-lift .card {
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .card-hover-lift:hover .card {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12) !important;
    }
    .stat-card-link:hover .stat-card {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0,0,0,.13) !important;
    }
    .stat-card { cursor: pointer; }
</style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passData = {{ $passCount }};
            const failData = {{ $failCount }};
            const totalInspected = "{{ $totalInspected }}";

            const chartSeries = (passData === 0 && failData === 0) ? [0, 0] : [passData, failData];

            const vehicleChart = new ApexCharts(document.getElementById('vehicle-chart'), {
                chart: {
                    type: 'donut',
                    width: 210,
                    height: 210
                },
                series: chartSeries,
                labels: ['ผ่าน', 'ไม่ผ่าน'],
                colors: ['#36AE7C', '#EB5353'],
                stroke: {
                    show: true,
                    width: 3,
                    colors: ['#fff']
                },
                legend: {
                    show: false
                },
                dataLabels: {
                    enabled: false
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '68%',
                            labels: {
                                show: true,                               
                                value: {
                                    show: true,
                                    fontSize: '22px',
                                    fontWeight: 700,
                                    color: '#272B41',
                                    offsetY: 6,
                                    formatter: val => val
                                },
                                total: {
                                    show: true,
                                    showAlways: true,
                                    label: 'ตรวจแล้ว',
                                    fontSize: '16px',
                                    fontWeight: 400,
                                    color: '#9299B8',
                                    formatter: () => totalInspected
                                }
                            }
                        }
                    }
                },
                tooltip: {
                    y: {
                        formatter: val => val + ' คัน'
                    }
                }
            });
            vehicleChart.render();

            const driverChart = new ApexCharts(document.getElementById('driver-chart'), {
                chart: {
                    type: 'donut',
                    width: 210,
                    height: 210
                },
                series: [{{ $certDriverIds }}, {{ $noCertDrivers }}],
                labels: ['ผ่านการอบรม', 'ไม่มีข้อมูล'],
                colors: ['#36AE7C', '#EB5353'],
                stroke: {
                    show: true,
                    width: 3,
                    colors: ['#fff']
                },
                legend: {
                    show: false
                },
                dataLabels: {
                    enabled: false
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '68%',
                            labels: {
                                show: true,                               
                                value: {
                                    show: true,
                                    fontSize: '22px',
                                    fontWeight: 700,
                                    color: '#272B41',
                                    offsetY: 6,
                                    formatter: val => val
                                },
                                total: {
                                    show: true,
                                    showAlways: true,
                                    label: 'ทั้งหมด',
                                    fontSize: '16px',
                                    fontWeight: 400,
                                    color: '#9299B8',
                                    formatter: () => '{{ $totalDrivers }}'
                                }
                            }
                        }
                    }
                },
                tooltip: {
                    y: {
                        formatter: val => val + ' คน'
                    }
                }
            });
            driverChart.render();

            // ── Bar Chart รายวัน ──
            const dailyLabels = @json($dailyLabels);
            const dailyPass = @json($dailyPass);
            const dailyFail = @json($dailyFail);

            const dailyChart = new ApexCharts(document.getElementById('daily-inspection-chart'), {
                chart: {
                    type: 'bar',
                    height: 280,
                    stacked: true,
                    toolbar: {
                        show: false
                    }
                },
                series: [{
                        name: 'ผ่าน',
                        data: dailyPass
                    },
                    {
                        name: 'ไม่ผ่าน',
                        data: dailyFail
                    }
                ],
                colors: ['#36AE7C', '#EB5353'],
                xaxis: {
                    categories: dailyLabels,
                    labels: {
                        style: {
                            fontSize: '13px'
                        }
                    }
                },
                yaxis: {
                    title: {
                        text: 'จำนวน (คัน)'
                    },
                    min: 0,
                    forceNiceScale: true,
                    labels: {
                        formatter: v => Math.round(v)
                    }
                },
                legend: {
                    position: 'top'
                },
                dataLabels: {
                    enabled: false
                },
                tooltip: {
                    y: {
                        formatter: val => val + ' คัน'
                    }
                },
                plotOptions: {
                    bar: {
                        borderRadius: 4,
                        columnWidth: '45%'
                    }
                }
            });
            dailyChart.render();

        });
    </script>
@endpush
