@section('title', 'ระบบปฏิบัติการพนักงานขับรถราชการ')
@section('description', 'ID Drives')
@extends('layout.LayoutAdmin')
@section('content')

    <div class="container-fluid">
        <div class="social-dash-wrap">


            <div class="row">
                <div class="col-lg-12">
                    {{-- กล่องแสดงข้อมูลหน่วยงาน --}}
                    <div class="mt-25 card card-default mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <p class="card-title fs-22 fw-bold mb-0">รายละเอียดหน่วยงาน</p>
                            <a href="{{ route('admin.agency_list') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-arrow-left me-1"></i> กลับ
                            </a>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless table-sm">
                                <tbody>
                                    @if ($agency->logo_agency)
                                        <tr>
                                            <th></th>
                                            <td>
                                                <img src="{{ asset($agency->logo_agency) }}" alt="โลโก้"
                                                    class="img-thumbnail" style="max-height: 120px;">
                                            </td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <th style="width: 25%;">ชื่อหน่วยงาน:</th>
                                        <td>{{ $agency->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>อีเมล:</th>
                                        <td>{{ $agency->email }}</td>
                                    </tr>

                                    <tr>
                                        <th>เบอร์โทร:</th>
                                        <td>{{ $agency->user_phone }}</td>
                                    </tr>
                                    <tr>
                                        <th>วันที่สร้าง:</th>
                                        <td>{{ thai_date($agency->created_at) }}</td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>

            {{-- ห้วหน้า --}}
            <div class="row">
                <div class="col-md-6">
                    <div class="card card-default mb-4 border border-primary">
                        <div class="card-header">
                            <p class="mb-0 fs-20 fw-bold">ประเภทผู้ใช้ หัวหน้า</p>
                            <a href="{{ route('admin.member.create', ['role' => 'manager', 'id' => request()->id]) }}"
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
                                            <th>อีเมล</th>
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
                                                <td>{{ $manager->email }}</td>
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
                            <a href="{{ route('admin.member.create', ['role' => 'user', 'id' => $agency->id]) }}"
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
                                            <th>อีเมล</th>
                                            <th>ลายเซ็น</th>
                                            <th>จัดการ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($users as $index => $user)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $user->prefix }} {{ $user->name }} {{ $user->lastname }}</td>
                                                <td>{{ $user->email }}</td>
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

                                                        <form action="{{ route('admin.member.destroy', $user->id) }}"
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

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const deleteButtons = document.querySelectorAll('.btn-delete');

                deleteButtons.forEach(function(button) {
                    button.addEventListener('click', function() {
                        const form = button.closest('form');

                        Swal.fire({
                            title: 'ยืนยันการลบ?',
                            text: "หากลบแล้วจะไม่สามารถกู้คืนได้!",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'ลบ',
                            cancelButtonText: 'ยกเลิก'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                    });
                });
            });
        </script>
    @endpush
