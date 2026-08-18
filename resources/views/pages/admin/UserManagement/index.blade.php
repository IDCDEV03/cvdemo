@section('title', 'ระบบตรวจมาตรฐานรถ')
@section('description', 'ID Drives')
@extends('layout.LayoutAdmin')
@section('content')

@php
    $roleLabels = [
        'agency' => 'หน่วยงาน',
        'manager' => 'หัวหน้า',
        'user' => 'เจ้าหน้าที่หน่วยงาน',
        'company' => 'บริษัทฯว่าจ้าง',
        'supply' => 'บริษัทฯ Supply',
        'staff' => 'เจ้าหน้าที่',
        'inspector' => 'ช่างตรวจ',
    ];
    $roleDots = [
        'agency' => '#3B82F6',
        'manager' => '#6366F1',
        'user' => '#64748B',
        'company' => '#EC4899',
        'supply' => '#8B5CF6',
        'staff' => '#22C55E',
        'inspector' => '#F59E0B',
    ];
@endphp

@push('styles')
<style>
    .um-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 18px;
    }
    .um-filters {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }
    .um-filter-chip {
        font-size: 13px;
        font-weight: 500;
        padding: 6px 14px;
        border-radius: 20px;
        border: 1px solid #E2E8F0;
        color: #64748B;
        background: #fff;
        text-decoration: none;
        transition: all .15s ease;
    }
    .um-filter-chip:hover {
        color: #334155;
        border-color: #CBD5E1;
        text-decoration: none;
    }
    .um-filter-chip.active {
        background: #EEF2FF;
        border-color: #6366F1;
        color: #4338CA;
    }
    .um-table {
        width: 100%;
        border-collapse: collapse;
    }
    .um-table thead th {
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: .03em;
        color: #94A3B8;
        font-weight: 600;
        border-bottom: 1px solid #E2E8F0;
        padding: 10px 14px;
        white-space: nowrap;
    }
    .um-table tbody td {
        padding: 12px 14px;
        border-bottom: 1px solid #F1F5F9;
        vertical-align: middle;
        font-size: 14px;
    }
    .um-table tbody tr:hover {
        background: #F8FAFC;
    }
    .um-name { font-weight: 600; color: #1E293B; }
    .um-sub { color: #94A3B8; font-size: 12px; }
    .um-role {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        font-size: 13px;
        font-weight: 600;
        color: #334155;
    }
    .um-role .dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        flex-shrink: 0;
    }
    .um-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
    }
    .um-status .dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }
    .um-status.active { color: #16A34A; }
    .um-status.active .dot { background: #16A34A; }
    .um-status.inactive { color: #DC2626; }
    .um-status.inactive .dot { background: #DC2626; }
    .um-status:hover { opacity: .75; text-decoration: none; }
    .um-actions { display: flex; gap: 6px; }
    .um-actions .btn { font-size: 12px; padding: 5px 10px; }
</style>
@endpush

    <div class="container-fluid">
        <div class="social-dash-wrap">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb-main">
                        <span class="fs-24 fw-bold breadcrumb-title">จัดการผู้ใช้งาน</span>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card card-default">
                        <div class="card-body">

                            <div class="um-toolbar">
                                <div class="um-filters">
                                    <a class="um-filter-chip {{ !$roleFilter ? 'active' : '' }}"
                                        href="{{ route('admin.users.index') }}">ทั้งหมด</a>
                                    @foreach ($roles as $role)
                                        <a class="um-filter-chip {{ $roleFilter === $role->value ? 'active' : '' }}"
                                            href="{{ route('admin.users.index', ['role' => $role->value]) }}">
                                            {{ $roleLabels[$role->value] ?? $role->value }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table id="table-data" class="table um-table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>ชื่อ-นามสกุล</th>
                                            <th>Email / Username</th>
                                            <th>Role</th>
                                            <th>บริษัทว่าจ้าง</th>
                                            <th>สถานะ</th>
                                            <th>วันที่สร้าง</th>
                                            <th>จัดการ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($users as $index => $user)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td class="um-name">{{ trim($user->prefix . ' ' . $user->name . ' ' . $user->lastname) }}</td>
                                                <td>
                                                    {{ $user->email }}
                                                    @if ($user->username)
                                                        <div class="um-sub">{{ $user->username }}</div>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="um-role">
                                                        <span class="dot" style="background: {{ $roleDots[$user->role->value] ?? '#94A3B8' }};"></span>
                                                        {{ $roleLabels[$user->role->value] ?? $user->role->value }}
                                                    </span>
                                                </td>
                                                <td>{{ $user->role === \App\Enums\Role::Supply ? ($companyNames[$user->company_code] ?? '-') : '-' }}</td>
                                                <td>
                                                    @if ((string) $user->user_status === '2')
                                                        <a href="{{ route('admin.users.status', [$user->id, 1]) }}"
                                                            class="um-status inactive" title="คลิกเพื่อเปิดใช้งาน">
                                                            <span class="dot"></span> ปิดใช้งาน
                                                        </a>
                                                    @else
                                                        <a href="{{ route('admin.users.status', [$user->id, 2]) }}"
                                                            class="um-status active" title="คลิกเพื่อปิดใช้งาน">
                                                            <span class="dot"></span> ใช้งานอยู่
                                                        </a>
                                                    @endif
                                                </td>
                                                <td>{{ thai_date($user->created_at) }}</td>
                                                <td>
                                                    <div class="um-actions">
                                                        <a href="{{ route('admin.users.edit', $user->id) }}"
                                                            class="btn btn-warning btn-default btn-squared btn-shadow-warning"
                                                            title="แก้ไข">
                                                            <i class="fas fa-edit"></i> แก้ไข
                                                        </a>

                                                        <form action="{{ route('admin.users.destroy', $user->id) }}"
                                                            method="POST" class="d-inline"
                                                            onsubmit="return confirm('ต้องการลบผู้ใช้งานนี้ใช่หรือไม่?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                class="btn btn-danger btn-default btn-squared btn-shadow-danger">
                                                                <i class="las la-trash-alt"></i> ลบ
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
    </div>
@endsection

@push('scripts')
    <!-- DataTables  -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#table-data').DataTable({
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
