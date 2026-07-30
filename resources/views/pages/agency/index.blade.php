@section('title', 'ระบบตรวจมาตรฐานรถ')
@section('description', 'ID Drives')
@extends('layout.app')
@section('content')
    <div class="container-fluid">

        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb-main">
                    <label class="fs-20 fw-bold text-dark breadcrumb-title"> @auth
                            {{ Auth::user()->name }}
                        @endauth </label>

                </div>
            </div>
        </div>


        <div class="row">
            <!-- รายการบริษัทฯ -->
            <div class="col-md-4 mb-4">
                <a href="{{route('companies.index')}}" class="text-decoration-none">
                    <div class="card shadow-sm" style="border: 2px solid 	#ffd6b5; background-color: #fff1e6;">
                        <div class="card-body text-center">                          
                            <h5 class="card-title">รายการบริษัทฯว่าจ้าง</h5>
                            <p class="card-text text-muted">สร้างและจัดการข้อมูลบริษัทฯว่าจ้าง</p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- รายการบริษัทฯSup -->
            <div class="col-md-4 mb-4">
                <a href="#" class="text-decoration-none">
                    <div class="card shadow-sm " style="border: 2px solid 	#C2B9F0; background-color: #F0EDFF;">
                        <div class="card-body text-center">                           
                            <h5 class="card-title">รายการบริษัทฯ Supply</h5>
                            <p class="card-text text-muted">สร้างและจัดการข้อมูล Supply</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <div class="border-top border-light my-4"></div>

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
