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
                                       <a href="{{route('form_report',['rec'=>request()->rec])}}" class="btn btn-sm btn-outline-secondary">รายงานผลการประเมิน</a>
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
                                            <span class="fs-16">  {{ $forms->form_name }}</span>
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
                                        <td>  บริษัท ไอดีไดรฟ์ จำกัด </td>
                                    </tr>
                               
                                </table>

                                 <table class="table table-bordered report-table">
                                    <tr  class="text-center">
                                        <td>
                                            <img src="{{asset('upload/vehicle_images/'.$image8->image1)}}" width="300px" alt="" class="img-fluid">
                                             <br>
                                            <label class="mt-2">1. ด้านหน้ารถ</label>
                                        </td>
                                         <td> <img src="{{asset('upload/vehicle_images/'.$image8->image2)}}"  width="300px" alt="" class="img-fluid">
                                             <br>
                                         <label class="mt-2">2. หลังรถเยื้องไปทางซ้าย</label>
                                        </td>
                                          <td>
                                             <img src="{{asset('upload/vehicle_images/'.$image8->image3)}}"  width="300px" alt="" class="img-fluid">
                                              <br>
                                              <label class="mt-2">3. หลังรถเยื้องไปทางขวา</label>
                                          </td>
                                           <td>
                                             <img src="{{asset('upload/vehicle_images/'.$image8->image4)}}"  width="300px" alt="" class="img-fluid">
                                              <br>
                                              <label class="mt-2">4. ด้านหลังรถเฟืองท้าย</label>
                                           </td>
                                    </tr>
                                    <tr  class="text-center">
                                        <td> 
                                            <img src="{{asset('upload/vehicle_images/'.$image8->image5)}}"  width="300px" alt="" class="img-fluid">
                                             <br>
                                             <label class="mt-2">5. ในห้องโดยสารฝั่งคนขับ</label>
                                        </td>
                                         <td>
                                             <img src="{{asset('upload/vehicle_images/'.$image8->image6)}}"  width="300px" alt="" class="img-fluid">
                                              <br>
                                              <label class="mt-2">6. เฟืองเกียร์หมุนโม่</label>
                                         </td>
                                          <td>
                                             <img src="{{asset('upload/vehicle_images/'.$image8->image7)}}"  width="300px" alt="" class="img-fluid">
                                              <br>
                                              <label class="mt-2">7. ลูกหมากคันชักคันส่ง</label>
                                          </td>
                                           <td>
                                             <img src="{{asset('upload/vehicle_images/'.$image8->image8)}}"  width="300px" alt="" class="img-fluid">
                                             <br>
                                              <label class="mt-2">8. เพลาส่งกำลัง</label>
                                           </td>
                                    </tr>
                                 </table>


                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @endsection
