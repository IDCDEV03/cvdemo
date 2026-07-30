@section('title', 'ระบบตรวจมาตรฐานรถ')
@section('description', 'ID Drives')
@extends('layout.app')
@section('content')
    <div class="container-fluid">

        <div class="row">
            <div class="col-md-12">

                <div class="card mt-20 mb-25 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="fw-bold fs-20 ">
                                รายการรถ
                            </div>
                            <a href="{{ route('supply.new_veh') }}" class="btn btn-sm btn-secondary">
                                <i class="fas fa-truck me-1"></i> + ลงทะเบียนรถ
                            </a>
                        </div>
                    </div>
                </div>


                <div class="card shadow-sm">
                    <div class="card-body">

                        <table class="table table-bordered" id="forms-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>หมายเลขข้างรถ</th>
                                    <th>ทะเบียนรถ</th>                                   
                                    <th>รุ่นรถ</th>                                 
                                    <th>สถานะรถ</th>
                                    <th>วันที่เพิ่ม</th>
                                    
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($veh_list as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }} </td>
                                        <td>{{ $item->car_number_record }}</td>    
                                        <td>{{ $item->car_plate }}</td>                                        
                                        <td> {{$item->car_brand}} {{$item->car_model}} </td>
                                      
                                        <td> 
                                            @if ($item->status == '1')
                                                 <label class="badge badge-round badge-success">ปกติ</label>
                                            @elseif($item->status == '2')
                                                 <label class="badge badge-round badge-warning">รอซ่อม</label>
                                                  @elseif($item->status == '0')
                                                 <label class="badge badge-round badge-danger">งดใช้งาน</label>
                                            @endif

                                        </td>
                                        <td> {{thai_date($item->created_at)}} </td>
                                      
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>



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
