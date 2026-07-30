@section('title', 'ระบบ E-Checker')
@section('description', 'ID Drives')
@extends('layout.app')
@section('content')
    <div class="container-fluid">
        <div class="social-dash-wrap">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb-main">
                        <h4 class="text-capitalize breadcrumb-title">รายละเอียดหมวดหมู่</h4>

                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class=" alert alert-info " role="alert">
                        <div class="alert-content">
                            <span class="fs-20 fw-bold">แบบฟอร์ม : {{ $cates_data->form_name }}</span>
                            <br>
                            <span class="fs-20 fw-bold">หมวดหมู่ : {{ $cates_data->chk_cats_name }} </span>
                        </div>
                    </div>

                    <div class="card mb-2">
                        <div class="card-body">

                            @php
                                $cates_id = request()->cates_id;
                                $item_data_count = DB::table('check_items')
                                    ->where('category_id', '=', $cates_id)
                                    ->first();
                            @endphp

                            <div class="dm-button-list d-flex flex-wrap">
                                <a href="{{ route('agency.cates_list', ['form_id' => $cates_data->form_id]) }}"
                                    class="mx-2 btn btn-success btn-default btn-squared btn-shadow-success ">
                                    <i class="fas fa-long-arrow-alt-left"></i> กลับไปรายการหมวดหมู่
                                </a>

                                <a href="#" class="mx-2 btn btn-warning btn-default btn-squared btn-shadow-warning ">
                                    <i class="fas fa-edit"></i> แก้ไขชื่อหมวดหมู่
                                </a>
                                @if (isset($item_data_count))
                                    <a href="#" class="mx-2 btn btn-info btn-default btn-squared btn-shadow-info ">
                                        <i class="fas fa-redo-alt"></i> เพิ่มข้อตรวจ
                                    </a>
                                @else
                                    <a href="{{ route('agency.item_create', ['id' => request()->cates_id]) }}"
                                        class="mx-2 btn btn-secondary btn-default btn-squared btn-shadow-secondary ">
                                        <i class="fas fa-plus"></i> เพิ่มข้อตรวจ
                                    </a>
                                @endif

                            </div>
                        </div>
                    </div>


                    <div class="card">
                        <div class="card-body">
                            <div class="table4 p-25 mb-30">
                                <div class="table-responsive">
                                    <table class="table mb-0 table-bordered">
                                        <thead>
                                            <tr class="userDatatable-header">
                                                <th>#</th>
                                                <th width="20%">ภาพ</th>
                                                <th>ข้อตรวจ</th>
                                                <th>แก้ไข/ลบ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($item_data as $data)
                                                <tr>
                                                    <td>
                                                        {{ $data->item_no }}
                                                    </td>
                                                    <td>
                                                        @if (empty($data->item_image))
                                                            <img src="{{ asset('upload/No_Image.jpg') }}"
                                                                class="img-thumbnail" width="100px" alt="">
                                                        @else
                                                            <img src="{{ asset($data->item_image) }}" class="img-thumbnail"
                                                                alt="">
                                                        @endif
                                                    </td>
                                                    <td>
                                                        {{ $data->item_name }}
                                                        <br>
                                                        @if (empty($data->item_description))
                                                        @else
                                                            ({{ $data->item_description }})
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="btn-group dm-button-group btn-group-normal my-2"
                                                            role="group">
                                                            <a href="{{ route('agency.item_edit', ['id' => $data->item_id]) }}"
                                                                class="btn btn-xs btn-default 
   btn-squared color-primary btn-outline-primary">แก้ไข</a>
                                                            <a href="#"
                                                                class="btn btn-xs btn-default 
   btn-squared color-primary btn-outline-danger">ลบ</a>
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
@endsection
