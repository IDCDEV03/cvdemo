@section('title', 'Admin')
@section('description', 'ทดสอบ')
@extends('layout.LayoutAdmin')
@section('content')


    <div class="container-fluid">
        <div class="social-dash-wrap">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb-main">
                        <h4 class="text-capitalize breadcrumb-title">สร้างประกาศใหม่</h4>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">

                    {{-- โพสต์ประกาศ --}}
                    <div class="card mb-25">
                        <div class="card-body">
                            @if ($errors->any())
                                <div class="alert alert-big alert-danger">
                                    <div class="alert-content">
                                        <h5 class='fw-bold alert-heading'>พบข้อผิดพลาด</h5>
                                        <ul class="mb-0">
                                            @foreach ($errors->all() as $error)
                                                <li><i class="las la-caret-right"></i> {{ $error }} </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            @endif

                            <form action="{{ route('admin.insert_post') }}" method="post" enctype="multipart/form-data">
                                @csrf

                                <div class="mb-3">
                                    <label for="title" class="form-label">หัวข้อประกาศ <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="title" name="title" required>

                                </div>

                                <div class="mb-3">
                                    <label for="body" class="form-label">เนื้อหา <span class="text-danger">*</span>

                                    </label>
                                    <textarea class="form-control" id="editor" name="detail" rows="5"></textarea>

                                </div>

                                <div class="mb-3">
                                    <label for="attachment" class="form-label">ไฟล์แนบ (ถ้ามี)</label>
                                    <input type="file" class="form-control custom-file-input" id="attachment"
                                        name="file_upload">
                                    <span class="fs-12">รองรับเฉพาะ PDF, DOCX, JPG, PNG ขนาดไม่เกิน 5MB</span>

                                </div>

                                <button type="submit" class="btn btn-success">
                                    บันทึก
                                </button>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script src="https://cdn.tiny.cloud/1/6u39l558bp1t9nbc9okqrmousz84myu8x6s5djuaxhi69i04/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        tinymce.init({
            selector: '#editor',
            menubar: false,
            plugins: 'link lists',
            toolbar: 'fontsize | undo redo | bold italic underline | bullist numlist | link',
            font_size_formats: '12pt 14pt 16pt 18pt 24pt 36pt 48pt',
            height: 300,
            branding: false
        });
    </script>


@endsection
