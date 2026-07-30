@section('title', 'ระบบปฏิบัติการพนักงานขับรถราชการ')
@section('description', 'ID Drives')
@extends('layout.app')
@section('content')

    <div class="container-fluid">
        <div class="social-dash-wrap">

            <div class="row">
                <div class="col-md-12">

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card mt-4 mb-25">
                                <div class="card-header">
                                    <span class="fs-20 mb-0">รายการภาพการตรวจรถ ( {{ $car_detail->plate }}
                                        {{ $car_detail->province }} )
                                        วันที่ตรวจ {{ thai_datetime($car_detail->updated_at) }}

                                    </span>
                                </div>
                                <div class="card-body">
                                      <div class=" alert alert-info" role="alert">
                                    <div class="alert-content">
                                       <p class="fw-bold fs-20">ข้อตรวจ : {{ $chk_item->item_name }}</p>
                                    </div>
                                 </div>

                                   

                                    <div class="table-responsive">
                                        <table class="table table-default table-bordered mb-0" id="table-one">
                                            <thead>
                                                <tr>
                                                    <th class="text-sm fw-bold">#</th>
                                                    <th class="text-sm fw-bold">ภาพ</th>
                                                    <th>จัดการ</th>

                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($image as $item)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>
                                                            <img src="{{ asset($item->image_path) }}" width="400px"
                                                                alt="" class="img-thumbnail">
                                                        </td>
                                                        <td>
                                                            <div class="btn-group btn-group-xs" role="group"
                                                                aria-label="Image Actions">
                                                                <a href="#"
                                                                    class="btn btn-xs btn-outline-primary edit-image-btn"
                                                                    data-bs-toggle="modal" data-bs-target="#editImageModal"
                                                                    data-id="{{ $item->id }}"
                                                                    data-image="{{ asset($item->image_path) }}">
                                                                    <i class="fas fa-edit"></i> เปลี่ยนภาพ
                                                                </a>
                                                                <a href="{{route('delete_image',['id'=>$item->id])}}" onclick="return confirm('ต้องการลบภาพ ใช่หรือไม่?');" class="btn btn-outline-danger btn-xs">
                                                                    <i class="fas fa-trash-alt"></i> ลบ
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
            </div>
        </div>
    </div>


    <!-- Modal: Edit Image -->
<div class="modal fade" id="editImageModal" tabindex="-1" aria-labelledby="editImageModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{route('update_image')}}" enctype="multipart/form-data">
            @csrf
            @method('POST')
            <input type="hidden" name="image_id" id="modal-image-id">

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editImageModalLabel">เปลี่ยนภาพ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <div class="modal-body text-center">
                    <div class="mb-3">
                        <img id="current-image" src="#" alt="Current" class="img-thumbnail mb-2" style="max-height: 200px;">
                        <input type="file" name="new_image" id="new-image" class="form-control mt-2" accept="image/*" required>
                        <div class="mt-2">
                            <img id="preview-image" class="img-thumbnail d-none" style="max-height: 200px;">
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-sm btn-success">บันทึกการเปลี่ยน</button>
                    <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">ยกเลิก</button>
                </div>
            </div>
        </form>
    </div>
</div>


@endsection
@push('scripts')
<script>
    // เปิด Modal แก้ไขภาพ
    $('.edit-image-btn').on('click', function () {
        const imageId = $(this).data('id');
        const imageUrl = $(this).data('image');

        $('#modal-image-id').val(imageId);
        $('#current-image').attr('src', imageUrl);
        $('#preview-image').addClass('d-none');
        $('#new-image').val('');
    });

    // Preview รูปใหม่
    $('#new-image').on('change', function () {
        const file = this.files[0];
        const reader = new FileReader();
        reader.onload = function (e) {
            $('#preview-image').attr('src', e.target.result).removeClass('d-none');
        };
        if (file) reader.readAsDataURL(file);
    });
</script>

    <!-- DataTables  -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#table-one').DataTable({
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
