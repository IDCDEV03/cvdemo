@section('title', 'ระบบปฏิบัติการพนักงานขับรถราชการ')
@section('description', 'ID Drives')
@extends('layout.app')
@section('content')
    <style>
        @media print {
            body * {
                visibility: hidden !important;
            }

            #print-area,
            #print-area * {
                visibility: visible !important;
            }

            #print-area {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                background: white;
                padding: 30px;
                z-index: 9999;
            }

            .d-print-none {
                display: none !important;
            }
        }
    </style>

    <div class="container-fluid">

        <div class="row mt-4">
            <div class="col-md-12">


                <div class="d-flex justify-content-between mb-3">
                    <label class="fs-24 fw-bold">หนังสือแจ้งซ่อมยานพาหนะ</label>
                    <button onclick="window.print()" class="btn btn-sm btn-outline-primary d-print-none">
                        <i class="fas fa-print"></i> พิมพ์
                    </button>
                </div>

                <div id="print-area">
                    <div class="border p-4 mb-25" style="background: white;">
                        {{-- หัวกระดาษ --}}
                        <div class="text-center mb-4">
                            <img src="{{ asset('logo//20250602_161040_1.png') }}" alt="logo" style="height: 80px;">
                            <p class="fs-20 fw-bold mb-0 mt-2 text-dark">สำนักงานขนส่งจังหวัดพัทลุง</p>
                            <small>123 หมู่ 4 ตำบลตัวอย่าง อำเภอตัวอย่าง จังหวัดตัวอย่าง 30000</small>
                        </div>

                        <div class="border-top my-3"></div>

                        {{-- ข้อมูลทั่วไป --}}
                        <p><strong>วันที่แจ้ง:</strong> 25 มีนาคม 2568</p>
                        <p><strong>หน่วยงานที่แจ้ง:</strong> กองช่าง</p>
                        <p><strong>ชื่อผู้แจ้ง:</strong> นายสมศักดิ์ ใจดี (ผู้ใช้ยานพาหนะ)</p>

                        <div class="border-top my-3"></div>

                        {{-- รายละเอียดรถ --}}
                        <h6 class="fw-bold">รายละเอียดยานพาหนะ</h6>
                        <table class="table table-bordered w-100">
                            <tbody>
                                <tr>
                                    <td style="width: 30%;">ทะเบียนรถ</td>
                                    <td>กข 1234 กรุงเทพมหานคร</td>
                                </tr>
                                <tr>
                                    <td>ประเภทรถ</td>
                                    <td>รถกระบะ</td>
                                </tr>
                                <tr>
                                    <td>หมายเลขครุภัณฑ์</td>
                                    <td>ทะเบียนทรัพย์สิน 5002-001-0001</td>
                                </tr>
                            </tbody>
                        </table>

                        {{-- รายการแจ้งซ่อม --}}
                        <h6 class="fw-bold mt-4">รายการที่แจ้งซ่อม</h6>
                        <ul>
                            <li>ระบบเบรกมีเสียงดังขณะใช้งาน</li>
                            <li>ไฟหน้าซ้ายไม่ติด</li>
                        </ul>

                        {{-- ความคิดเห็น --}}
                        <div class="mt-3">
                            <strong>รายละเอียดเพิ่มเติม:</strong>
                            <p>โปรดดำเนินการซ่อมแซมโดยด่วน เนื่องจากรถใช้ในภารกิจฉุกเฉินของอบต.</p>
                        </div>

                        {{-- ช่องเซ็นชื่อ --}}
                        <div class="row mt-5">
                            <div class="col-6 text-center">
                                ..................................................<br>
                                <strong>(นายสมศักดิ์ ใจดี)</strong><br>
                                ผู้แจ้งซ่อม
                            </div>
                            <div class="col-6 text-center">
                                ..................................................<br>
                                <strong>(นางสาววิภาดา อนุมัติ)</strong><br>
                                ผู้อนุมัติการซ่อม
                            </div>
                        </div>

                        {{-- ช่องเซ็นชื่อเพิ่มเติม --}}
                        <div class="row mt-5">
                            <div class="col-12 text-center">
                                ..................................................<br>
                                <strong>(นายประเสริฐ ช่างใหญ่)</strong><br>
                                เจ้าหน้าที่ตรวจสอบสภาพ
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
