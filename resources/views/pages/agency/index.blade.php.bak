@section('title', 'ระบบปฏิบัติการพนักงานขับรถราชการ')
@section('description', 'ID Drives')
@extends('layout.app')
@section('content')
    <div class="container-fluid">
        <div class="social-dash-wrap">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb-main">
                        <label class="fs-20 fw-bold text-dark breadcrumb-title"> @auth
                            {{Auth::user()->name}}
                        @endauth </label>

                    </div>
                </div>
            </div>


               <div class="row">
                        <div class="col-md-6">
                            <a href="{{ route('agency.veh_regis') }}" class="text-decoration-none">
                                <div class="card card-md border-2 card-bordered card-default text-center"
                                    style=" border-color: #a071ff;background-color: #f8f4ff;">
                                    <div class="card-body">
                                        <div class="mb-3">
                                           <img src="{{asset('bus.png')}}" alt="" width="120px">
                                        </div>
                                        <span class="fs-24 fw-bold text-dark mb-1">ลงทะเบียนรถ</span>
                                        <p class="fs-20 text-muted mt-1">คลิกเพื่อกรอกข้อมูลทะเบียนรถ</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>

 <div class="border-top border-light my-4"></div>

            <div class="row">
                <div class="col-md-12">
                   
                             <div class="row">
                <div class="col-md-6">
                    <div class="card card-default mb-4 border border-primary">
                        <div class="card-header">
                            <p class="mb-0 fs-20 fw-bold">ประเภทผู้ใช้ หัวหน้า</p>
                            <a href="{{ route('agency.create_account', ['role' => 'manager']) }}"
                                class="btn btn-primary btn-sm">
                                <i class="fas fa-user-plus me-1"></i> เพิ่มหัวหน้า
                            </a>
                        </div>
                        <div class="card-body">
                            @if ($managers->isEmpty())
                                <p class="text-muted">ไม่มีหัวหน้าในหน่วยงานนี้</p>
                            @else
                                <table class="table table-bordered table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>ชื่อ</th>                                          
                                            <th>ลายเซ็น</th>
                                            <th>จัดการ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($managers as $index => $manager)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $manager->prefix }} {{ $manager->name }} {{ $manager->lastname }}
                                                </td>                                               
                                                <td>    @if ($manager->signature_image)
                                                          <span class="text-success"> <i class="fas fa-check"></i></span>
                                                    @else
                                                        -
                                                    @endif</td>
                                                <td>

                                                    <div class="btn-group dm-button-group btn-group-normal my-2"
                                                        role="group">
                                                        <a href="#" class="btn  btn-xs btn-outline-warning">แก้ไข</a>

                                                        <form action="{{ route('admin.member.destroy', $manager->id) }}"
                                                            method="POST" class="d-inline delete-form">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button"
                                                                class="btn btn-xs btn-outline-danger btn-delete">
                                                                ลบ
                                                            </button>
                                                        </form>

                                                    </div>


                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif


                        </div>
                    </div>


                </div>


                {{-- User --}}
                <div class="col-md-6">
                    <div class="card card-default mb-25 border border-info">
                        <div class="card-header">
                            <p class="fs-20 mb-0 fw-bold">ประเภทผู้ใช้ เจ้าหน้าที่</p>
                            <a href="{{ route('agency.create_account', ['role' => 'user']) }}"
                                class="btn btn-info btn-sm">
                                <i class="fas fa-user-plus me-1"></i> เพิ่มเจ้าหน้าที่
                            </a>
                        </div>
                        <div class="card-body">
                            @if ($users->isEmpty())
                                <p class="text-muted">ไม่มีเจ้าหน้าที่ในหน่วยงานนี้</p>
                            @else
                                <table class="table table-bordered table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>ชื่อ</th>                                           
                                            <th>ลายเซ็น</th>
                                            <th>จัดการ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($users as $index => $user)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $user->prefix }} {{ $user->name }} {{ $user->lastname }}</td>                                               
                                                <td>
                                                    @if ($user->signature_image)
                                                      <span class="text-success"> <i class="fas fa-check"></i></span> 
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>

                                                     <div class="btn-group dm-button-group btn-group-normal my-2"
                                                        role="group">
                                                        <a href="#" class="btn  btn-xs btn-outline-warning">แก้ไข</a>

                                                        <form action="#"
                                                            method="POST" class="d-inline delete-form">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button"
                                                                class="btn btn-xs btn-outline-danger btn-delete">
                                                                ลบ
                                                            </button>
                                                        </form>

                                                    </div>

                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif
                        </div>
                    </div>
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
