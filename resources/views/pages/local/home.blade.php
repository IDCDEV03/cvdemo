@section('title', $title)
@section('description', $description)
@extends($layout)
@section('content')
    @php
        use App\Enums\Role;
        $role = Auth::user()->role;
    @endphp
    <div class="container-fluid">
        <div class="social-dash-wrap">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb-main">
                        <span class="fs-24 fw-bold text-capitalize breadcrumb-title">หน้าหลัก</span>

                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">

                    @if ($role === Role::User)
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card p-4 h-100">
                                    <a href="{{route('user.veh_regis')}}" class="btn btn-outline-secondary">ลงทะเบียนรถ</a>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="card mt-4">
                                    <div class="card-header">
                                        <span class="fs-20 mb-0">รถที่ลงทะเบียน</span>
                                    </div>
                                    <div class="card-body">

                                        <div class="table-responsive">
                                            <table class="table table-default table-bordered mb-0">
                                                <thead class="table-info">
                                                    <tr>
                                                        <th class="text-sm fw-bold">#</th>
                                                        <th class="text-sm fw-bold">ทะเบียนรถ</th>
                                                        <th class="text-sm fw-bold">วันที่ลงทะเบียน</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>1</td>
                                                        <td>กข 1234 กรุงเทพมหานคร</td>
                                                        <td>10 มกราคม 2566</td>
                                                    </tr>
                                                    <tr>
                                                        <td>2</td>
                                                        <td>ขก 5678 เชียงใหม่</td>
                                                        <td>5 กุมภาพันธ์ 2566</td>
                                                    </tr>
                                                    <tr>
                                                        <td>3</td>
                                                        <td>งจ 1122 ชลบุรี</td>
                                                        <td>20 มีนาคม 2566</td>
                                                    </tr>
                                                    <tr>
                                                        <td>4</td>
                                                        <td>จด 4455 ขอนแก่น</td>
                                                        <td>12 เมษายน 2566</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="card">
                            <div class="card-body">
                                Not user
                            </div>
                        </div>
                    @endif


                </div>
            </div>
        </div>
    </div>
@endsection
