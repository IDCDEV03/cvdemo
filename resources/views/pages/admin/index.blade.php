@section('title', 'ระบบตรวจมาตรฐานรถ')
@section('description', 'ID Drives')
@extends('layout.LayoutAdmin')

@push('styles')
<style>
    .menu-card-link { text-decoration: none; display: block; }
    .menu-card-link:hover .menu-card {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0,0,0,.12) !important;
    }
    .menu-card {
        border-radius: 16px !important;
        border: none !important;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .menu-icon-wrap {
        width: 76px; height: 76px;
        border-radius: 18px;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 14px;
    }
    .menu-icon-wrap i {
        font-size: 42px !important;
        line-height: 1 !important;
    }
</style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="social-dash-wrap">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb-main">
                        <h4 class="text-capitalize breadcrumb-title">หน้าหลัก</h4>
                    </div>
                </div>
            </div>

            <div class="row g-4">

                {{-- รายการบริษัทฯ --}}
                <div class="col-md-4">
                    <a href="{{ route('admin.cp_list') }}" class="menu-card-link">
                        <div class="menu-card card shadow-sm text-center px-3 py-4"
                            style="background:#EFF6FF; border-top: 4px solid #3B82F6 !important;">
                            <div class="menu-icon-wrap" style="background:#DBEAFE; color:#1D4ED8;">
                                <i class="uil uil-building"></i>
                            </div>
                            <div style="font-size:20px; font-weight:700; color:#1D4ED8;">รายการบริษัทฯ</div>
                            <div class="text-muted mt-1" style="font-size:13px;">สร้างและจัดการข้อมูลบริษัทฯว่าจ้าง</div>
                        </div>
                    </a>
                </div>

                {{-- รายการบริษัทฯ Supply --}}
                <div class="col-md-4">
                    <a href="{{ route('admin.supply_all') }}" class="menu-card-link">
                        <div class="menu-card card shadow-sm text-center px-3 py-4"
                            style="background:#F5F3FF; border-top: 4px solid #8B5CF6 !important;">
                            <div class="menu-icon-wrap" style="background:#EDE9FE; color:#6D28D9;">
                                <i class="uil uil-truck"></i>
                            </div>
                            <div style="font-size:20px; font-weight:700; color:#6D28D9;">รายการบริษัทฯ Supply</div>
                            <div class="text-muted mt-1" style="font-size:13px;">สร้างและจัดการข้อมูล Supply</div>
                        </div>
                    </a>
                </div>

                {{-- รายการฟอร์ม --}}
                <div class="col-md-4">
                    <a href="#" class="menu-card-link">
                        <div class="menu-card card shadow-sm text-center px-3 py-4"
                            style="background:#FDF2F8; border-top: 4px solid #EC4899 !important;">
                            <div class="menu-icon-wrap" style="background:#FCE7F3; color:#9D174D;">
                                <i class="uil uil-file-alt"></i>
                            </div>
                            <div style="font-size:20px; font-weight:700; color:#9D174D;">รายการฟอร์ม</div>
                            <div class="text-muted mt-1" style="font-size:13px;">สร้างและจัดการฟอร์ม</div>
                        </div>
                    </a>
                </div>

                {{-- รายการช่างตรวจ --}}
                <div class="col-md-4">
                    <a href="#" class="menu-card-link">
                        <div class="menu-card card shadow-sm text-center px-3 py-4"
                            style="background:#F0FDF4; border-top: 4px solid #22C55E !important;">
                            <div class="menu-icon-wrap" style="background:#DCFCE7; color:#15803D;">
                                <i class="uil uil-wrench"></i>
                            </div>
                            <div style="font-size:20px; font-weight:700; color:#15803D;">รายการช่างตรวจ</div>
                            <div class="text-muted mt-1" style="font-size:13px;">สร้างและจัดการข้อมูลช่างตรวจ</div>
                        </div>
                    </a>
                </div>

                {{-- จัดการผู้ใช้งาน --}}
                <div class="col-md-4">
                    <a href="{{ route('admin.users.index') }}" class="menu-card-link">
                        <div class="menu-card card shadow-sm text-center px-3 py-4"
                            style="background:#EEF2FF; border-top: 4px solid #6366F1 !important;">
                            <div class="menu-icon-wrap" style="background:#E0E7FF; color:#4338CA;">
                                <i class="uil uil-users-alt"></i>
                            </div>
                            <div style="font-size:20px; font-weight:700; color:#4338CA;">จัดการผู้ใช้งาน</div>
                            <div class="text-muted mt-1" style="font-size:13px;">เพิ่ม แก้ไข ลบผู้ใช้งานในระบบ</div>
                        </div>
                    </a>
                </div>





            </div>
        </div>
    </div>
@endsection
