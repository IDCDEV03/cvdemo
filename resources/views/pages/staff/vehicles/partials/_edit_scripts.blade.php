{{-- ============================================ --}}
{{-- Reuse Select2 assets + styles from create     --}}
{{-- ============================================ --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

{{-- Reuse same Select2 override CSS from _create_scripts --}}
<style>
    .select2-container--bootstrap-5 .select2-selection { min-height:41px!important; padding:.45rem 1rem!important; border:1px solid #e3e6ef!important; border-radius:6px!important; font-size:14px!important; }
    .select2-container--bootstrap-5.select2-container--focus .select2-selection,
    .select2-container--bootstrap-5.select2-container--open .select2-selection { border-color:#5840ff!important; box-shadow:0 0 0 .2rem rgba(88,64,255,.15)!important; }
    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered { line-height:1.5!important; padding-left:0!important; }
    .select2-container--bootstrap-5 .select2-dropdown { border:1px solid #e3e6ef!important; border-radius:6px!important; }
    .select2-container--bootstrap-5 .select2-results__option--highlighted[aria-selected] { background-color:#5840ff!important; }
</style>

<script>
$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

// Edit form state
const VehicleForm = {
    currentStep    : 1,
    originalSupId  : $('#original_supply_id').val(),
    selectedSupply : {
        id     : '{{ $vehicle->supply_id }}',
        name   : '{{ $vehicle->supply_name }}',
        isFull : false,
    },
    plateIsUnique  : true, // pre-filled plate assumed valid
};
</script>

{{-- ============================================ --}}
{{-- Select2 Init                                  --}}
{{-- ============================================ --}}
<script>
$(document).ready(function () {
    const s2 = { 
        theme:'bootstrap-5', 
        width:'100%', 
        minimumResultsForSearch: 0,
        anguage:{ noResults:()=>'ไม่พบข้อมูล' } 
    };
    $('#supply_id').select2({ ...s2, placeholder:'-- กรุณาเลือก Supply --' });
    $('#province').select2({ ...s2, placeholder:'-- กรุณาเลือกจังหวัด --' });
    $('#car_brand').select2({ ...s2, placeholder:'-- เลือกยี่ห้อรถ --' });
    $('#car_type').select2({ ...s2, placeholder:'-- เลือกประเภทรถ --' });
});
</script>

{{-- ============================================ --}}
{{-- Step Navigation (same logic as create)        --}}
{{-- ============================================ --}}
<script>
$(document).ready(function () {

    function goToStep(n) {
        $('.step-content').removeClass('active');
        $(`.step-content[data-step-content="${n}"]`).addClass('active');
        $('.step-wizard .step').each(function () {
            const s = parseInt($(this).data('step'));
            $(this).removeClass('active completed');
            if (s < n) $(this).addClass('completed');
            else if (s === n) $(this).addClass('active');
        });
        VehicleForm.currentStep = n;
        $('html,body').animate({ scrollTop: $('.step-wizard').offset().top - 100 }, 300);
    }

    $('.btn-next').on('click', function () {
        if (!validateStep(VehicleForm.currentStep)) return;
        goToStep(parseInt($(this).data('next-step')));
    });

    $('.btn-prev').on('click', function () {
        goToStep(parseInt($(this).data('prev-step')));
    });

    $('.step-wizard .step').on('click', function () {
        const t = parseInt($(this).data('step'));
        if (t < VehicleForm.currentStep) goToStep(t);
    });

    function validateStep(n) {
        if (n === 1) return true; // company locked, always valid
        if (n === 2) {
            if (!$('#supply_id').val()) { alert('กรุณาเลือก Supply'); return false; }
            if (VehicleForm.selectedSupply.isFull) { alert('Supply นี้เต็มโควต้าแล้ว'); return false; }
            return true;
        }
        return true;
    }
});
</script>

{{-- ============================================ --}}
{{-- Step 2: Supply change + quota check           --}}
{{-- ============================================ --}}
<script>
$(document).ready(function () {

    $('#supply_id').on('change', function () {
        const supId   = $(this).val();
        const supName = $(this).find('option:selected').text().trim();
        if (!supId) return;

        VehicleForm.selectedSupply.id   = supId;
        VehicleForm.selectedSupply.name = supName;
        $('#step3_supply_name').text(supName);

        // If same supply as original → always OK
        if (supId === VehicleForm.originalSupId) {
            $('#limit_supply_name').text(supName);
            $('#supply_limit_box').removeClass('is-full');
            $('#limit_status_text').html('<span class="text-success"><i class="uil uil-check-circle"></i> Supply เดิม</span>');
            VehicleForm.selectedSupply.isFull = false;
            return;
        }

        // Different supply → check quota via AJAX
        $.ajax({
            url     : `{{ url('staff/vehicles/ajax/supply-info') }}/${encodeURIComponent(supId)}`,
            method  : 'GET',
            success : function (res) {
                if (!res.success) return;
                const d = res.data;
                VehicleForm.selectedSupply.isFull = d.is_full;

                $('#limit_supply_name').text(d.supply_name);
                $('#limit_current').text(d.current_count);
                $('#limit_max').text(d.vehicle_limit > 0 ? d.vehicle_limit : 'ไม่จำกัด');

                if (d.is_full) {
                    $('#supply_limit_box').addClass('is-full');
                    $('#limit_status_text').html('<span class="text-danger fw-bold"><i class="uil uil-times-circle"></i> เต็มโควต้าแล้ว</span>');
                } else {
                    $('#supply_limit_box').removeClass('is-full');
                    const remain = d.remaining !== null ? `เหลือ ${d.remaining} คัน` : 'ลงทะเบียนได้';
                    $('#limit_status_text').html(`<span class="text-success"><i class="uil uil-check-circle"></i> ${remain}</span>`);
                }
                $('#supply_limit_box').show();
            },
            error : function () {
                $('#supply_limit_box').hide();
            }
        });
    });
});
</script>

{{-- ============================================ --}}
{{-- Step 3: Plate uniqueness (exclude self)       --}}
{{-- ============================================ --}}
<script>
$(document).ready(function () {
    let plateTimer = null;
    const VEH_ID = '{{ $vehicle->car_id }}';

    function checkPlate() {
        clearTimeout(plateTimer);
        const plate    = $('#plate').val().trim();
        const province = $('#province').val();
        const supId    = $('#supply_id').val();
        if (!plate || !province || !supId) return;

        $('#plate_feedback').html('<small class="text-muted"><i class="uil uil-spinner-alt"></i> กำลังตรวจสอบ...</small>');

        plateTimer = setTimeout(function () {
            $.ajax({
                url    : "{{ route('staff.vehicles.ajax.check_plate') }}",
                method : 'GET',
                data   : { plate, province, supply_id: supId, exclude_id: VEH_ID },
                success: function (res) {
                    VehicleForm.plateIsUnique = res.is_unique;
                    if (res.is_unique) {
                        $('#plate_feedback').html(`<small class="text-success"><i class="uil uil-check-circle"></i> ${res.message}</small>`);
                        $('#plate').addClass('is-valid').removeClass('is-invalid');
                    } else {
                        $('#plate_feedback').html(`<small class="text-danger"><i class="uil uil-times-circle"></i> ${res.message}</small>`);
                        $('#plate').addClass('is-invalid').removeClass('is-valid');
                    }
                }
            });
        }, 500);
    }

    $('#plate').on('input', checkPlate);
    $('#province').on('change', checkPlate);
});
</script>

{{-- ============================================ --}}
{{-- Thai Buddhist Date Input (reuse from create)  --}}
{{-- ============================================ --}}
<script>
$(document).ready(function () {

    function isValidDate(d, m, y) {
        const dt = new Date(y, m - 1, d);
        return dt.getFullYear() === y && (dt.getMonth() + 1) === m && dt.getDate() === d;
    }

    // Init: mark pre-filled dates as valid
    $('.date-th-input').each(function () {
        if ($(this).val().length === 10) {
            $(this).addClass('is-valid');
            const hid = $(this).data('hidden-id');
            // hidden already has value from blade — no need to recalculate
        }
    });

    $('.date-th-input').on('input', function () {
        let raw = $(this).val().replace(/[^0-9]/g, '');
        let fmt = '';
        if (raw.length > 2) { fmt = raw.substring(0,2)+'/'; raw.length > 4 ? fmt += raw.substring(2,4)+'/'+raw.substring(4,8) : fmt += raw.substring(2); }
        else { fmt = raw; }
        $(this).val(fmt);

        const feedback  = $(this).closest('.col-12,.col-md-6').find('.date-feedback');
        const hiddenId  = $(this).data('hidden-id');
        const hidden    = $('#'+hiddenId);

        if (!fmt) { hidden.val(''); feedback.html(''); $(this).removeClass('is-valid is-invalid'); return; }

        if (fmt.length === 10) {
            const [dd, mm, yyyy] = fmt.split('/').map(Number);
            if (yyyy < 2400 || yyyy > 2700) {
                hidden.val(''); feedback.html('<span class="text-danger"><i class="uil uil-times-circle"></i> ใส่ปี พ.ศ. ให้ถูกต้อง</span>');
                $(this).addClass('is-invalid').removeClass('is-valid'); return;
            }
            const ce = yyyy - 543;
            if (!isValidDate(dd, mm, ce)) {
                hidden.val(''); feedback.html('<span class="text-danger"><i class="uil uil-times-circle"></i> วันที่ไม่ถูกต้อง</span>');
                $(this).addClass('is-invalid').removeClass('is-valid'); return;
            }
            hidden.val(`${ce}-${String(mm).padStart(2,'0')}-${String(dd).padStart(2,'0')}`);
            feedback.html('<span class="text-success"><i class="uil uil-check-circle"></i> ถูกต้อง</span>');
            $(this).addClass('is-valid').removeClass('is-invalid');
        } else {
            hidden.val(''); feedback.html(''); $(this).removeClass('is-valid is-invalid');
        }
    });
});
</script>

{{-- ============================================ --}}
{{-- Image preview + Document upload validation    --}}
{{-- ============================================ --}}
<script>
$(document).ready(function () {

    // Image preview
    $('#vehicle_image').on('change', function () {
        const file = this.files[0];
        if (!file) { $('#vehicle_preview').hide(); return; }
        if (file.size > 5*1024*1024) { alert('ขนาดไฟล์รูปภาพต้องไม่เกิน 5 MB'); $(this).val(''); return; }
        const r = new FileReader();
        r.onload = e => $('#vehicle_preview').attr('src', e.target.result).show();
        r.readAsDataURL(file);
    });

    // Document upload
    $('#vehicle_document').on('change', function () {
        const file = this.files[0];
        if (!file) { $('#document_info').hide(); $('#doc_name_box').hide(); return; }
        const ext = file.name.split('.').pop().toLowerCase();
        if (!['pdf','docx'].includes(ext)) { alert('รองรับเฉพาะ PDF หรือ DOCX'); $(this).val(''); return; }
        if (file.size > 10*1024*1024) { alert('ขนาดไฟล์เกิน 10 MB'); $(this).val(''); return; }
        $('#document_filename').text(file.name);
        $('#document_filesize').text('('+(file.size/1024/1024).toFixed(2)+' MB)');
        $('#document_info').show();
        $('#doc_name_box').show();
        if (!$('input[name="doc_name"]').val()) {
            $('input[name="doc_name"]').val('เอกสาร {{ $vehicle->car_plate }} ประจำปี '+(new Date().getFullYear()+543));
        }
    });
});
</script>

{{-- ============================================ --}}
{{-- Preview Modal + Submit                        --}}
{{-- ============================================ --}}
<script>
$(document).ready(function () {

    $('#btnPreview').on('click', function () {
        // Validate required
        const req = [
            {id:'plate', label:'ทะเบียนรถ'},
            {id:'province', label:'จังหวัดทะเบียน'},
            {id:'car_brand', label:'ยี่ห้อรถ'},
            {id:'car_type', label:'ประเภทรถ'},
        ];
        let missing = req.filter(f => !$('#'+f.id).val()).map(f => f.label);
        if (!$('input[name="car_model"]').val().trim()) missing.push('รุ่นรถ');
        if (missing.length) { alert('กรุณากรอกข้อมูล:\n- '+missing.join('\n- ')); return; }
        if (VehicleForm.plateIsUnique === false) { alert('ทะเบียนรถซ้ำ กรุณาตรวจสอบ'); return; }
        if ($('.date-th-input.is-invalid').length) { alert('มีวันที่กรอกไม่ถูกต้อง'); return; }
        if (VehicleForm.selectedSupply.isFull) { alert('Supply เต็มโควต้าแล้ว'); return; }

        const plate    = $('#plate').val().trim().replace(/\s/g,'');
        const fullPlate= plate+' '+$('#province').val();

        populatePreviewModal({
            company              : '{{ $vehicle->company_name }}',
            supply               : $('#supply_id option:selected').text(),
            car_plate            : fullPlate,
            car_brand            : $('#car_brand').val(),
            car_type_name        : $('#car_type option:selected').text(),
            car_model            : $('input[name="car_model"]').val() || '-',
            car_number_record    : $('input[name="car_number_record"]').val() || '-',
            car_age              : $('input[name="car_age"]').val() || '-',
            car_mileage          : $('input[name="car_mileage"]').val() || '-',
            car_trailer_plate    : $('input[name="car_trailer_plate"]').val() || '-',
            car_fuel_type        : $('input[name="car_fuel_type"]').val() || '-',
            car_weight           : $('input[name="car_weight"]').val() || '-',
            car_total_weight     : $('input[name="car_total_weight"]').val() || '-',
            car_product          : $('input[name="car_product"]').val() || '-',
            car_insure           : $('input[name="car_insure"]').val() || '-',
            car_tax              : $('#real_car_tax').val(),
            car_register_date    : $('#real_car_register_date').val(),
            car_insurance_expire : $('#real_car_insurance_expire').val(),
            status               : $('#status').is(':checked') ? 'เปิดใช้งาน' : 'ปิดใช้งาน',
            doc_name             : $('input[name="doc_name"]').val() || '-',
        },
        $('#vehicle_preview').attr('src') || '{{ $vehicle->car_image ? asset($vehicle->car_image) : "" }}',
        $('#vehicle_document')[0].files[0]?.name || null
        );

        $('#previewModal').modal('show');
    });

    $('#btnConfirmSubmit').on('click', function () {
        $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> กำลังบันทึก...');
        $('#vehicleEditForm').submit();
    });
});

// Reuse helpers from create
function formatDateBE(d) {
    if (!d) return '-';
    const p = d.split('-');
    if (p.length !== 3) return '-';
    return p[2]+'/'+p[1]+'/'+(parseInt(p[0])+543);
}

function populatePreviewModal(data, imageSrc, docName) {
    $('#prev_company').text(data.company);
    $('#prev_supply').text(data.supply);
    $('#prev_car_plate').text(data.car_plate);
    $('#prev_car_brand').text(data.car_brand);
    $('#prev_car_type').text(data.car_type_name);
    $('#prev_car_model').text(data.car_model);
    $('#prev_car_number_record').text(data.car_number_record);
    $('#prev_car_age').text(data.car_age);
    $('#prev_car_mileage').text(data.car_mileage);
    $('#prev_car_trailer_plate').text(data.car_trailer_plate);
    $('#prev_car_fuel_type').text(data.car_fuel_type);
    $('#prev_car_weight').text(data.car_weight);
    $('#prev_car_total_weight').text(data.car_total_weight);
    $('#prev_car_product').text(data.car_product);
    $('#prev_car_insure').text(data.car_insure);
    $('#prev_car_tax').text(formatDateBE(data.car_tax));
    $('#prev_car_register_date').text(formatDateBE(data.car_register_date));
    $('#prev_car_insurance_expire').text(formatDateBE(data.car_insurance_expire));
    $('#prev_status').text(data.status);
    $('#prev_image').attr('src','');

    const hasImg = imageSrc && imageSrc !== '';
    const hasDoc = !!docName;

    if (hasImg) { $('#prev_image').attr('src', imageSrc); $('#prev_image_box').show(); }
    else { $('#prev_image_box').hide(); }

    if (hasDoc) { $('#prev_doc_filename').text(docName); $('#prev_doc_name').text(data.doc_name); $('#prev_doc_box').show(); }
    else { $('#prev_doc_box').hide(); }

    if (hasImg || hasDoc) {
        $('#prev_left_col').removeClass('d-none col-md-12').addClass('col-md-5');
        $('#prev_right_col').removeClass('col-md-12').addClass('col-md-7');
    } else {
        $('#prev_left_col').addClass('d-none');
        $('#prev_right_col').removeClass('col-md-7').addClass('col-md-12');
    }
}
</script>