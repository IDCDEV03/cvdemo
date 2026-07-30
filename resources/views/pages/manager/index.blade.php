@section('title', 'ระบบตรวจมาตรฐานรถ')
@section('description', 'ID Drives')
@extends('layout.app')
@section('content')
    <div class="container-fluid">
        <div class="social-dash-wrap">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb-main">
                        <label class="fs-20 fw-bold text-dark breadcrumb-title"> 
                            @auth
                            {{Auth::user()->name}}
                        @endauth </label>

                    </div>
                </div>
            </div>


               <div class="row">
                        <div class="col-md-6">
                            <a href="{{route('manager.company_regis')}}" class="text-decoration-none">
                                <div class="card card-md border-2 card-bordered card-default text-center"
                                    style=" border-color: #FFA673;background-color: #fff0e7;">
                                    <div class="card-body">
                                        <div class="mb-3">
                                           <img src="{{asset('company.png')}}" alt="" width="120px">
                                        </div>
                                        <span class="fs-24 fw-bold text-dark mb-1">ลงทะเบียนบริษัท</span>
                                        <p class="fs-20 text-muted mt-1">คลิกเพื่อกรอกข้อมูลทะเบียนบริษัท</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>

 <div class="border-top border-light my-4"></div>
****
        </div>
    </div>
@endsection

@push('scripts')
    <!-- DataTables  -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#forms-table').DataTable({
                responsive: true,
                pageLength: 25,
                language: {
                    search: "ค้นหา:",
                    lengthMenu: "แสดง _MENU_ รายการ",
                    info: "แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ",
                    paginate: {
                        next: "ถัดไป",
                        previous: "ก่อนหน้า"
                    }
                }
            });
        });
    </script>
@endpush
