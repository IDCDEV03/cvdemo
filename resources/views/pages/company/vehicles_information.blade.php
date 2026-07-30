@section('title', 'รายการตรวจสอบสภาพรถ')
@section('description', 'ID Drives')
@extends('layout.app')
@push('styles')
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
 <style>
        /* ── Stat filter cards ── */
        .stat-filter-card {
            border-radius: 14px;
            padding: 14px 16px 12px;
            text-align: center;
            border: none;
            transition: transform .2s, box-shadow .2s;
            cursor: pointer;
        }
        .stat-filter-card:hover { transform: translateY(-3px); }
        .stat-filter-main { display: flex; align-items: center; justify-content: center; gap: 8px; }
        .stat-filter-icon { font-size: 22px; line-height: 1; }
        .stat-filter-count { font-size: 24px; font-weight: 700; line-height: 1; }
        .stat-filter-label { font-size: 16px; margin-top: 6px; font-weight: 500; }

        /* Inactive: light pastel gradient per type */
        .stat-filter-card.stat-all  { background: linear-gradient(135deg,#ede9fe,#c7d2fe); }
        .stat-filter-card.stat-all .stat-filter-icon,
        .stat-filter-card.stat-all .stat-filter-count { color: #4f46e5; }
        .stat-filter-card.stat-all .stat-filter-label { color: #6366f1; }
        .stat-filter-card.stat-all:not(.stat-active):hover { box-shadow: 0 6px 20px rgba(79,70,229,.22); }

        .stat-filter-card.stat-pass { background: linear-gradient(135deg,#dcfce7,#a7f3d0); }
        .stat-filter-card.stat-pass .stat-filter-icon,
        .stat-filter-card.stat-pass .stat-filter-count { color: #059669; }
        .stat-filter-card.stat-pass .stat-filter-label { color: #10b981; }
        .stat-filter-card.stat-pass:not(.stat-active):hover { box-shadow: 0 6px 20px rgba(5,150,105,.22); }

        .stat-filter-card.stat-wait { background: linear-gradient(135deg,#fef3c7,#fde68a); }
        .stat-filter-card.stat-wait .stat-filter-icon,
        .stat-filter-card.stat-wait .stat-filter-count { color: #d97706; }
        .stat-filter-card.stat-wait .stat-filter-label { color: #f59e0b; }
        .stat-filter-card.stat-wait:not(.stat-active):hover { box-shadow: 0 6px 20px rgba(217,119,6,.22); }

        .stat-filter-card.stat-fail { background: linear-gradient(135deg,#fee2e2,#fecaca); }
        .stat-filter-card.stat-fail .stat-filter-icon,
        .stat-filter-card.stat-fail .stat-filter-count { color: #dc2626; }
        .stat-filter-card.stat-fail .stat-filter-label { color: #ef4444; }
        .stat-filter-card.stat-fail:not(.stat-active):hover { box-shadow: 0 6px 20px rgba(220,38,38,.22); }

        /* Active: vibrant gradient + white text + glow */
        .stat-filter-card.stat-active.stat-all  { background: linear-gradient(135deg,#4f46e5,#7c3aed); box-shadow: 0 8px 28px rgba(79,70,229,.45); }
        .stat-filter-card.stat-active.stat-pass { background: linear-gradient(135deg,#059669,#10b981); box-shadow: 0 8px 28px rgba(5,150,105,.45); }
        .stat-filter-card.stat-active.stat-wait { background: linear-gradient(135deg,#d97706,#f59e0b); box-shadow: 0 8px 28px rgba(217,119,6,.45); }
        .stat-filter-card.stat-active.stat-fail { background: linear-gradient(135deg,#dc2626,#f43f5e); box-shadow: 0 8px 28px rgba(220,38,38,.45); }
        .stat-filter-card.stat-active .stat-filter-icon,
        .stat-filter-card.stat-active .stat-filter-count,
        .stat-filter-card.stat-active .stat-filter-label { color: #fff !important; }

        /* ── Search form ── */
        .vi-search-label { font-size: 16px; font-weight: 600; color: #374151; margin-bottom: 6px; display: block; }
        .vi-search-input { border: 1.5px solid #d1d5db; border-radius: 8px; font-size: 14px; transition: border-color .2s, box-shadow .2s; }
        .vi-search-input:focus { border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(79,70,229,.1); outline: none; }
        .vi-btn-search { background: linear-gradient(135deg,#4f46e5,#7c3aed); color: #fff; border: none; border-radius: 8px; font-weight: 600; font-size: 16px; padding: 9px 20px; transition: opacity .2s; }
        .vi-btn-search:hover { opacity: .88; color: #fff; }
        .vi-btn-reset { background: #f1f5f9; color: #374151; border: 1.5px solid #d1d5db; border-radius: 8px; font-size: 16px; padding: 8px 14px; transition: background .2s; }
        .vi-btn-reset:hover { background: #e2e8f0; color: #374151; }

        /* ── Table ── */
        #table-one { border-collapse: separate; border-spacing: 0 5px; }
        #table-one thead tr { background: linear-gradient(90deg,#1e1b4b,#3730a3,#4f46e5); }
        #table-one thead th { color: #fff !important; font-size: 16px; font-weight: 600; border: none !important; padding: 14px 16px; }
        #table-one tbody tr { box-shadow: 0 0 0 1px #dde3ec; border-radius: 6px; }
        #table-one tbody tr:hover { background: #f8f7ff; box-shadow: 0 0 0 1px #b8c2d4; }
        #table-one tbody td { padding: 14px 16px; vertical-align: middle; border: none !important; background: transparent; }
        #table-one tbody td:first-child { border-radius: 6px 0 0 6px; }
        #table-one tbody td:last-child { border-radius: 0 6px 6px 0; }

        .vi-plate-link { font-size: 15px; color: #4f46e5; text-decoration: none; font-weight: 700; }
        .vi-plate-link:hover { text-decoration: underline; color: #3730a3; }

        /* History */
        .vi-history-row { display: flex; align-items: center; gap: 8px; padding: 8px 0; flex-wrap: wrap; border-bottom: 1px solid #f1f5f9; }
        .vi-history-row:last-child { border-bottom: none; padding-bottom: 0; }
        .vi-history-num { font-size: 14px; color: #64748b; font-weight: 600; white-space: nowrap; min-width: 72px; }

        /* Report rows */
        .vi-report-row { padding: 8px 0; border-bottom: 1px solid #f1f5f9; width: 100%; text-align: center; }
        .vi-report-row:last-child { border-bottom: none; padding-bottom: 0; }

        /* Status badges */
        .vi-status-badge { display: inline-block; padding: 3px 12px; border-radius: 20px; font-size: 14px; font-weight: 600; }
        .vi-badge-pass { background: #dcfce7; color: #15803d; }
        .vi-badge-warn { background: #fef3c7; color: #b45309; }
        .vi-badge-fail { background: #fee2e2; color: #b91c1c; }

        /* Report button */
        .vi-btn-report { display: inline-block; background: #ede9fe; color: #5b21b6; border: none; border-radius: 6px; font-size: 14px; font-weight: 600; padding: 5px 14px; white-space: nowrap; text-decoration: none; transition: background .2s, color .2s; }
        .vi-btn-report:hover { background: #ddd6fe; color: #4c1d95; }

        /* Fail detail button */
        .vi-btn-fail-detail { display: inline-block; background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px; font-weight: 600; padding: 2px 10px; cursor: pointer; transition: background .2s, color .2s; vertical-align: middle; }
        .vi-btn-fail-detail:hover { background: #e2e8f0; color: #334155; }

        /* Modal fail items */
        .fail-item-row { display: flex; gap: 10px; padding: 10px 0; border-bottom: 1px solid #f1f5f9; align-items: flex-start; }
        .fail-item-row:last-child { border-bottom: none; }
        .fail-item-name { flex: 1; font-size: 14px; color: #1e293b; }
        .fail-item-cat { font-size: 11px; color: #94a3b8; display: block; margin-bottom: 2px; }
        .fail-item-comment { font-size: 14px; color: #b91c1c; margin-top: 3px; }
        .fail-item-icon { color: #f43f5e; font-size: 16px; flex-shrink: 0; padding-top: 2px; }

        /* DataTables overrides */
        .dataTables_wrapper .dataTables_filter input { border: 1.5px solid #d1d5db; border-radius: 8px; padding: 6px 12px; font-size: 14px; }
        .dataTables_wrapper .dataTables_length select { border: 1.5px solid #d1d5db; border-radius: 8px; padding: 4px 8px; }
        .dataTables_wrapper .paginate_button.current,
        .dataTables_wrapper .paginate_button.current:hover { background: #4f46e5 !important; color: #fff !important; border-color: #4f46e5 !important; border-radius: 6px; }
        .dataTables_wrapper .paginate_button:hover { background: #ede9fe !important; color: #4f46e5 !important; border-color: transparent !important; border-radius: 6px; }
        .dataTables_wrapper { padding: 16px; }
    </style>
@endpush
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="d-flex align-items-center user-member__title mb-30 mt-30">
                <h4 class="text-capitalize">รายการตรวจรถ</h4>
            </div>

            <!-- Filter stat cards -->
            <div class="row mb-4 g-3">
                <div class="col-6 col-md-3">
                    <a href="{{ route('company.vehicles.inform', ['filter' => 'all']) }}" class="text-decoration-none">
                        <div class="stat-filter-card stat-all {{ $filter == 'all' ? 'stat-active' : '' }}">
                            <div class="stat-filter-main">
                                <i class="uil uil-clipboard-alt stat-filter-icon"></i>
                                <span class="stat-filter-count">{{ $totalInspections }}</span>
                            </div>
                            <div class="stat-filter-label">รายการทั้งหมด</div>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-md-3">
                    <a href="{{ route('company.vehicles.inform', ['filter' => 'passed']) }}" class="text-decoration-none">
                        <div class="stat-filter-card stat-pass {{ $filter == 'passed' ? 'stat-active' : '' }}">
                            <div class="stat-filter-main">
                                <i class="uil uil-check-circle stat-filter-icon"></i>
                                <span class="stat-filter-count">{{ $passedInspections }}</span>
                            </div>
                            <div class="stat-filter-label">ผ่านการตรวจ</div>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-md-3">
                    <a href="{{ route('company.vehicles.inform', ['filter' => 'waiting']) }}" class="text-decoration-none">
                        <div class="stat-filter-card stat-wait {{ $filter == 'waiting' ? 'stat-active' : '' }}">
                            <div class="stat-filter-main">
                                <i class="uil uil-clock stat-filter-icon"></i>
                                <span class="stat-filter-count">{{ $waitingInspections }}</span>
                            </div>
                            <div class="stat-filter-label">รอตรวจอีกครั้ง</div>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-md-3">
                    <a href="{{ route('company.vehicles.inform', ['filter' => 'failed']) }}" class="text-decoration-none">
                        <div class="stat-filter-card stat-fail {{ $filter == 'failed' ? 'stat-active' : '' }}">
                            <div class="stat-filter-main">
                                <i class="uil uil-times-circle stat-filter-icon"></i>
                                <span class="stat-filter-count">{{ $failedInspections }}</span>
                            </div>
                            <div class="stat-filter-label">ตรวจไม่ผ่าน</div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Search card -->
            <div class="card mb-4 border-0 shadow-sm overflow-hidden">
                <div class="card-header py-3 d-flex align-items-center gap-2" style="background: linear-gradient(135deg,#1e1b4b,#3730a3,#4f46e5); border: none;">
                    <i class="uil uil-search text-white fs-18"></i>
                    <h6 class="fw-600 mb-0 text-white" style="font-size:16px;">ค้นหาข้อมูลการตรวจรถ</h6>
                </div>
                <div class="card-body py-4 px-4" style="background: #f8f9fc;">
                    <form action="{{ route('company.vehicles.inform') }}" method="GET">
                        <input type="hidden" name="filter" value="{{ $filter }}">
                        <div class="row align-items-end g-3">
                            <div class="col-md-3">
                                <label class="vi-search-label">จากวันที่</label>
                                <input type="date" name="start_date" class="form-control vi-search-input" value="{{ request('start_date') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="vi-search-label">ถึงวันที่</label>
                                <input type="date" name="end_date" class="form-control vi-search-input" value="{{ request('end_date') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="vi-search-label">ผลประเมินล่าสุด</label>
                                <select name="evaluate_status" class="form-select vi-search-input">
                                    <option value="">-- แสดงทั้งหมด --</option>
                                    <option value="1" {{ request('evaluate_status') == '1' ? 'selected' : '' }}>ผ่าน</option>
                                    <option value="2" {{ request('evaluate_status') == '2' ? 'selected' : '' }}>ไม่ผ่าน แต่ใช้งานได้</option>
                                    <option value="3" {{ request('evaluate_status') == '3' ? 'selected' : '' }}>ไม่ผ่าน (ห้ามใช้งาน)</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn vi-btn-search flex-grow-1">
                                        <i class="uil uil-search me-1"></i>ค้นหา
                                    </button>
                                    <a href="{{ route('company.vehicles.inform') }}" class="btn vi-btn-reset">
                                        <i class="uil uil-redo"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- ตารางแสดงข้อมูล -->
            <div class="card mb-50 border-0 shadow-sm overflow-hidden">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0" id="table-one">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>ทะเบียนรถ / Supply</th>
                                    <th>ประวัติผลการตรวจ</th>
                                    <th class="text-center">รายงาน (Report)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($vehicles as $item)
                                    <tr>
                                        <td class="text-muted">{{ $loop->iteration }}</td>

                                        <td>
                                            <a href="{{ route('vehicles.show',['veh_id'=>$item->car_id]) }}" class="vi-plate-link fw-bold">{{ $item->car_plate }}</a>
                                            <br><small class="text-muted"><i class="uil uil-building"></i> {{ $item->supply_name ?? 'ไม่ระบุสาขา' }}</small>
                                        </td>

                                        <td>
                                            @if ($item->inspect_count > 0)
                                                <div class="d-flex flex-column">
                                                    @foreach($item->history as $index => $record)
                                                        @if($record->chk_status == '1')
                                                            <div class="vi-history-row">
                                                                <span class="vi-history-num">ครั้งที่ {{ $item->inspect_count - $index }}</span>
                                                                @if ($record->evaluate_status == 1)
                                                                    <span class="vi-status-badge vi-badge-pass">ปกติ อนุญาตใช้งาน</span>
                                                                @elseif ($record->evaluate_status == 2)
                                                                    <span class="vi-status-badge vi-badge-warn">ไม่ปกติ แต่สามารถใช้งานได้</span>
                                                                    <button class="vi-btn-fail-detail" onclick="loadFailedItems('{{ $record->record_id }}','{{ $item->car_plate }}','{{ $record->updated_at ? thai_date($record->updated_at) : "-" }}')">
                                                                       ข้อที่ไม่ผ่าน
                                                                    </button>
                                                                @elseif ($record->evaluate_status == 3)
                                                                    <span class="vi-status-badge vi-badge-fail">ไม่ปกติ ห้ามใช้งาน</span>
                                                                    <button class="vi-btn-fail-detail" onclick="loadFailedItems('{{ $record->record_id }}','{{ $item->car_plate }}','{{ $record->updated_at ? thai_date($record->updated_at) : "-" }}')">
                                                                       ข้อที่ไม่ผ่าน
                                                                    </button>
                                                                    @if ($record->next_inspect_date)
                                                                        <br><small class="text-danger ms-1">ตรวจซ้ำ: {{ thai_date(\Carbon\Carbon::parse($record->next_inspect_date)) }}</small>
                                                                    @endif
                                                                @endif
                                                            </div>
                                                        @elseif(in_array($record->chk_status, ['0', '2']) && is_null($record->evaluate_status))
                                                            <div class="vi-history-row">
                                                                <span class="vi-history-num">ครั้งที่ {{ $item->inspect_count - $index }}</span>
                                                                <span class="vi-status-badge vi-badge-fail">ไม่ผ่าน</span>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-muted small">-</span>
                                            @endif
                                        </td>

                                        <td class="text-center">
                                            @if ($item->inspect_count > 0)
                                                <div class="d-flex flex-column">
                                                    @foreach($item->history as $index => $record)
                                                        @if ($record->chk_status === '1')
                                                            <div class="vi-report-row">
                                                                <a href="{{ route('inspection.report', $record->record_id) }}" class="vi-btn-report">
                                                                    <i class="uil uil-file-alt me-1"></i>Report ครั้งที่ {{ $item->inspect_count - $index }}
                                                                </a>
                                                            </div>
                                                        @elseif(in_array($record->chk_status, ['0', '2']) && is_null($record->evaluate_status))
                                                            <div class="vi-report-row">
                                                                <span class="text-muted small">-</span>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-muted small">ไม่มีเอกสาร</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Modal: ข้อที่ไม่ผ่าน -->
<div class="modal fade" id="failItemsModal" tabindex="-1" aria-labelledby="failItemsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header py-3 d-block" style="background: linear-gradient(135deg,#be123c,#f43f5e); border: none;">
                <div class="d-flex align-items-center justify-content-between">
                    <h6 class="modal-title text-white fw-600 mb-0" id="failItemsModalLabel" style="font-size:16px;">
                        <i class="uil uil-times-circle me-2"></i>ข้อที่ตรวจไม่ผ่าน
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="mt-2 d-flex gap-3" style="font-size:13px; color:rgba(255,255,255,.85);">
                    <span><i class="uil uil-car me-1"></i><span id="modalCarPlate">-</span></span>
                    <span><i class="uil uil-calendar-alt me-1"></i> วันที่ตรวจ <span id="modalInspectDate">-</span></span>
                </div>
            </div>
            <div class="modal-body px-4 py-3" id="failItemsBody">
                <div class="text-center py-4 text-muted">
                    <div class="spinner-border spinner-border-sm me-2"></div> กำลังโหลด...
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
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
                    paginate: { next: "ถัดไป", previous: "ก่อนหน้า" }
                }
            });
        });

        function loadFailedItems(recordId, plate, inspectDate) {
            const modal = new bootstrap.Modal(document.getElementById('failItemsModal'));
            $('#modalCarPlate').text(plate ?? '-');
            $('#modalInspectDate').text(inspectDate ?? '-');
            $('#failItemsBody').html('<div class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm me-2"></div> กำลังโหลด...</div>');
            modal.show();

            $.get('{{ route("company.vehicles.fail_items", ":id") }}'.replace(':id', recordId))
                .done(function(items) {
                    if (!items.length) {
                        $('#failItemsBody').html('<div class="text-center py-4 text-muted"><i class="uil uil-check-circle fs-24 d-block mb-2 text-success"></i>ไม่พบข้อที่ไม่ผ่าน</div>');
                        return;
                    }

                    let lastCat = null;
                    let html = '';
                    items.forEach(function(item) {
                        if (item.category_name !== lastCat) {
                            if (lastCat !== null) html += '</div>';
                            html += `<div class="mb-1 mt-3"><span class="dm-tag" style="background:#fee2e2;color:#b91c1c;font-size:16px;padding:4px 12px;">${item.category_name ?? 'ไม่ระบุหมวด'}</span></div><div>`;
                            lastCat = item.category_name;
                        }
                        html += `<div class="fail-item-row">
                            <i class="uil uil-times-circle fail-item-icon"></i>
                            <div class="fail-item-name">
                                ${item.item_name}
                                ${item.result_value ? `<span class="fail-item-comment">${item.result_value}</span>` : ''}
                                ${item.user_comment ? `<br><span class="fail-item-comment">สิ่งที่ตรวจพบ: ${item.user_comment}</span>` : ''}
                            </div>
                        </div>`;
                    });
                    if (lastCat !== null) html += '</div>';
                    $('#failItemsBody').html(html);
                })
                .fail(function() {
                    $('#failItemsBody').html('<div class="text-center py-4 text-danger">เกิดข้อผิดพลาด กรุณาลองใหม่</div>');
                });
        }
    </script>
@endpush
