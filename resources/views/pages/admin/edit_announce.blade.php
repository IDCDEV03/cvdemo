@section('title', 'Admin')
@section('description', 'ทดสอบ')
@extends('layout.LayoutAdmin')
@section('content')

    <div class="container-fluid">
        <div class="social-dash-wrap">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb-main">
                        <h4 class="text-capitalize breadcrumb-title">แก้ไขประกาศ</h4>
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

                            <form action="{{ route('admin.update_post', ['id' => $announce->id]) }}" method="post"
                                enctype="multipart/form-data">
                                @csrf

                                <div class="mb-3">
                                    <label for="title" class="form-label">หัวข้อประกาศ <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="title" name="title"
                                        value="{{ old('title', $announce->title) }}">

                                </div>

                                <div class="mb-3">
                                    <label for="body" class="form-label">เนื้อหา <span class="text-danger">*</span>

                                    </label>
                                    <textarea class="form-control" id="editor" name="detail" rows="5">
                                    {{ old('detail', $announce->description) }}
                                    </textarea>

                                </div>
                                @if ($announce->file_upload)
                                    <div class="mb-3">
                                        <label for="attachment" class="form-label">ไฟล์แนบเดิม : </label>
                                        <a href="{{ asset('../upload/' . $announce->file_upload) }}" target="_blank">
                                            <i class="las la-paperclip"></i> คลิกเพื่อดูไฟล์แนบ
                                        </a>

                                        <a href="{{ route('admin.delete_file', $announce->id) }}"
                                            class="mb-2 btn btn-xs btn-danger btn-default btn-squared btn-transparent-danger"
                                            onclick="return confirm('ต้องการลบใช่หรือไม่ หากลบแล้วไม่สามารถกู้คืนได้อีก?');">
                                            <i class="fas fa-trash-alt"></i>ลบไฟล์แนบ</a>
                                        <span class="text-danger">หากต้องการแนบไฟล์ใหม่ กรุณาลบไฟล์เก่าออกก่อน</span>
                                    </div>

                                    <div class="border-top my-3"></div>
                                    <div class="mb-3">
                                        <label for="attachment" class="form-label">ไฟล์แนบ (ใหม่)</label>
                                        <input type="file" class="form-control custom-file-input" id="attachment"
                                            name="file_upload">
                                        <span class="fs-12">รองรับเฉพาะ PDF, DOCX, JPG, PNG ขนาดไม่เกิน 5MB </span>
                                    </div>
                                @else
                                 <div class="mb-3">
                                        <label for="attachment" class="form-label">ไฟล์แนบ (ถ้ามี)</label>
                                        <input type="file" class="form-control custom-file-input" id="attachment"
                                            name="file_upload">
                                        <span class="fs-12">รองรับเฉพาะ PDF, DOCX, JPG, PNG ขนาดไม่เกิน 5MB </span>
                                    </div>
                                @endif

                                <div class="border-top my-3"></div>
                                <button type="submit" class="btn btn-success ">
                                    บันทึกการแก้ไข
                                </button>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script src="https://cdn.tiny.cloud/1/dwhh7ntpqcizas3qtuxlr6djbra54faczek8pufkr4g9sjp4/tinymce/7/tinymce.min.js"
        referrerpolicy="origin"></script>
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
