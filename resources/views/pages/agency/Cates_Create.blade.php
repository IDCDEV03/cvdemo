@section('title', 'ระบบ E-Checker')
@section('description', 'ID Drives')
@extends('layout.app')
@section('content')
    <div class="container-fluid">
        <div class="social-dash-wrap">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb-main">
                        <h4 class="text-capitalize breadcrumb-title">สร้างหมวดหมู่ </h4>

                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class=" alert alert-info " role="alert">
                        <div class="alert-content">
                            <span class="fs-20 fw-bold">ชื่อฟอร์ม : {{ $chk_cates->form_name }} </span>
                        </div>
                    </div>

                    <div class="card mb-25">
                        <div class="card-body">
                            @if (session('error'))
                                <div class="alert alert-big alert-danger">
                                    <div class="alert-content">
                                        {{ session('error') }}
                                    </div>
                                </div>
                            @endif
                            <form action="{{route('agency.insert_cates',['id'=>request()->id])}}" method="POST">
                                @csrf
                                <input type="hidden" name="cates_no[]" value="1" class="order-field">

                                <div id="category-wrapper">
                                    <div class="category-group border border-info p-3 mb-2 rounded">
                                        <span class="fs-20 fw-bold text-dark category-number">หมวดหมู่ที่ 1</span>
                                        <div class="mb-2 mt-2">
                                            <label>ชื่อหมวดหมู่ <span class="text-danger">*</span></label>
                                            <input type="text" name="chk_cats_name[]" class="form-control" required>
                                        </div>
                                        <div class="mb-2">
                                            <label>รายละเอียด (ถ้ามี)</label>
                                            <textarea name="chk_detail[]" class="form-control"></textarea>
                                        </div>
                                        <button type="button"
                                            class="btn btn-xs btn-default btn-squared color-danger btn-outline-danger remove-category">ลบ</button>
                                    </div>
                                </div>


                                <button type="button" id="add-category"
                                    class="btn btn-secondary btn-default btn-squared btn-transparent-secondary btn-block"><i class="fas fa-plus"></i> เพิ่มหมวดหมู่</button>

                                <div class="border-top my-3"></div>

                                <button type="submit" class="btn btn-default btn-success mt-4">บันทึกข้อมูล</button>

                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function updateCategoryNumbers() {
            const groups = document.querySelectorAll('.category-group');
            groups.forEach((group, index) => {
                const number = index + 1;
                const heading = group.querySelector('.category-number');
                const orderField = group.querySelector('.order-field');

                if (heading) heading.textContent = `หมวดหมู่ที่ ${number}`;
                if (orderField) orderField.value = number;
            });
        }
        document.getElementById('add-category').addEventListener('click', function() {
            const wrapper = document.getElementById('category-wrapper');
            const group = document.createElement('div');
            group.className = 'category-group border-info border p-3 mb-2 rounded';

            group.innerHTML = `
            <span class="fs-20 fw-bold text-dark category-number">หมวดหมู่ที่ ?</span>
            <input type="hidden" name="cates_no[]" value="?" class="order-field">
            <div class="mb-2 mt-2">
                <label>ชื่อหมวดหมู่ <span class="text-danger">*</span></label>
                <input type="text" name="chk_cats_name[]" class="form-control" required>
            </div>
            <div class="mb-2">
                <label>รายละเอียด (ถ้ามี)</label>
                <textarea name="chk_detail[]" class="form-control"></textarea>
            </div>
            <button type="button" class="btn btn-xs btn-default btn-squared color-danger btn-outline-danger remove-category">ลบ</button>
        `;

            wrapper.appendChild(group);
            updateCategoryNumbers(); // อัปเดตลำดับ
        });

        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-category')) {
                e.target.closest('.category-group').remove();
                updateCategoryNumbers(); // อัปเดตลำดับใหม่
            }
        });

        updateCategoryNumbers();
    </script>
@endpush
