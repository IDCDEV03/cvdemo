@section('title', 'ระบบปฏิบัติการพนักงานขับรถราชการ')
@section('description', 'ID Drives')
@extends('layout.app')
@section('content')
    <div class="container-fluid">        
        
            <div class="row">
                <div class="col-md-12">

   <div class="card mt-20 mb-25 shadow-sm bg-warning">
                    <div class="card-body ">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="fw-bold fs-20 text-white">
                                รายชื่อเจ้าหน้าที่
                            </div>                          
                            <a href="{{ route('agency.create_account', ['role' => 'user']) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-user-plus me-1"></i> เพิ่มผู้ใช้งาน
                            </a>
                        </div>
                    </div>
                </div>

                    <div class="card">
                        <div class="card-body">

                            <table class="table table-bordered" id="forms-table">
                                <thead>
                                    <tr>
                                    <th>#</th>
                                    <th>ชื่อ</th>
                                     <th>ลายเซ็น</th>
                                    <th>วันที่เพิ่ม</th>
                                    <th>จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach ($user_list as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td> <a href="#"> {{ $item->prefix }} {{ $item->name }} {{ $item->lastname }} </a></td>
                                            <td>  @if ($item->signature_image)
                                                      <span class="text-success"> <i class="fas fa-check"></i></span> 
                                                    @else
                                                        -
                                                    @endif
                                    </td>
                                        <td> {{thai_date($item->created_at)}} </td>
                                        <td>
                                            
                                                     <div class="btn-group dm-button-group btn-group-normal my-2"
                                                        role="group">
                                                        <a href="#" class="btn  btn-xs btn-warning">แก้ไข</a>

                                                        <form action="#"
                                                            method="POST" class="d-inline delete-form">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button"
                                                                class="btn btn-xs btn-danger btn-delete">
                                                                ลบ
                                                            </button>
                                                        </form>

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
