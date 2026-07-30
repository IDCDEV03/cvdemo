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

        .fixed-table {
            table-layout: fixed;
            width: 100%;
        }

        .custom-table thead {
            background-color: #20d185;
            /* เขียวพาสเทลแบบ custom */
        }

        .table.table-bordered th,
        .table.table-bordered td {
            border: 1px solid #000 !important;
            /* บังคับให้เป็นสีดำ */
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

                                <a href="{{ route('form_image8', ['rec' => request()->rec]) }}"
                                    class="btn btn-sm btn-outline-secondary">รูปถ่ายประเมินรอบคัน</a>
                                <a href="{{ route('form_imagefail', ['rec' => request()->rec]) }}"
                                    class="btn btn-sm btn-outline-danger">รูปถ่ายรถที่ต้องแก้ไข</a>
                                <button class="btn btn-outline-primary btn-sm" onclick="window.print()">
                                    <i class="fas fa-print"></i> พิมพ์
                                </button>
                            </div>
                        </div>
                    </div>

                    @php
                        $ins_birth = $inspector_data->ins_birthyear;

                        $year_en = $ins_birth - 543;
                        $ins_age = date('Y') - $year_en;
                        $userdata = DB::table('users')
                            ->where('user_id', $record->chk_user)
                            ->select('users.prefix', 'users.name', 'users.lastname', 'users.signature_image')
                            ->first();

                        $fullname = $userdata->prefix . $userdata->name . ' ' . $userdata->lastname;
                    @endphp
                    <!-- print-->
                    <div id="print-area">
                        <div class="card mb-2 ">
                            <div class="card-body">
                                <table class="table table-bordered report-table ">
                                    <tr>
                                        <td width='20%' class="text-center">
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
                                        <td style="background-color: #20d185;">
                                            <span class="fw-bold">ยี่ห้อ</span>
                                        </td>
                                        <td>
                                            {{ $record->car_brand }}
                                        </td>
                                        <td style="background-color: #20d185;" class="fw-bold">รุ่นรถ</td>
                                        <td>  {{ blank($record->car_model) ? '-' : $record->car_model }}</td>
                                    </tr>
                                    <tr>
                                        <td style="background-color: #20d185;">
                                            <span class="fw-bold">ทะเบียน</span>
                                        </td>
                                        <td>
                                            {{ $record->car_plate }}
                                        </td>
                                        <td style="background-color: #20d185;" class="fw-bold">หมายเลขข้างรถ</td>
                                        <td> {{ $record->car_number_record }} </td>
                                    </tr>

                                    </tr>
                                    <tr>
                                        <td style="background-color: #20d185;">
                                            <span class="fw-bold">ปีที่จดทะเบียน</span>
                                        </td>
                                        <td>
                                     {{ blank($record->car_age) ? '-' : $record->car_age }}
                                        </td>
                                        <td style="background-color: #20d185;" class="fw-bold">บริษัทผู้ขนส่ง</td>
                                        <td> บริษัท ไอดีไดรฟ์ จำกัด </td>
                                    </tr>

                                </table>
                                <table class="table custom-table table-bordered fixed-table report-table">
                                    @foreach ($categories as $cat)
                                        <thead>
                                            <tr>
                                                <th>
                                                   รายการตรวจประเมิน {{ $cat->cates_no }}. {{ $cat->chk_cats_name }} 
                                                </th>
                                                <th>ผลการประเมิน</th>
                                                <th>สิ่งที่ตรวจพบ</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            @foreach ($results[$cat->category_id] ?? [] as $r)
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
                                                        @elseif($r->result_value == '3')
                                                            <span class="text-muted"> ไม่เกี่ยวข้อง
                                                            </span>
                                                        @else
                                                            <span>
                                                                {{ $r->result_value }}
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $r->user_comment }}</td>

                                                </tr>
                                            @endforeach
                                        </tbody>
                                    @endforeach
                                </table>
                                @php
                                    $get = fn($id, $field = 'result_value') => optional($rr->get($id))->{$field};
                                    $date_th = $get(43);
                                @endphp

                                <table class="table table-borderless fixed-table report-table">
                                    <tr>
                                        <td colspan="4">ข้อเสนอแนะ : {{ $get(42) ?? '-' }} </td>
                                      
                                    </tr>
                                    <tr>
                                        <td>ตรวจสอบวันที่ : {{ thai_date($date_th) ?? '-' }}</td>
                                        <td>เวลา : {{ $get(44) ?? '-' }} </td>
                                        <td>
                                            <!--signature-->
                                            @if (empty($userdata->signature_image))
                                                <div class="text-center text-dark ">
                                                    .................................................</div>
                                            @else
                                                <div class="text-center"><img src="{{ asset($userdata->signature_image) }}"
                                                        width="150px" alt=""></div>
                                                <div class="text-center">..........................................</div>
                                            @endif
                                            <div class="text-center text-dark fs-14 mt-2">ผู้ตรวจสอบ {{ $fullname }}
                                            </div>
                                        </td>
                                        <td >หน่วยงาน : {{ $get(46) ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="4">สถานที่ตรวจสอบ : {{ $get(45) ?? '-' }}</td>
                                    </tr>

                                </table>




                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @endsection
