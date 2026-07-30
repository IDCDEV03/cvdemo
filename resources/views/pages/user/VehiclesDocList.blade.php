@section('title', 'ระบบปฏิบัติการพนักงานขับรถราชการ')
@section('description', 'ID Drives')
@extends('layout.app')
@section('content')
    <div class="container-fluid">

        <div class="row mt-25">
            <div class="col-md-12">
                @php
                    $forms = [
                        [
                            'title' => 'แบบขออนุญาตใช้รถยนต์ส่วนกลาง',
                            'icon' => 'fas fa-car',
                            'color' => '#FF6F61',
                        ],
                        [
                            'title' => 'แบบขออนุญาตเบิกน้ำมันเชื้อเพลิง',
                            'icon' => 'fas fa-gas-pump',
                            'color' => '#4CAF50',
                        ],
                        [
                            'title' => 'แบบรายงานผลการเดินทาง',
                            'icon' => 'fas fa-route',
                            'color' => '#2196F3',
                        ],
                        [
                            'title' => 'แบบสรุปรายงานการใช้รถประจำเดือน',
                            'icon' => 'fas fa-clipboard-list',
                            'color' => '#FFC107',
                        ],
                        [
                            'title' => 'แบบขออนุมัติไปราชการเข้ารับการฝึกอบรม ใช้พาหนะส่วนตัว',
                            'icon' => 'fas fa-user-tie',
                            'color' => '#9C27B0',
                        ],
                    ];
                @endphp

                <div class="row">
                    @foreach ($forms as $form)
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 shadow-sm" style="border-left: 6px solid {{ $form['color'] }};">
                                <div class="card-body d-flex flex-column justify-content-between">
                                    <div>
                                        <span class="card-title fs-20 fw-bold d-flex align-items-center mb-3">
                                            <i class="{{ $form['icon'] }} me-2" style="color: {{ $form['color'] }};"></i>
                                            {{ $form['title'] }}
                                        </span>
                                    </div>
                                    <div class="mt-4">
                                        <p class="text-muted small">Coming Soon</p>
                                        <div class="text-end mt-3">
                                            <div class="d-flex justify-content-start gap-2 mt-3">
                                                <a href="#" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-plus"></i> สร้าง
                                                </a>
                                                <a href="#" class="btn btn-sm btn-outline-secondary">
                                                    <i class="fas fa-eye"></i> ดูรายละเอียด
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach


                </div>
            </div>
        </div>

    </div>
@endsection
