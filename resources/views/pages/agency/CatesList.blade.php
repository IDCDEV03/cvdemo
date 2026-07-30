@section('title', 'ระบบ E-Checker')
@section('description', 'ID Drives')
@extends('layout.app')
@section('content')
    <div class="container-fluid">
        <div class="social-dash-wrap">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb-main">
                        <h4 class="text-capitalize breadcrumb-title">รายการหมวดหมู่</h4>

                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class=" alert alert-info " role="alert">
                        <div class="alert-content">
                            <span class="fs-20 fw-bold">ชื่อฟอร์ม : {{ $form_name->form_name }} </span>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">

                            <table class="table table-bordered" id="forms-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>ชื่อหมวดหมู่</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data as $item)
                                        <tr>
                                            <td>{{ $item->cates_no }}</td>
                                            <td>
                                                <a href="{{route('agency.cates_detail',['cates_id'=>$item->category_id])}}">
                                                {{ $item->chk_cats_name }}
                                                </a>
                                            </td>
                                            <td>
                                                <div class="btn-group dm-button-group btn-group-normal my-2" role="group">
                                                   
                                                    <a href="#" class="btn btn-primary btn-sm btn-squared btn-transparent-primary ">แก้ไขชื่อหมวดหมู่</a>
                                                     <a href="#" class="btn btn-info btn-sm btn-squared btn-transparent-info ">เพิ่มข้อตรวจ
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
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
                pageLength: 10,
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
