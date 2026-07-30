@section('title', 'Admin')
@section('description', 'ทดสอบ')
@extends('layout.LayoutAdmin')
@section('content')
    @php
        use Illuminate\Support\Str;
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    @endphp
    <div class="container-fluid">
        <div class="social-dash-wrap">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb-main">
                        <h4 class="text-capitalize breadcrumb-title">ประกาศ</h4>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <a href="{{ route('admin.create_post') }}"
                                class="btn btn-default btn-squared btn-transparent-secondary ">สร้างประกาศใหม่</a>
                        </div>
                    </div>

                    @foreach ($list_post as $item)
                        @php

                            $attachment = $item->file_upload;

                            $ext = $attachment ? strtolower(pathinfo($attachment, PATHINFO_EXTENSION)) : null;

                            // ประเภทไฟล์
                            $imageExt = ['jpg', 'jpeg', 'png', 'gif'];
                            $pdfExt = ['pdf'];
                            $docExt = ['doc', 'docx'];

                            $isImage = in_array($ext, $imageExt);
                            $isPDF = in_array($ext, $pdfExt);
                            $isDoc = in_array($ext, $docExt);
                        @endphp
                        <!-- Blog Post -->
                        <div class="ap-main-post mt-4">
                            <div class="card mb-25">
                                <!-- Blog Style -->
                                <div class="card-body pb-0 px-sm-25 ap-main-post__header">
                                    <div
                                        class="d-flex  flex-row pb-20 border-top-0 border-left-0 border-right-0 ap-post-content__title align-items-center ">

                                        <div class="d-inline-block align-middle me-15">
                                            <span
                                                class="profile-image bg-opacity-secondary rounded-circle d-block avatar avatar-md m-0"
                                                style="background-image:url('{{ asset('assets/img/announce.png') }}'); background-size: cover;"></span>
                                        </div>

                                        <h6 class="mb-0 flex-1 text-dark">
                                            <i class="las la-star"></i> {{ $item->name }}
                                            <small class="m-0 d-block">
                                                สร้างโพส {{ Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i') }}
                                                แก้ไขล่าสุด  {{ Carbon\Carbon::parse($item->updated_at)->format('d/m/Y H:i') }}
                                            </small>
                                        </h6>


                                        <div class="button-inline-list">

                                            <div class="btn-group dm-button-group btn-group-normal my-2" role="group">

                                                <a href="{{route('admin.edit_post',['id'=>$item->post_id])}}" class="btn btn-outline-secondary btn-xs">
                                                    <i class="las la-edit"></i>
                                                    แก้ไข
                                                </a>

                                                <a href="{{route('admin.delete_post',['id'=>$item->post_id])}}" class="btn btn-outline-danger btn-xs" onclick="return confirm('ต้องการลบใช่หรือไม่ หากลบแล้วไม่สามารถกู้คืนได้อีก?');">
                                                    <i class="las la-trash-alt"></i> ลบ
                                                </a>

                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <span class="fs-24 fw-bold">{{ $item->title }}</span>
                                    </div>

                                    @isset($item->file_upload)
                                        @if ($isImage)
                                            <div class="mb-15">
                                                <img src="{{ asset('../upload/' . $item->file_upload) }}" alt="post image"
                                                    class="ap-post-attach__headImg img-thumbnail">
                                            </div>
                                        @elseif($isPDF)
                                            <div class="mb-10">
                                                <i class="las la-paperclip"></i>
                                                <a href="{{ asset('../upload/' . $item->file_upload) }}" target="_blank"
                                                    class="fs-14">
                                                    ดาวน์โหลดเอกสาร <i class="far fa-file-pdf me-1"></i>
                                                </a>
                                            </div>
                                        @elseif($isDoc)
                                            <div class="mb-10">
                                                <i class="las la-paperclip"></i>
                                                <a href="{{ asset('../upload/' . $item->file_upload) }}" target="_blank"
                                                    class="fs-14">
                                                    ดาวน์โหลดเอกสาร <i class="far fa-file-word"></i>
                                                </a>
                                            </div>
                                        @endif
                                    @endisset

                                    <div class="pb-3 border-top-0 border-left-0 border-right-0 ap-post-content__p">
                                        {!! $item->description !!}
                                    </div>
                                </div>
                                <!-- Blog Style End -->
                            </div>
                            <!-- Blog Post End -->
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
