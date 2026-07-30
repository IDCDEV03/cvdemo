@section('title', 'ระบบตรวจมาตรฐานรถ')
@section('description', 'ID Drives')
@extends('layout.app')
@section('content')
    <style>
        @media print {
            body * {
                visibility: hidden;
            }

            .card {
                border: none !important;
                box-shadow: none !important;
            }

            #print-area,
            #print-area * {
                visibility: visible;
            }

            #print-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
        }
    </style>
    <style>
        .fixed-table {
            table-layout: fixed;
            width: 100%;
        }

        .fixed-table th,
        .fixed-table td {
            word-wrap: break-word;
            vertical-align: middle;
        }

        .fixed-table th:nth-child(1),
        .fixed-table td:nth-child(1) {
            width: 40%;
        }

        .fixed-table th:nth-child(2),
        .fixed-table td:nth-child(2) {
            width: 30%;
        }

        .fixed-table th:nth-child(3),
        .fixed-table td:nth-child(3) {
            width: 30%;
        }

        table.report-table {
            font-size: 14px;
            line-height: 1.4;
        }

        table.report-table th,
        table.report-table td {
            padding: 4px 6px;
        }
    </style>

    <div class="container-fluid">
        <div class="social-dash-wrap">

            <div class="row mt-4">
                <div class="col-md-12">
                    <div class=" alert alert-primary " role="alert">
                        <div class="alert-content">
                            <span class="fs-20 fw-bold"> {{ $forms->form_name }} </span>
                        </div>
                    </div>

                    <div class="card mb-2 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="mb-0 fs-20 fw-bold">รายงานการตรวจรถ</span>
                                <a href="{{ route('form_report', ['rec' => request()->rec]) }}"
                                    class="btn btn-sm btn-outline-success">รายงานผลการประเมิน</a>
                                          <a href="{{route('form_image8',['rec'=>request()->rec])}}" class="btn btn-sm btn-outline-secondary">รูปถ่ายประเมินรอบคัน</a>
                             <a href="{{route('form_imagefail',['rec'=>request()->rec])}}" class="btn btn-sm btn-outline-danger">รูปถ่ายรถที่ต้องแก้ไข</a>
                                <button class="btn btn-outline-primary btn-sm" onclick="window.print()">
                                    <i class="fas fa-print"></i> พิมพ์
                                </button>
                            </div>
                        </div>
                    </div>


                    <!-- print-->
                    <div id="print-area">
                        <div class="card mb-2 ">
                            <div class="card-body">
                                <table class="table table-bordered report-table">
                                    <tr>
                                        <td width='20%'>
                                            <img src="{{ asset($company_datas->company_logo) }}" width="180px"
                                                alt="">
                                        </td>
                                        <td colspan="2" class="text-center">
                                            <span class="fw-bold fs-20 "> {{ $company_datas->company_name }}</span>
                                            <br>
                                            <span class="fs-16"> {{ $forms->form_name }}</span>
                                        </td>
                                        <td width='20%' class="text-end">
                                            {{ $forms->form_code }}

                                        </td>
                                    </tr>
                              
                                    <tr>
                                        <td colspan="4" style="background-color: #20d185;"><strong>ข้อมูลเบื้องต้น</strong></td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <span class="fw-bold">ยี่ห้อ</span>
                                        </td>
                                        <td>
                                            {{ $record->car_brand }}
                                        </td>
                                        <td class="fw-bold">รุ่นรถ</td>
                                        <td> {{ blank($record->car_model) ? '-' : $record->car_model }} </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <span class="fw-bold">ทะเบียน</span>
                                        </td>
                                        <td>
                                            {{ $record->car_plate }}
                                        </td>
                                        <td class="fw-bold">หมายเลขข้างรถ</td>
                                        <td> {{ $record->car_number_record }} </td>
                                    </tr>

                                    </tr>
                                    <tr>
                                        <td>
                                            <span class="fw-bold">ปีที่จดทะเบียน</span>
                                        </td>
                                        <td>
                                          {{ blank($record->car_age) ? '-' : $record->car_age }}
                                        </td>
                                        <td class="fw-bold">บริษัทผู้ขนส่ง</td>
                                        <td> บริษัท ไอดีไดรฟ์ จำกัด </td>
                                    </tr>

                                </table>
<label class="fw-bold fs-16">ภาพถ่ายรถโม่ที่ต้องแก้ไข</label>
                                <table class="table table-bordered fixed-table mt-2 mb-4 report-table">
                                    <thead>
                                        <tr>
                                            <th>รายการตรวจประเมิน</th>
                                            <th>ผลการประเมิน</th>
                                            <th>สิ่งที่ตรวจพบ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($results ?? [] as $category_id => $items)
                                            @foreach ($items as $r)
                                                <tr>
                                                    <td class="text-left">{{ $r->item_name }}</td>
                                                    <td>
                                                       @if ($r->result_value == '1')
                                                            ผ่าน
                                                        @elseif($r->result_value == '0')
                                                            <span class="text-danger">ไม่ผ่าน</span>
                                                        @elseif($r->result_value == '2')
                                                            <span class="text-secondary"> ผ่าน แต่ต้องแก้ไขปรับปรุง
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $r->user_comment }}</td>
                                                </tr>

                                                @if (!empty($images[$r->item_id]))
                                                    <tr>
                                                        <td colspan="3" class="text-center">
                                                            @foreach ($images[$r->item_id] as $img)
                                                                <img src="{{ asset($img->image_path) }}"
                                                                    class="img-thumbnail" width="200px" alt="">
                                                            @endforeach
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
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
