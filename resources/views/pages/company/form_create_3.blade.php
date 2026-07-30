@section('title', 'ระบบตรวจมาตรฐานรถ')
@section('description', 'ID Drives')
@extends('layout.app')
@section('content')
@php
     //เช็คลำดับหมวดหมู่   
        $maxOrder = DB::table('check_categories')
           ->where('form_id', $id)
           ->max('cates_no');

       $startOrder = is_null($maxOrder) ? 0 : (int)$maxOrder;
@endphp
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

                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="dm-button-list d-flex flex-wrap gap-2">

                                <a class="btn btn-dark btn-default btn-squared btn-transparent-dark">
                                    <i class="fa fa-search-plus" aria-hidden="true"></i> ตัวอย่างฟอร์ม
                                </a>

                                <a href="{{route('company.form.create2',['id'=>request()->id,'order'=>$startOrder])}}" class="btn btn-secondary btn-default btn-squared btn-transparent-secondary">
                                    เพิ่มหมวดหมู่
                                </a>
                            </div>

                        </div>

                    </div>

                    <div class="card mb-10 mt-2">
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
                                                <a
                                                    href="{{ route('company.form.categories_list', ['cates_id' => $item->category_id]) }}">
                                                    {{ $item->chk_cats_name }}
                                                </a>
                                            </td>
                                            <td>
                                                <div class="btn-group dm-button-group btn-group-normal my-2" role="group">

                                                     <div class="btn-group dm-button-group btn-group-normal my-2" role="group">
                                                    <button
                                                        class="btn btn-primary btn-sm btn-squared btn-transparent-primary"
                                                        data-bs-toggle="modal" data-bs-target="#editModal"
                                                        data-id="{{ $item->id }}"
                                                        data-name="{{ $item->chk_cats_name }}">
                                                        แก้ไขชื่อหมวดหมู่
                                                    </button>

                                                
                                                    <form action="{{route('staff.cates_delete', $item->category_id)}}"
                                                        method="POST" class="d-inline"
                                                        onsubmit="return confirm('หากลบหมวดหมู่ข้อตรวจในหมวดหมู่นี้จะถูกลบไปด้วย ต้องการลบใช่หรือไม่?');">
                                                        @csrf
                                                        <input type="hidden" name="form_id" value="{{$item->form_id}}">
                                                        <button type="submit" class="btn btn-danger btn-sm btn-squared btn-transparent-danger">
                                                            ลบหมวดหมู่
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
    </div>


      <!-- Modal ฟอร์มแก้ไข -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" id="editForm">
                    @csrf
                    <div class="modal-header">
                        <label class="modal-title" id="editModalLabel">แก้ไขชื่อหมวดหมู่</label>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="ปิด"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="category_name" class="form-label">ชื่อหมวดหมู่</label>
                            <input type="text" class="form-control" id="category_name" name="category_name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">บันทึก</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')

  <script>
        var editModal = document.getElementById('editModal');
        editModal.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;
            var id = button.getAttribute('data-id');
            var name = button.getAttribute('data-name');
            var inputName = editModal.querySelector('#category_name');
            inputName.value = name;
            var form = document.getElementById('editForm');
            form.action = '/form/categories/' + id + '/update';
        });
    </script>

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
