@section('title', 'การตรวจรถ')
@section('description', 'ID Drives - ระบบตรวจมาตรฐานรถ')
@extends('layout.app')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/custom-mobile.css') }}">
    <style>
        /* ============================================== */
        /* Status buttons (existing - unchanged)            */
        /* ============================================== */
        .btn-status-custom { background-color: #F4F6F9; border: 1px solid #E4E7EC !important; color: #4A5568; font-weight: 600 !important; border-radius: 8px !important; transition: all 0.25s ease; }
        .btn-status-custom:hover { background-color: #E2E8F0; }
        .btn-check:checked+.btn-pass-custom { background-color: #10B981 !important; border-color: #10B981 !important; color: #ffffff !important; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25) !important; }
        .btn-check:checked+.btn-warning-custom { background-color: #F59E0B !important; border-color: #F59E0B !important; color: #ffffff !important; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.25) !important; }
        .btn-check:checked+.btn-fail-custom { background-color: #EF4444 !important; border-color: #EF4444 !important; color: #ffffff !important; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.25) !important; }
        .checklist-card { border-radius: 12px !important; border: 1px solid #EAEDF2 !important; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02) !important; }

        /* ============================================== */
        /* Compact header (form name + plate + summary btn) */
        /* ============================================== */
        .compact-header {
            background: #fff;
            border-radius: 12px;
            padding: 12px 14px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
            border: 1px solid #EAEDF2;
        }
        .compact-header .form-title {
            font-size: 18px;
            font-weight: 700;
            color: #1a1a1a;
            line-height: 1.3;
            margin: 0;
        }
        .compact-header .plate-badge {
            display: inline-block;
            background: #F0F4FF;
            color: #5F63F2;
            font-weight: 700;
            font-size: 18px;
            padding: 2px 10px;
            border-radius: 6px;
            margin-top: 2px;
        }

        /* ============================================== */
        /* Sticky horizontal tabs container                  */
        /* ============================================== */
        .tabs-sticky-wrapper {
            position: sticky;
            top: 0;
            z-index: 100;
            background: #F4F5F7;
            padding: 8px 0;
            margin-bottom: 12px;
            transition: box-shadow 0.2s;
        }
        .tabs-sticky-wrapper.is-pinned {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
        }

        /* The scroll container */
        .tabs-scroll-container {
            position: relative;
        }

        /* Horizontal scrollable tab strip */
        .category-tabs-scroll {
            display: flex;
            gap: 8px;
            overflow-x: auto;
            scroll-behavior: smooth;
            padding: 4px 8px;
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .category-tabs-scroll::-webkit-scrollbar { display: none; }

        /* Single tab pill */
        .cat-tab {
            flex: 0 0 auto;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            background: #ffffff;
            border: 1px solid #D9E0E7;
            border-radius: 999px;
            color: #5A6A85;
            font-size: 16px;
            font-weight: 600;
            white-space: nowrap;
            cursor: pointer;
            transition: all 0.2s ease;
            user-select: none;
        }
        .cat-tab:hover {
            background: #F4F5F7;
            color: #5F63F2;
        }
        .cat-tab.active-cat {
            background: #5F63F2;
            border-color: #5F63F2;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(95, 99, 242, 0.25);
        }

        /* Progress dot inside each tab */
        .cat-tab .progress-dot {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            display: inline-block;
            flex-shrink: 0;
        }
        .cat-tab .progress-dot.empty    { background: transparent; border: 1.5px solid #B8BFCC; }
        .cat-tab .progress-dot.partial  { background: #F59E0B; }
        .cat-tab .progress-dot.complete { background: #10B981; }

        /* When tab is active, dots get a white outline for contrast */
        .cat-tab.active-cat .progress-dot.empty    { border-color: #ffffff; }
        .cat-tab.active-cat .progress-dot.partial  { box-shadow: 0 0 0 1.5px #ffffff; }
        .cat-tab.active-cat .progress-dot.complete { box-shadow: 0 0 0 1.5px #ffffff; }

        /* ============================================== */
        /* Scroll indicators (PC) - left/right gradient + arrow */
        /* ============================================== */
        .scroll-arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #ffffff;
            border: 1px solid #E3E6EF;
            display: none;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            color: #5F63F2;
            z-index: 5;
            transition: all 0.2s;
        }
        .scroll-arrow:hover {
            background: #5F63F2;
            color: #ffffff;
            transform: translateY(-50%) scale(1.1);
        }
        .scroll-arrow.left  { left: 4px; }
        .scroll-arrow.right { right: 4px; }
        .scroll-arrow.is-visible { display: flex; }

        /* Fade gradient at edges to hint scrollability */
        .scroll-fade {
            position: absolute;
            top: 0;
            bottom: 0;
            width: 30px;
            pointer-events: none;
            z-index: 4;
            opacity: 0;
            transition: opacity 0.2s;
        }
        .scroll-fade.left  { left: 0; background: linear-gradient(to right, #F4F5F7, transparent); }
        .scroll-fade.right { right: 0; background: linear-gradient(to left, #F4F5F7, transparent); }
        .scroll-fade.is-visible { opacity: 1; }

        /* Show arrows on desktop only (hover-capable devices with mouse) */
        @media (hover: hover) and (pointer: fine) {
            .scroll-arrow.has-overflow { display: flex; }
        }

        /* ============================================== */
        /* Back to top button (existing - unchanged)         */
        /* ============================================== */
        #backToTop {
            position: fixed; bottom: 25px; right: 25px; z-index: 999; display: none;
            width: 45px; height: 45px; border-radius: 50%;
            background: #5F63F2; color: #fff; border: none;
            box-shadow: 0 4px 12px rgba(95, 99, 242, 0.4);
            cursor: pointer; transition: all 0.3s;
        }
        #backToTop:hover { transform: translateY(-3px); background: #4448dc; }

        /* ============================================== */
        /* AJAX loader (existing - unchanged)                */
        /* ============================================== */
        .ajax-loader-overlay {
            position: absolute; top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(255, 255, 255, 0.6); z-index: 10;
            display: flex; justify-content: center; align-items: flex-start;
            padding-top: 50px; border-radius: 12px;
        }
        /* Force hide when d-none is applied (Bootstrap utility override) */
        .ajax-loader-overlay.d-none {
            display: none !important;
        }
        /* ============================================== */
        /* Round indicator banner (rounds 2/3 only)         */
        /* ============================================== */
        .round-indicator {
            background: linear-gradient(135deg, #FEF3C7 0%, #FDE68A 100%);
            border: 1px solid #F59E0B;
            border-radius: 10px;
            padding: 10px 14px;
            margin-bottom: 12px;
            font-size: 13px;
            color: #78350F;
        }
        .round-indicator strong { color: #78350F; }

        /* ============================================== */
        /* Readonly checklist card (already-passed items)   */
        /* ============================================== */
        .checklist-card.is-readonly {
            background: #F9FAFB !important;
            border: 1px solid #E5E7EB !important;
            opacity: 0.85;
        }
        .checklist-card.is-readonly .card-body {
            position: relative;
        }
        .readonly-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: #D1FAE5;
            color: #065F46;
            padding: 3px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            margin-left: 8px;
        }
        .readonly-status-display {
            background: #FFFFFF;
            border: 1px dashed #10B981;
            color: #065F46;
            padding: 8px 14px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            font-size: 13px;
        }
        /* ============================================== */
        /* Previous round info (collapsible)                */
        /* ============================================== */
        .prev-round-toggle {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: transparent;
            border: 1px dashed #B8BFCC;
            border-radius: 8px;
            padding: 6px 12px;
            font-size: 12px;
            font-weight: 600;
            color: #5A6A85;
            cursor: pointer;
            transition: all 0.2s;
            margin-bottom: 12px;
        }
        .prev-round-toggle:hover {
            background: #F4F5F7;
            color: #5F63F2;
            border-color: #5F63F2;
        }
        .prev-round-toggle .arrow-icon {
            transition: transform 0.25s;
        }
        .prev-round-toggle.prev-open .arrow-icon {
            transform: rotate(180deg);
        }

        .prev-round-panel {
            border-radius: 10px;
            padding: 12px 14px;
            margin-bottom: 12px;
            font-size: 13px;
            display: none;
        }
        .prev-round-panel.prev-open {
            display: block;
            animation: slideDown 0.25s ease;
        }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-6px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Status-based colors */
        .prev-round-panel.status-fail {
            background: #FEF2F2;
            border: 1px solid #FCA5A5;
            color: #7F1D1D;
        }
        .prev-round-panel.status-almost {
            background: #FFFBEB;
            border: 1px solid #FCD34D;
            color: #78350F;
        }

        .prev-round-row {
            display: flex;
            gap: 8px;
            align-items: flex-start;
            margin-bottom: 8px;
        }
        .prev-round-row:last-child { margin-bottom: 0; }
        .prev-round-row .label {
            font-weight: 700;
            min-width: 80px;
            flex-shrink: 0;
        }
        .prev-round-row .value {
            flex: 1;
            word-break: break-word;
        }

        .prev-round-images {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-top: 4px;
        }
        .prev-round-images img {
            height: 64px;
            width: 64px;
            object-fit: cover;
            border-radius: 6px;
            border: 1px solid #fff;
            cursor: pointer;
            transition: transform 0.15s;
        }
        .prev-round-images img:hover {
            transform: scale(1.05);
        }

              /* Lightbox for image preview */
        .prev-img-lightbox {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.85);
            display: none ;        /* Force hidden by default */
            justify-content: center;
            align-items: center;
            z-index: 9999;
            padding: 20px;
            cursor: zoom-out;
        }
        .prev-img-lightbox.lightbox-open {
            display: flex !important;        /* Force visible when open */
        }
        .prev-img-lightbox img {
            max-width: 100%;
            max-height: 100%;
            border-radius: 8px;
        }

        
    </style>
@endpush

@section('content')
  

   <div class="container-fluid py-3">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8 col-xl-7">

            {{-- ============================================== --}}
            {{-- Compact header: form name + plate + summary btn --}}
            {{-- ============================================== --}}
            <div class="compact-header d-flex justify-content-between align-items-center mb-2">
                <div class="d-flex flex-column" style="min-width: 0; flex: 1;">
                    <span class="form-title text-truncate">
                        {{ $formGroup->form_group_name ?? 'แบบฟอร์มการตรวจ' }}
                    </span>
                    <span class="plate-badge align-self-start mt-1">
                     ทะเบียน : {{ $vehicle->car_plate ?? 'ไม่ระบุ' }}
                    </span>
                </div>
                <a href="{{ route('inspection.step4', $record->record_id) }}"
                    class="btn btn-success btn-sm radius-xs shadow-sm fw-bold ms-2 flex-shrink-0">
                    สรุปผล <i class="uil uil-arrow-right ms-1"></i>
                </a>
            </div>

            {{-- ============================================== --}}
            {{-- Sticky horizontal scrollable category tabs       --}}
            {{-- ============================================== --}}
            <div class="tabs-sticky-wrapper" id="tabs-sticky-wrapper">
                <div class="tabs-scroll-container">

                    {{-- Left scroll arrow (desktop) --}}
                    <button type="button" class="scroll-arrow left" id="scroll-arrow-left" aria-label="เลื่อนซ้าย">
                        <i class="uil uil-angle-left fs-18"></i>
                    </button>

                    {{-- Left fade gradient --}}
                    <div class="scroll-fade left" id="scroll-fade-left"></div>

                    {{-- The actual scrollable tabs --}}
                    <div class="category-tabs-scroll" id="category-tabs-scroll">
                        @foreach ($categories as $cat)
                            @php
                                $progress = $categoryProgress[$cat->category_id] ?? ['status' => 'empty', 'checked' => 0, 'total' => 0];
                                $isActive = ($currentCategoryId == $cat->category_id);
                            @endphp
                            <button type="button"
                                data-url="{{ route('inspection.step3', ['record_id' => $record->record_id, 'cat_id' => $cat->category_id]) }}"
                                data-cat-id="{{ $cat->category_id }}"
                                class="cat-tab category-ajax-btn {{ $isActive ? 'active-cat' : '' }}"
                                title="{{ $cat->chk_cats_name }} ({{ $progress['checked'] }}/{{ $progress['total'] }})">
                                <span class="progress-dot {{ $progress['status'] }}"></span>
                                <span>{{ $cat->chk_cats_name }}</span>
                            </button>
                        @endforeach
                    </div>

                    {{-- Right fade gradient --}}
                    <div class="scroll-fade right" id="scroll-fade-right"></div>

                    {{-- Right scroll arrow (desktop) --}}
                    <button type="button" class="scroll-arrow right" id="scroll-arrow-right" aria-label="เลื่อนขวา">
                        <i class="uil uil-angle-right fs-18"></i>
                    </button>
                </div>
            </div>

                  {{-- Round indicator (only show on rounds 2 and 3) --}}
                @if ($isRetestRound)
                    <div class="round-indicator d-flex align-items-center">
                        <i class="uil uil-redo fs-20 me-2"></i>
                        <div class="fs-14">
                            กำลังตรวจ<strong> ครั้งที่ {{ $roundNumber }}</strong> — 
                            ตรวจเฉพาะข้อที่ไม่ผ่านในรอบก่อน 
                        </div>
                    </div>
                @endif
            
                <div id="checklist-container" class="position-relative min-vh-50">
                    
                    <div id="ajax-spinner" class="ajax-loader-overlay d-none">
                        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;"></div>
                    </div>

                    <div class="d-flex flex-column gap-3" id="checklist-items-wrapper">
                        @foreach ($items as $item)
                            @php
                                $result = $existingResults->get($item->item_id);
                                $status = $result ? $result->result_status : '';
                                $val = $result ? $result->result_value : '';
                                $comment = $result ? $result->user_comment : '';
                                $images = $existingImages->get($item->item_id) ?? collect();

                                $isType2 = $item->item_type == 2;
                                $lblPass = $isType2 ? 'ปกติ' : 'ผ่าน';
                                $lblAlmost = $isType2 ? 'ไม่ปกติ แต่ยังสามารถใช้งานได้' : 'ผ่าน แต่แก้ไข';
                                $lblFail = $isType2 ? 'ไม่ปกติ' : 'ไม่ผ่าน';

                                // Determine if this item is readonly (passed in previous round)
                                $isReadonly = $isRetestRound && in_array($item->item_id, $passedItemIds);
                            @endphp

                            <div class="card checklist-card {{ $isReadonly ? 'is-readonly' : '' }}">
                                <div class="card-body p-4">
                                    <div class="mb-3">
                                        <div class="fw-bold text-dark lh-base fs-16 d-flex align-items-start flex-wrap">
                                            <span>{{ $loop->iteration }}. {{ $item->item_name }}</span>
                                            @if ($isReadonly)
                                                <span class="readonly-badge fs-14">
                                                    <i class="uil uil-check-circle"></i> ผ่าน
                                                </span>
                                            @endif
                                        </div>
                                        @if (!empty($item->item_description) && !$isReadonly)
                                            <div class="text-danger fs-14 mt-1">{!! nl2br(e($item->item_description)) !!}</div>
                                        @endif
                                    </div>

                                    <input type="hidden" class="data-record" value="{{ $record->record_id }}">
                                    <input type="hidden" class="data-item" value="{{ $item->item_id }}">

                                    {{-- READONLY MODE: Show locked result instead of editable controls --}}
                                    @if ($isReadonly)
                                        <div class="readonly-status-display">
                                            <i class="uil uil-lock fs-18"></i>
                                            <span>ผลการตรวจ: <strong>{{ $lblPass }}</strong></span>
                                        </div>
                                    @else
                                    
                                        {{-- ============================================== --}}
                                        {{-- Previous round info (collapsible) - retest only --}}
                                        {{-- Show only for items that failed in previous round --}}
                                        {{-- ============================================== --}}
                                        @if ($isRetestRound && $previousResults->has($item->item_id))
                                            @php
                                                $prevResult = $previousResults->get($item->item_id);
                                                $prevImages = $previousImages->get($item->item_id) ?? collect();
                                                $prevStatus = $prevResult->result_status;

                                                // Only show panel for fail (0) or almost (2) - not for passes (1)
                                                $showPrevPanel = in_array($prevStatus, ['0', '2']);
                                                $statusClass = $prevStatus === '0' ? 'status-fail' : 'status-almost';
                                                $statusLabel = $prevStatus === '0'
                                                    ? ($item->item_type == 2 ? 'ไม่ปกติ' : 'ไม่ผ่าน')
                                                    : ($item->item_type == 2 ? 'ไม่ปกติ แต่ยังสามารถใช้งานได้' : 'ผ่าน แต่แก้ไข');
                                                $statusIcon = $prevStatus === '0' ? 'uil-times-circle' : 'uil-exclamation-triangle';
                                            @endphp

                                            @if ($showPrevPanel)
                                                <button type="button"
                                                    class="prev-round-toggle"
                                                    data-target="prev_panel_{{ $item->item_id }}">
                                                   
                                                    <span class="fs-14">ดูข้อมูลจากการตรวจครั้งล่าสุด</span>
                                                    <i class="uil uil-angle-down arrow-icon fs-16"></i>
                                                </button>

                                                <div class="prev-round-panel {{ $statusClass }}"
                                                    id="prev_panel_{{ $item->item_id }}">
                                                    <div class="prev-round-row">
                                                        <span class="label fs-14">
                                                            <i class="uil {{ $statusIcon }}"></i> ผลตรวจ:
                                                        </span>
                                                        <span class="value fs-14"><strong>{{ $statusLabel }}</strong></span>
                                                    </div>

                                                    @if (!empty($prevResult->result_value))
                                                        <div class="prev-round-row">
                                                            <span class="label">
                                                                ข้อมูลเพิ่มเติม:
                                                            </span>
                                                            <span class="value">{{ $prevResult->result_value }}</span>
                                                        </div>
                                                    @endif

                                                    @if (!empty($prevResult->user_comment))
                                                        <div class="prev-round-row">
                                                            <span class="label">
                                                              สิ่งที่ตรวจพบ:
                                                            </span>
                                                            <span class="value"><em>{{ $prevResult->user_comment }}</em></span>
                                                        </div>
                                                    @endif

                                                    @if ($prevImages->count() > 0)
                                                        <div class="prev-round-row">
                                                            <span class="label">
                                                                <i class="uil uil-camera"></i> รูปภาพ:
                                                            </span>
                                                            <div class="value">
                                                                <div class="prev-round-images">
                                                                    @foreach ($prevImages as $img)
                                                                        <img src="{{ asset($img->image_path) }}"
                                                                            class="prev-img-thumb"
                                                                            alt="รูปจากรอบก่อน">
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                        @endif

                                    
                                        {{-- EDITABLE MODE (existing logic - unchanged) --}}
                                        @if (in_array($item->item_type, [1, 2, 6]))
                                            <div class="d-flex flex-column gap-2 mb-3">
                                                <div class="row g-2">
                                                    <div class="col-6">
                                                        <input type="radio" class="btn-check btn-pass" name="status_{{ $item->item_id }}" id="pass_{{ $item->item_id }}" value="1" {{ ($status === '1' || $status === '') ? 'checked' : '' }} {{ $status === '' ? 'data-new="1"' : '' }}>
                                                        <label class="btn btn-status-custom btn-pass-custom w-100 py-2 d-flex justify-content-center align-items-center gap-2" for="pass_{{ $item->item_id }}">
                                                            <i class="uil uil-check-circle fs-18"></i> <span>{{ $lblPass }}</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-6">
                                                        <input type="radio" class="btn-check btn-fail" name="status_{{ $item->item_id }}" id="fail_{{ $item->item_id }}" value="0" {{ $status === '0' ? 'checked' : '' }}>
                                                        <label class="btn btn-status-custom btn-fail-custom w-100 py-2 d-flex justify-content-center align-items-center gap-2" for="fail_{{ $item->item_id }}">
                                                            <i class="uil uil-times-circle fs-18"></i> <span>{{ $lblFail }}</span>
                                                        </label>
                                                    </div>
                                                </div>

                                                @if ($item->item_type == 1 || $item->item_type == 2)
                                                    <div class="w-100">
                                                        <input type="radio" class="btn-check btn-warning" name="status_{{ $item->item_id }}" id="almost_{{ $item->item_id }}" value="2" {{ $status === '2' ? 'checked' : '' }}>
                                                        <label class="btn btn-status-custom btn-warning-custom w-100 py-2 fs-13 d-flex align-items-center justify-content-center gap-2" for="almost_{{ $item->item_id }}">
                                                            <i class="uil uil-exclamation-triangle fs-18"></i> <span>{{ $lblAlmost }}</span>
                                                        </label>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif

                                        @if (in_array($item->item_type, [3, 4, 5, 6]))
                                            <div class="mb-2">
                                                @if ($item->item_type == 3 || $item->item_type == 6)
                                                    <input type="text" class="form-control bg-light input-value radius-xs border-0" placeholder="ระบุข้อความ..." value="{{ $val }}">
                                                @elseif($item->item_type == 4)
                                                    <input type="date" class="form-control bg-light input-value radius-xs border-0" value="{{ $val }}">
                                                @elseif($item->item_type == 5)
                                                    <input type="number" class="form-control bg-light input-value radius-xs border-0" placeholder="ระบุตัวเลข..." value="{{ $val }}">
                                                @endif
                                            </div>
                                        @endif

                                        <div class="detail-box pt-3 mt-3 border-top {{ $status === '0' || $status === '2' ? '' : 'd-none' }}" id="detail_box_{{ $item->item_id }}">
                                            <label class="small fw-bold mb-2 {{ $status === '0' ? 'text-danger' : 'text-warning' }}">
                                                <i class="uil uil-comment-info"></i> สิ่งที่ตรวจพบ
                                            </label>
                                            <textarea class="form-control bg-light input-comment radius-xs border-0 mb-3" rows="2" placeholder="อธิบายเพิ่มเติม...">{{ $comment }}</textarea>

                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <label class="small text-dark fw-bold mb-0">ภาพประกอบ <span id="count_{{ $item->item_id }}">({{ $images->count() }}/10)</span></label>
                                                <button type="button" class="btn btn-outline-primary btn-xs radius-xs" onclick="document.getElementById('file_{{ $item->item_id }}').click();">
                                                    <i class="uil uil-camera-plus"></i> เพิ่มภาพ
                                                </button>
                                                <input type="file" id="file_{{ $item->item_id }}" class="d-none image-uploader" accept="image/*" capture="environment" data-item="{{ $item->item_id }}">
                                            </div>

                                            <div class="row g-2 image-gallery" id="gallery_{{ $item->item_id }}">
                                                @foreach ($images as $img)
                                                    <div class="col-3 col-md-2 position-relative img-wrapper-{{ $img->id }}">
                                                        <img src="{{ asset($img->image_path) }}" class="img-fluid rounded border" style="height: 70px; width: 100%; object-fit: cover;">
                                                        <button type="button" class="btn btn-danger btn-xs position-absolute top-0 end-0 m-1 rounded-circle p-0" style="width: 20px; height: 20px;" onclick="deleteImage({{ $img->id }}, '{{ $item->item_id }}')">
                                                            <i class="uil uil-times fs-12"></i>
                                                        </button>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                 {{-- Lightbox for previous round image preview --}}
                <div class="prev-img-lightbox" id="prevImgLightbox" style="display: none;">
                    <img src="" alt="">
                </div>

                <button id="backToTop" title="กลับขึ้นบนสุด">
                    <i class="uil uil-arrow-up" style="font-size: 20px;"></i>
                </button>

            </div>
        </div>
    </div>
 @push('scripts')
    <script>
        $(document).ready(function() {

            // ==========================================
            // Back to top button (existing - unchanged)
            // ==========================================
            const backToTopBtn = $('#backToTop');
            $(window).scroll(function() {
                if ($(window).scrollTop() > 300) backToTopBtn.fadeIn();
                else backToTopBtn.fadeOut();
            });
            backToTopBtn.click(function() {
                $('html, body').animate({ scrollTop: 0 }, 400);
                return false;
            });

            // ==========================================
            // Horizontal scrollable category tabs
            // - Auto-scroll active tab into view on load
            // - Show/hide scroll arrows on desktop
            // - Show/hide fade gradients
            // - Detect sticky pinned state for shadow
            // ==========================================
            const tabScroll      = document.getElementById('category-tabs-scroll');
            const arrowLeft      = document.getElementById('scroll-arrow-left');
            const arrowRight     = document.getElementById('scroll-arrow-right');
            const fadeLeft       = document.getElementById('scroll-fade-left');
            const fadeRight      = document.getElementById('scroll-fade-right');
            const stickyWrapper  = document.getElementById('tabs-sticky-wrapper');

            // Update scroll indicators visibility based on scroll position
            function updateScrollIndicators() {
                if (!tabScroll) return;

                const scrollLeft  = tabScroll.scrollLeft;
                const maxScroll   = tabScroll.scrollWidth - tabScroll.clientWidth;
                const hasOverflow = maxScroll > 2;

                // Show/hide left arrow + fade
                if (hasOverflow && scrollLeft > 5) {
                    arrowLeft.classList.add('has-overflow', 'is-visible');
                    fadeLeft.classList.add('is-visible');
                } else {
                    arrowLeft.classList.remove('has-overflow', 'is-visible');
                    fadeLeft.classList.remove('is-visible');
                }

                // Show/hide right arrow + fade
                if (hasOverflow && scrollLeft < maxScroll - 5) {
                    arrowRight.classList.add('has-overflow', 'is-visible');
                    fadeRight.classList.add('is-visible');
                } else {
                    arrowRight.classList.remove('has-overflow', 'is-visible');
                    fadeRight.classList.remove('is-visible');
                }
            }

            // Scroll arrow click handlers (desktop)
            arrowLeft.addEventListener('click', function() {
                tabScroll.scrollBy({ left: -200, behavior: 'smooth' });
            });
            arrowRight.addEventListener('click', function() {
                tabScroll.scrollBy({ left: 200, behavior: 'smooth' });
            });

            // Update indicators on scroll, resize
            tabScroll.addEventListener('scroll', updateScrollIndicators);
            window.addEventListener('resize', updateScrollIndicators);

            // Auto-scroll the active tab into view (centered)
            function scrollActiveTabIntoView() {
                const activeTab = tabScroll.querySelector('.cat-tab.active-cat');
                if (!activeTab) return;

                const tabRect       = activeTab.getBoundingClientRect();
                const containerRect = tabScroll.getBoundingClientRect();
                const offset        = (tabRect.left + tabRect.width / 2) - (containerRect.left + containerRect.width / 2);

                tabScroll.scrollBy({ left: offset, behavior: 'smooth' });
            }

            // Run on initial load
            setTimeout(function() {
                scrollActiveTabIntoView();
                updateScrollIndicators();
            }, 100);

            // Detect sticky pinned state (add shadow when stuck to top)
            const stickyObserver = new IntersectionObserver(
                function(entries) {
                    entries.forEach(function(entry) {
                        if (entry.intersectionRatio < 1) {
                            stickyWrapper.classList.add('is-pinned');
                        } else {
                            stickyWrapper.classList.remove('is-pinned');
                        }
                    });
                },
                { threshold: [1], rootMargin: '-1px 0px 0px 0px' }
            );

            // Create a sentinel element above the sticky wrapper to detect when it sticks
            const sentinel = document.createElement('div');
            sentinel.style.height = '1px';
            stickyWrapper.parentNode.insertBefore(sentinel, stickyWrapper);
            stickyObserver.observe(sentinel);

            // ==========================================
            // AJAX category switching (existing - kept, with new selector)
            // ==========================================
            $('.category-ajax-btn').click(function(e) {
                e.preventDefault();
                let btn = $(this);
                let url = btn.data('url');

                // Update active state
                $('.category-ajax-btn').removeClass('active-cat');
                btn.addClass('active-cat');

                // Center the newly active tab (mobile UX improvement)
                setTimeout(scrollActiveTabIntoView, 50);

                // Show loading spinner
                $('#ajax-spinner').removeClass('d-none');

                // Fetch new category content
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        let newHtml = $(response).find('#checklist-items-wrapper').html();
                        $('#checklist-items-wrapper').html(newHtml);

                        // Refresh progress dots after switching (re-fetch from response)
                        $(response).find('.cat-tab').each(function() {
                            const catId = $(this).data('cat-id');
                            const dotClass = $(this).find('.progress-dot').attr('class');
                            $('.cat-tab[data-cat-id="' + catId + '"] .progress-dot').attr('class', dotClass);
                        });

                        saveDefaults(document.getElementById('checklist-items-wrapper'));
                        $('#ajax-spinner').addClass('d-none');
                    },
                    error: function() {
                        alert('เกิดข้อผิดพลาดในการโหลดข้อมูล กรุณาลองใหม่อีกครั้ง');
                        $('#ajax-spinner').addClass('d-none');
                    }
                });
            });
          
           // ==========================================
            // Previous round info: collapsible toggle
            // ==========================================
            $(document).on('click', '.prev-round-toggle', function(e) {
                e.preventDefault();
                e.stopPropagation();   // Prevent bubbling to parent click handlers (e.g. category tabs)

                const targetId = $(this).data('target');
                const panel = $('#' + targetId);

                $(this).toggleClass('prev-open');
                panel.toggleClass('prev-open');
            });

            // ==========================================
            // Previous round images: lightbox preview
            // ==========================================
            $(document).on('click', '.prev-img-thumb', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const src = $(this).attr('src');
                $('#prevImgLightbox img').attr('src', src);
                $('#prevImgLightbox').addClass('lightbox-open')
            });

            $(document).on('click', '#prevImgLightbox', function() {
                $(this).removeClass('lightbox-open')
                $(this).find('img').attr('src', '');
            });
        });
    </script>
        
       <script>
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            function saveItemData(card) {
                const recordId = card.querySelector('.data-record').value;
                const itemId = card.querySelector('.data-item').value;
                let resultStatus = null;
                const checkedRadio = card.querySelector('input[type="radio"]:checked');
                if (checkedRadio) resultStatus = checkedRadio.value;
                let resultValue = null;
                const valueInput = card.querySelector('.input-value');
                if (valueInput) resultValue = valueInput.value;
                let userComment = null;
                const commentInput = card.querySelector('.input-comment');
                if (commentInput) userComment = commentInput.value;

                fetch('{{ route('inspection.saveResult') }}', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json', 
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json' // บังคับรับค่า JSON
                    },
                    body: JSON.stringify({ record_id: recordId, item_id: itemId, result_status: resultStatus, result_value: resultValue, user_comment: userComment })
                });
            }

            // Save pre-checked "ปกติ" items that have no existing result in DB
            function saveDefaults(container) {
                container.querySelectorAll('input.btn-pass[data-new]').forEach(function(radio) {
                    saveItemData(radio.closest('.card-body'));
                });
            }
            saveDefaults(document);

            // 🌟 ใช้งาน Event Delegation เพื่อให้ AJAX ทำงานร่วมกับฟังก์ชันได้ 🌟
            $(document).on('change', 'input[type="radio"].btn-check', function() {
                const card = this.closest('.card-body');
                const itemId = card.querySelector('.data-item').value;
                const detailBox = document.getElementById('detail_box_' + itemId);
                if (this.value === '0' || this.value === '2') {
                    detailBox.classList.remove('d-none');
                } else {
                    detailBox.classList.add('d-none');
                }
                saveItemData(card);
            });

            $(document).on('change', '.input-value, .input-comment', function() { 
                saveItemData(this.closest('.card-body')); 
            });

            // 🌟 อัปโหลดรูปภาพ (แก้ไขแล้ว) 🌟
            $(document).on('change', '.image-uploader', function() {
                const itemId = this.dataset.item;
                const card = this.closest('.card-body');
                const recordId = card.querySelector('.data-record').value;
                const file = this.files[0];
                if (!file) return;

                const gallery = document.getElementById('gallery_' + itemId);
                const tempId = Date.now();
                const objectUrl = URL.createObjectURL(file);
                const tempHtml = `
                    <div class="col-3 col-md-2 position-relative img-wrapper-temp-${tempId}">
                        <img src="${objectUrl}" class="img-fluid rounded border" style="height: 70px; width: 100%; object-fit: cover; opacity: 0.5;">
                        <div class="position-absolute top-50 start-50 translate-middle">
                            <i class="uil uil-spinner fa-spin text-primary fs-20"></i>
                        </div>
                    </div>`;
                gallery.insertAdjacentHTML('beforeend', tempHtml);

                const formData = new FormData();
                formData.append('image', file);
                formData.append('item_id', itemId);
                formData.append('record_id', recordId);

                fetch('{{ route('inspection.uploadItemImage') }}', { 
                    method: 'POST', 
                    headers: { 
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json' // บังคับรับค่า JSON
                    }, 
                    body: formData 
                })
                .then(res => res.text())
                .then(text => {
                    try {
                        const data = JSON.parse(text);
                        if (data.success || data.status === 'success') {
                            document.querySelector(`.img-wrapper-temp-${tempId}`).remove();
                            if (data.image_id) {
                                const finalHtml = `
                                    <div class="col-3 col-md-2 position-relative img-wrapper-${data.image_id}">
                                        <img src="${objectUrl}" class="img-fluid rounded border" style="height: 70px; width: 100%; object-fit: cover;">
                                        <button type="button" class="btn btn-danger btn-xs position-absolute top-0 end-0 m-1 rounded-circle p-0" style="width: 20px; height: 20px;" onclick="deleteImage(${data.image_id}, '${itemId}')">
                                            <i class="uil uil-times fs-12"></i>
                                        </button>
                                    </div>`;
                                gallery.insertAdjacentHTML('beforeend', finalHtml);
                                const countSpan = document.getElementById('count_' + itemId);
                                if (countSpan) {
                                    let currentCount = parseInt(countSpan.innerText.match(/\d+/)[0]) || 0;
                                    countSpan.innerText = `(${currentCount + 1}/10)`;
                                }
                            } else { 
                                // ถ้าไม่มี ID รูปกลับมา ให้คลิกโหลดหมวดหมู่ซ้ำแบบ AJAX
                                $('.category-ajax-btn.active-cat').trigger('click'); 
                            }
                        } else { 
                            alert('อัปโหลดไม่สำเร็จ กรุณาลองใหม่');
                            document.querySelector(`.img-wrapper-temp-${tempId}`).remove();
                        }
                    } catch (e) { 
                        // 🌟 แก้ปัญหาหน้ากระพริบ: ถ้าระบบส่งกลับมาไม่ใช่ JSON ให้โหลด AJAX ซ้ำแทนการรีเฟรชจอ
                        $('.category-ajax-btn.active-cat').trigger('click'); 
                    }
                })
                .catch(error => { 
                    console.error('Upload Error:', error); 
                    $('.category-ajax-btn.active-cat').trigger('click'); 
                });
                
                this.value = '';
            });

            // 🌟 ลบรูปภาพ (แก้ไขแล้ว) 🌟
            function deleteImage(imageId, itemId) {
                if (!confirm('ยืนยันการลบรูปภาพ?')) return;
                const imgWrapper = document.querySelector(`.img-wrapper-${imageId}`);
                if (imgWrapper) imgWrapper.style.opacity = '0.5';

                fetch('{{ route('inspection.deleteItemImage') }}', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json', 
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json' 
                    },
                    body: JSON.stringify({ image_id: imageId })
                })
                .then(res => res.text())
                .then(text => {
                    try {
                        const data = JSON.parse(text);
                        if (data.success || data.status === 'success') {
                            if (imgWrapper) imgWrapper.remove();
                            const countSpan = document.getElementById('count_' + itemId);
                            if (countSpan) {
                                let currentCount = parseInt(countSpan.innerText.match(/\d+/)[0]) || 0;
                                if (currentCount > 0) countSpan.innerText = `(${currentCount - 1}/10)`;
                            }
                        } else { $('.category-ajax-btn.active-cat').trigger('click'); }
                    } catch (e) { 
                        // 🌟 แก้ปัญหาหน้ากระพริบ: ถ้าระบบส่งกลับมาไม่ใช่ JSON ให้โหลด AJAX ซ้ำ
                        $('.category-ajax-btn.active-cat').trigger('click'); 
                    }
                })
                .catch(error => { console.error('Delete Error:', error); $('.category-ajax-btn.active-cat').trigger('click'); });
            }
        </script>
    @endpush 
@endsection