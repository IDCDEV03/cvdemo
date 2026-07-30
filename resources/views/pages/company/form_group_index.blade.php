@section('title', 'จัดการกลุ่มฟอร์ม')
@section('description', 'ID Drives')
@extends('layout.app')
@section('content')
    <div class="container-fluid">

        <div class="col-md-12">
            <div class="card mt-20  mb-25">
                <div class="card-body">
                    <div class="dm-button-list d-flex flex-wrap gap-2">
                        <a href="{{ route('company.form-groups.create') }}" class="btn btn-primary btn-transparent-primary fs-16">
                            <i class="uil uil-plus"></i> สร้างกลุ่มฟอร์มใหม่ 
                        </a>
                       
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <div class="alert-content">
                            <p>{{ session('success') }}</p>
                            <button type="button" class="btn-close text-capitalize" data-bs-dismiss="alert" aria-label="Close">
                                <i class="uil uil-times"></i>
                            </button>
                        </div>
                    </div>
                @endif

                <div class="card">
                    <div class="card-header">
                        <span class="fs-18 fw-bold">รายการกลุ่มฟอร์ม</span>
                    </div>
                    <div class="card-body">

                        <table class="table table-bordered" id="form-groups-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>ชื่อกลุ่มฟอร์ม</th>
                                    <th>ประเภท/การมองเห็น</th>
                                    <th>ผู้สร้าง</th>
                                    <th>สถานะ</th>
                                    <th>จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($formGroups as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <a href="{{ route('company.form-groups.show', ['id' => $item->id]) }}">
                                                {{ $item->name }}
                                            </a>
                                        </td>
                                        <td>
                                            @if($item->is_system_default)
                                                <span class="badge bg-success rounded-pill">ส่วนกลาง (ทุกบริษัท)</span>
                                            @else
                                                <span class="badge bg-info rounded-pill">บริษัท: {{ $item->company_name ?? '-' }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $item->creator_name ?? '-' }}</td>
                                        <td>
                                            @if ($item->is_active == 1)
                                                <span class="badge bg-success rounded-pill">Active</span>
                                            @else
                                                <span class="badge bg-danger rounded-pill">InActive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('company.form-groups.show', ['id' => $item->id]) }}" class="btn btn-info btn-sm text-white">
                                                <i class="uil uil-eye"></i> ดูรายละเอียด
                                            </a>
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
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#form-groups-table').DataTable({
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