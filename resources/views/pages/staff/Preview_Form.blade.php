@section('title', 'ตัวอย่างฟอร์ม: ' . $form->form_name)
@section('description', 'ID Drives')
@extends('layout.app')

@section('content')
<div class="container-fluid">
    <div class="row mt-30 mb-25 justify-content-center">
        <div class="col-lg-8"> <div class="card mb-30 shadow-sm border-0">
                <div class="card-body text-center bg-info text-white rounded pb-4 pt-4">
                    <h3 class="text-white mb-2">[Preview] {{ $form->form_name }}</h3>
                    <p class="mb-0 text-white-50">รหัสฟอร์ม: {{ $form->form_code }} | ประเภทรถ: {{ $form->vehicle_type ?? 'ไม่ได้ระบุ' }}</p>
                </div>
            </div>

            @forelse($categories as $category)
                <div class="card mb-4 shadow-sm">
                    <div class="card-header border-bottom" style="background-color: #e3cef7 !important;">
                        <span class="mb-0 text-primary fw-bold fs-18">
                            หมวดหมู่ที่ {{ $category->cates_no }} : {{ $category->chk_cats_name }}
                        </span>
                        @if($category->chk_detail)
                            <small class="text-muted">{{ $category->chk_detail }}</small>
                        @endif
                    </div>
                    
                    <div class="card-body p-0">
                        @if(isset($itemsGrouped[$category->category_id]))
                            <ul class="list-group list-group-flush">
                                
                                @foreach($itemsGrouped[$category->category_id] as $item)
                                    <li class="list-group-item p-3 border-bottom-0 border-top">
                                        <div class="d-flex align-items-start">
                                            <div class="me-3 fw-bold text-dark">
                                                {{ $category->cates_no }}.{{ $item->item_no }}
                                            </div>
                                            <div class="flex-grow-1">
                                                <p class="mb-1 text-dark fw-500">{{ $item->item_name }}</p>
                                                
                                                @if($item->item_description)
                                                    <small class="text-muted d-block mb-2">{{ $item->item_description }}</small>
                                                @endif

                                             

                                            </div>
                                        </div>
                                    </li>
                                @endforeach

                            </ul>
                        @else
                            <div class="p-3 text-center text-muted">
                                <em>- ยังไม่มีข้อตรวจในหมวดหมู่นี้ -</em>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="alert alert-warning text-center">
                    ยังไม่มีการสร้างหมวดหมู่สำหรับฟอร์มนี้
                </div>
            @endforelse

            <div class="text-center mt-4">
                <a href="{{route('staff.form_step3',['id'=>$form->form_id])}}" class="btn btn-dark px-4">ย้อนกลับ</a>
            </div>

        </div>
    </div>
</div>
@endsection