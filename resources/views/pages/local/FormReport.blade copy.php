@section('title', 'ระบบปฏิบัติการพนักงานขับรถราชการ')
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
                                <table class="table table-bordered report-table">
                                    <tr>
                                        <td>
                                            <img src="{{ asset('logo/' . $company_datas->company_logo) }}" width="150px"
                                                alt="">
                                        </td>
                                        <td colspan="2">
                                            <span class="fw-bold fs-20 "> {{ $company_datas->company_name }}</span>
                                        </td>
                                        <td>
                                            {{ $forms->form_code }}
                                            <br>
                                            วันที่ตรวจ {{ thai_datetime($record->date_check) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="table-light"><strong>บันทึก (Record Form) :</strong>
                                            {{ $forms->form_name }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="4"><strong>ส่วนที่ 1</strong> ข้อมูลเบื้องต้น</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">ชื่อผู้ขับ</td>
                                        <td> {{ $inspector_data->ins_prefix }} {{ $inspector_data->ins_name }}
                                            {{ $inspector_data->ins_lastname }}
                                        </td>
                                        <td class="fw-bold">อายุ</td>
                                        <td>{{ $ins_age }} ปี</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">ประสบการณ์ผู้ขับขี่</td>
                                        <td>{{ $inspector_data->ins_experience }} ปี</td>
                                        <td class="fw-bold">หน่วยงาน/สังกัด</td>
                                        <td>{{ $agent_name->name }}</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <span class="fw-bold">ยี่ห้อ</span>
                                        </td>
                                        <td>
                                            {{ $record->car_brand }}
                                        </td>
                                        <td class="fw-bold">รุ่น</td>
                                        <td> {{ $record->car_model }} </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <span class="fw-bold">ทะเบียน</span>
                                        </td>
                                        <td>
                                            {{ $record->car_plate }}
                                        </td>
                                        <td class="fw-bold">หมายเลขรถ</td>
                                        <td> {{ $record->car_number_record }} </td>
                                    </tr>

                                    </tr>
                                    <tr>
                                        <td>
                                            <span class="fw-bold">อายุการใช้งาน</span>
                                        </td>
                                        <td>
                                            {{ $record->car_age }} ปี
                                        </td>
                                        <td class="fw-bold">ป้ายภาษีประจำปี</td>
                                        <td> {{ $record->car_tax }} </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <span class="fw-bold">ประกันภัย</span>
                                        </td>
                                        <td>
                                            {{ $record->car_insure }}
                                        </td>
                                    </tr>

                                    <tr>
                                        <td colspan="4" class="table-light"><strong>ส่วนที่ 2</strong> รายงาน
                                            {{ $forms->form_name }}</td>

                                    </tr>
                                    <tr>
                                        <td colspan="4">ประเภทรถ {{ $record->veh_type_name }}</td>
                                    </tr>

                                </table>




                                @foreach ($categories as $cat)
                                    <span class="fs-18 fw-bold mt-4">{{ $cat->cates_no }}.
                                        {{ $cat->chk_cats_name }}</span>

                                    <table class="table table-bordered fixed-table mt-2 mb-4 report-table">
                                        <thead>
                                            <tr>
                                                <th>รายการตรวจประเมิน</th>
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
                                                            ปกติ
                                                        @elseif($r->result_value == '0')
                                                            <span class="text-danger">ไม่สามารถใช้งานได้</span>
                                                        @elseif($r->result_value == '2')
                                                            <span class="text-secondary"> ไม่ปกติ แต่ยังสามารถใช้งานได้
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $r->user_comment }}</td>

                                                </tr>

                                                @if (isset($images[$r->item_id]))
                                                    <tr>
                                                        <td colspan="3" class="text-center">
                                                            @foreach ($images[$r->item_id] as $img)
                                                                <img src="{{ asset($img->image_path) }}"
                                                                    class="img-thumbnail" width="200px" alt="">
                                                            @endforeach
                                                        </td>
                                                @endif
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @endforeach

                                <table class="table table-bordered report-table">
                                    <tr>
                                        <td colspan="3" class="table-light" style="width: 50%;"><strong>ส่วนที่
                                                3</strong> สภาพรถปัจจุบัน</td>
                                        <td class="table-light"><strong>ส่วนที่ 4</strong> ผลการตรวจสอบ</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <img src="{{ asset($record->img_front) }}" alt="" width="150px">
                                        </td>
                                        <td>
                                            <img src="{{ asset($record->img_beside) }}" alt="" width="150px">
                                        </td>
                                        <td>
                                            <img src="{{ asset($record->img_overall) }}" alt="" width="150px">
                                        </td>
                                        <td>

                                            <!--signature-->
                                            @if (empty($userdata->signature_image))
                                                <div class="text-center text-dark mt-40">
                                                    .................................................</div>
                                            @else
                                                <div class="text-center"><img src="{{ asset($userdata->signature_image) }}"
                                                        width="150px" alt=""></div>
                                                <div class="text-center">..........................................</div>
                                            @endif
                                            <div class="text-center text-dark fs-18 mt-2">({{ $fullname }})</div>
                                            <div class="text-center text-dark fs-18 mt-2">ผู้ตรวจรถ</div>


                                        </td>
                                    </tr>
                                </table>

                                <div class="text-end text-dark fs-14 mt-2">{{ thai_datetime($record->date_check) }}</div>


                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @endsection
