@section('title', 'ระบบปฏิบัติการพนักงานขับรถราชการ')
@section('description', 'ID Drives')
@extends('layout.LayoutAdmin')
@section('content')

    <div class="container-fluid">
        <div class="social-dash-wrap">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb-main">
                        <span class="fs-24 fw-bold breadcrumb-title">รายการหน่วยงาน</span>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card card-default">
                        
                        <div class="card-body">
  <a href="{{ route('admin.agency.create') }}" class="mb-4 btn btn-secondary btn-default btn-squared btn-transparent-secondary ">
        <i class="fas fa-plus me-1"></i> สร้างหน่วยงานใหม่
    </a>
         <table id="table-data" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>ชื่อหน่วยงาน</th>
                        <th>Email</th>
                        <th>Logo</th>
                        <th>วันที่สร้าง</th>
                        <th>จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($agencies as $index => $agency)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><a href="{{route('admin.agency.show',$agency->id)}}"> {{ $agency->name }}</a></td>
                            <td>{{ $agency->email }}</td>
                            <td>
                                @if($agency->logo_agency)
                                    <img src="{{ asset($agency->logo_agency) }}" alt="โลโก้"
                                         class="img-thumbnail" style="max-height: 60px;">
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ thai_date($agency->created_at) }}</td>
                            <td>
                                <div class="btn-group btn-group-xs" role="group">
                  
                    <a href="{{route('admin.agency.edit',$agency->id)}}" class="btn btn-warning btn-default btn-squared btn-shadow-warning btn-xs" title="แก้ไข">
                        <i class="fas fa-edit"></i> แก้ไข
                    </a>
                

                     <a href="{{route('admin.agency.destroy',$agency->id)}}" class="btn btn-danger btn-default btn-squared btn-shadow-danger btn-xs" onclick="return confirm('ต้องการลบใช่หรือไม่ หากลบแล้วไม่สามารถกู้คืนได้อีก?');">
                                                    <i class="las la-trash-alt"></i> ลบ
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
            $('#table-data').DataTable({
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
