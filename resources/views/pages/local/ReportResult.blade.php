@section('title', 'ระบบปฏิบัติการพนักงานขับรถราชการ')
@section('description', 'ID Drives')
@extends('layout.app')
@section('content')
    <style>
        @media print {
            body * {
                visibility: hidden;
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
                    <!-- print-->
                    <div id="print-area">
                        <div class="card mb-25">
                            <div class="card-body">

                                @php
                                    $userdata = DB::table('users')
                                        ->where('user_id', $record->chk_user)
                                        ->select('users.prefix', 'users.name', 'users.lastname','users.signature_image')
                                        ->first();

                                    $fullname = $userdata->prefix . $userdata->name . ' ' . $userdata->lastname;
                                @endphp

                                @if (!empty($agent_name->logo_agency))
                                    <table class="table table-borderless">
                                        <tr>
                                            <td colspan="4" class="text-center">
                                                <img src="{{ asset($agent_name->logo_agency) }}" alt=""
                                                    width="100px">
                                            </td>
                                        </tr>
                                    </table>
                                @endif

                                <table class="table table-bordered">
                                    <tr>
                                        <td class="fw-bold">ทะเบียนรถ</td>
                                        <td>{{ $record->car_plate }} </td>
                                        <td class="fw-bold">แบบฟอร์มตรวจ</td>
                                        <td>{{ $forms->form_name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">ประเภทรถ</td>
                                        <td>{{ $record->veh_type_name }}</td>
                                        <td class="fw-bold">วันที่/เวลาที่ตรวจในระบบ</td>
                                        <td>{{ thai_datetime($record->date_check) }}</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <span class="fw-bold">ตรวจโดย</span>
                                        </td>
                                        <td>
                                            {{ $fullname }}
                                        </td>
                                        <td class="fw-bold">สังกัด</td>
                                        <td> {{ $agent_name->name }} </td>
                                    </tr>

                                </table>

                                <div class="my-4 text-center">

                                </div>

                                @foreach ($categories as $cat)
                                    <span class="fs-18 fw-bold mt-4">{{ $cat->cates_no }}. {{ $cat->chk_cats_name }}</span>

                                    <table class="table table-bordered fixed-table mt-2 mb-4">
                                        <thead>
                                            <tr>
                                                <th>รายการ</th>
                                                <th>ผลตรวจ</th>
                                                <th>ความคิดเห็น</th>
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

               <!--signature-->
@if (empty($userdata->signature_image))    
 <div class="text-center text-dark mt-40">.................................................</div>    
@else        
 <div class="text-center"><img src="{{asset($userdata->signature_image)}}" width="150px" alt=""></div>
 <div class="text-center">..........................................</div>
@endif
   <div class="text-center text-dark fs-18 mt-2">({{$fullname}})</div>
   <div class="text-center text-dark fs-18 mt-2">ผู้ตรวจรถ</div>
   <div class="text-end text-dark fs-14 mt-2">{{ thai_datetime($record->date_check) }}</div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @endsection
