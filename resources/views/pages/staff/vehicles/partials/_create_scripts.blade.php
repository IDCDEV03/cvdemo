{{-- ============================================ --}}
{{-- Select2 Assets (Bootstrap 5 Theme)            --}}
{{-- ============================================ --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
    rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

{{-- ============================================ --}}
{{-- Select2 Override CSS (match Hexadash style)   --}}
{{-- ============================================ --}}
<style>
    /* Match Bootstrap 5 form-control height */
    .select2-container--bootstrap-5 .select2-selection {
        min-height: 41px !important;
        padding: 0.45rem 1rem !important;
        border: 1px solid #e3e6ef !important;
        border-radius: 6px !important;
        font-size: 14px !important;
        line-height: 1.5 !important;
        color: #404659 !important;
        background-color: #fff !important;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    /* Focus state - match Hexadash primary color */
    .select2-container--bootstrap-5 .select2-selection:focus,
    .select2-container--bootstrap-5.select2-container--focus .select2-selection,
    .select2-container--bootstrap-5.select2-container--open .select2-selection {
        border-color: #5840ff !important;
        box-shadow: 0 0 0 0.2rem rgba(88, 64, 255, 0.15) !important;
        outline: 0 !important;
    }

    /* Single selection text alignment */
    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
        line-height: 1.5 !important;
        padding-left: 0 !important;
        padding-right: 25px !important;
        color: #404659 !important;
    }

    /* Placeholder color */
    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__placeholder {
        color: #adb4d2 !important;
    }

    /* Arrow icon */
    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__arrow {
        height: 100% !important;
        right: 8px !important;
    }

    /* Dropdown panel */
    .select2-container--bootstrap-5 .select2-dropdown {
        border: 1px solid #e3e6ef !important;
        border-radius: 6px !important;
        box-shadow: 0 5px 20px rgba(146, 153, 184, 0.15) !important;
    }

    /* Highlighted option (hover) */
    .select2-container--bootstrap-5 .select2-results__option--highlighted[aria-selected] {
        background-color: #5840ff !important;
        color: #fff !important;
    }

    /* Selected option */
    .select2-container--bootstrap-5 .select2-results__option[aria-selected=true] {
        background-color: rgba(88, 64, 255, 0.1) !important;
        color: #5840ff !important;
    }

    /* Search box inside dropdown */
    .select2-container--bootstrap-5 .select2-search--dropdown .select2-search__field {
        border: 1px solid #e3e6ef !important;
        border-radius: 4px !important;
        padding: 6px 10px !important;
        font-size: 14px !important;
    }

    .select2-container--bootstrap-5 .select2-search--dropdown .select2-search__field:focus {
        border-color: #5840ff !important;
        outline: 0 !important;
    }

    /* Disabled state */
    .select2-container--bootstrap-5.select2-container--disabled .select2-selection {
        background-color: #f8f9fb !important;
        cursor: not-allowed !important;
    }

    /* Validation states (is-valid / is-invalid) */
    .form-control.is-valid+.select2-container--bootstrap-5 .select2-selection {
        border-color: #20c997 !important;
    }

    .form-control.is-invalid+.select2-container--bootstrap-5 .select2-selection {
        border-color: #ff4d4f !important;
    }

    /* Spacing inside option list */
    .select2-container--bootstrap-5 .select2-results__option {
        padding: 8px 12px !important;
        font-size: 14px !important;
    }

    /* No results message */
    .select2-container--bootstrap-5 .select2-results__option--disabled {
        color: #adb4d2 !important;
    }
</style>

{{-- ============================================ --}}
{{-- AJAX Setup + CSRF                              --}}
{{-- ============================================ --}}
<script>
    // Setup CSRF for all AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Global state - track current step + supply info
    const VehicleForm = {
        currentStep: 1,
        selectedCompany: {
            id: null,
            name: null
        },
        selectedSupply: {
            id: null,
            name: null,
            limit: 0,
            current: 0,
            isFull: false
        },
        plateIsUnique: null, // null=not checked, true=unique, false=duplicate
    };
</script>
{{-- ============================================ --}}
{{-- Select2 Initialization                        --}}
{{-- ============================================ --}}
<script>
    $(document).ready(function() {

        // Common Select2 config
        const select2Config = {
            theme: 'bootstrap-5',
            width: '100%',
            language: {
                noResults: function() {
                    return 'ไม่พบข้อมูล';
                },
                searching: function() {
                    return 'กำลังค้นหา...';
                },
                inputTooShort: function(args) {
                    return 'พิมพ์อย่างน้อย ' + args.minimum + ' ตัวอักษร';
                }
            }
        };

        // Step 1: Company dropdown
        $('#company_id').select2({
            ...select2Config,
            placeholder: '-- กรุณาเลือกบริษัท --',
        });

        // Step 2: Supply dropdown
        $('#supply_id').select2({
            ...select2Config,
            placeholder: '-- กรุณาเลือก Supply --',
        });

        // Step 3: Province dropdown
        $('#province').select2({
            ...select2Config,
            placeholder: '-- กรุณาเลือกจังหวัด --',
        });

        // Step 3: Brand dropdown
        $('#car_brand').select2({
            ...select2Config,
            placeholder: '-- เลือกยี่ห้อรถ --',
        });

        // Step 3: Vehicle type dropdown
        $('#car_type').select2({
            ...select2Config,
            placeholder: '-- เลือกประเภทรถ --',
        });

    });
</script>

{{-- ============================================ --}}
{{-- Step Navigation                                --}}
{{-- ============================================ --}}
<script>
    $(document).ready(function() {

        // Helper: switch step UI
        function goToStep(stepNum) {
            // Hide all step contents
            $('.step-content').removeClass('active');
            // Show target step
            $(`.step-content[data-step-content="${stepNum}"]`).addClass('active');

            // Update step indicator
            $('.step-wizard .step').each(function() {
                const thisStep = parseInt($(this).data('step'));
                $(this).removeClass('active completed');
                if (thisStep < stepNum) {
                    $(this).addClass('completed');
                } else if (thisStep === stepNum) {
                    $(this).addClass('active');
                }
            });

            VehicleForm.currentStep = stepNum;

            // Scroll to top of card
            $('html, body').animate({
                scrollTop: $('.step-wizard').offset().top - 100
            }, 300);
        }

        // Click "ถัดไป" button
        $('.btn-next').on('click', function() {
            const nextStep = parseInt($(this).data('next-step'));

            // Validate current step before moving forward
            if (!validateStep(VehicleForm.currentStep)) {
                return;
            }

            goToStep(nextStep);
        });

        // Click "ย้อนกลับ" button
        $('.btn-prev').on('click', function() {
            const prevStep = parseInt($(this).data('prev-step'));
            goToStep(prevStep);
        });

        // Click step indicator (allow only going back, not forward)
        $('.step-wizard .step').on('click', function() {
            const targetStep = parseInt($(this).data('step'));
            if (targetStep < VehicleForm.currentStep) {
                goToStep(targetStep);
            }
        });

        // Validate current step before next
        function validateStep(stepNum) {
            if (stepNum === 1) {
                if (!$('#company_id').val()) {
                    alert('กรุณาเลือกบริษัท');
                    return false;
                }
                return true;
            }
            if (stepNum === 2) {
                if (!$('#supply_id').val()) {
                    alert('กรุณาเลือก Supply');
                    return false;
                }
                if (VehicleForm.selectedSupply.isFull) {
                    alert('Supply นี้ลงทะเบียนรถเต็มโควต้าแล้ว ไม่สามารถลงทะเบียนเพิ่มได้');
                    return false;
                }
                return true;
            }
            return true;
        }
    });
</script>


{{-- ============================================ --}}
{{-- Step 1 -> Step 2: Cascading Dropdown          --}}
{{-- (Updated: use option text instead of data-name) --}}
{{-- ============================================ --}}
<script>
    $(document).ready(function() {

        $('#company_id').on('change', function() {
            const companyId = $(this).val();
            // ⭐ Use option text instead of data-name (safer with special chars)
            const companyName = $(this).find('option:selected').text().trim();

            // Guard
            if (!companyId) {
                return;
            }

            VehicleForm.selectedCompany.id = companyId;
            VehicleForm.selectedCompany.name = companyName;

            // Update display
            $('#selected_company_name').text(companyName);
            $('#step3_company_name').text(companyName);

            // Reset supply
            $('#supply_id').empty()
                .append('<option value="" selected disabled>-- กำลังโหลด... --</option>')
                .trigger('change');

            $('#supply_limit_box').hide();
            $('#btn_to_step3').prop('disabled', true);
            VehicleForm.selectedSupply = {
                id: null,
                name: null,
                limit: 0,
                current: 0,
                isFull: false
            };

            // AJAX: Load supplies
            $.ajax({
                url: "{{ route('staff.vehicles.ajax.supplies') }}",
                method: 'GET',
                data: {
                    company_id: companyId
                },
                success: function(response) {
                    if (response.success) {
                        $('#supply_id').empty();

                        if (response.data.length === 0) {
                            $('#supply_id').append(
                                '<option value="" selected disabled>-- ไม่มี Supply ในบริษัทนี้ --</option>'
                            );
                        } else {
                            $('#supply_id').append(
                                '<option value="" selected disabled>-- กรุณาเลือก Supply --</option>'
                            );
                            response.data.forEach(function(supply) {
                                // Use new Option() - DOM-safe, no string concat
                                const opt = new Option(
                                    supply.supply_name, // text
                                    supply.sup_id, // value
                                    false, false
                                );
                                opt.setAttribute('data-limit', supply
                                    .vehicle_limit || 0);
                                $('#supply_id').append(opt);
                            });
                        }

                        $('#supply_id').trigger('change');
                    } else {
                        $('#supply_id').empty()
                            .append(
                                '<option value="" selected disabled>-- เกิดข้อผิดพลาด --</option>'
                            )
                            .trigger('change');
                    }
                },
                error: function(xhr) {
                    $('#supply_id').empty()
                        .append(
                            '<option value="" selected disabled>-- เกิดข้อผิดพลาด --</option>'
                        )
                        .trigger('change');
                    console.error('Load supplies error:', xhr);
                }
            });
        });

    });
</script>
{{-- ============================================ --}}
{{-- Step 2: Supply Limit Check                     --}}
{{-- (Updated: add null guard)                      --}}
{{-- ============================================ --}}
<script>
    $(document).ready(function() {

        $('#supply_id').on('change', function() {
            const supplyId = $(this).val();
            const supplyName = $(this).find('option:selected').text().trim();

            // ⚠️ Guard: skip if no value (placeholder/disabled option)
            if (!supplyId || supplyId === 'null' || supplyId === '') {
                $('#supply_limit_box').hide();
                $('#btn_to_step3').prop('disabled', true);
                VehicleForm.selectedSupply = {
                    id: null,
                    name: null,
                    limit: 0,
                    current: 0,
                    isFull: false
                };
                return;
            }

            VehicleForm.selectedSupply.id = supplyId;
            VehicleForm.selectedSupply.name = supplyName;

            // Update display in step 3
            $('#step3_supply_name').text(supplyName || '-');

            // Disable next button until limit check completes
            $('#btn_to_step3').prop('disabled', true);

            // AJAX: Get supply info (limit + current count)
            $.ajax({
                url: `{{ url('staff/vehicles/ajax/supply-info') }}/${encodeURIComponent(supplyId)}`,
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        const data = response.data;

                        VehicleForm.selectedSupply.limit = data.vehicle_limit;
                        VehicleForm.selectedSupply.current = data.current_count;
                        VehicleForm.selectedSupply.isFull = data.is_full;

                        $('#limit_supply_name').text(data.supply_name);
                        $('#limit_current').text(data.current_count);
                        $('#limit_max').text(data.vehicle_limit > 0 ? data.vehicle_limit :
                            'ไม่จำกัด');

                        if (data.is_full) {
                            $('#supply_limit_box').addClass('is-full').show();
                            $('#limit_status_text').html(
                                '<span class="text-danger fw-bold">' +
                                '<i class="uil uil-times-circle"></i> เต็มโควต้าแล้ว' +
                                '</span>'
                            );
                            $('#btn_to_step3').prop('disabled', true);
                        } else {
                            $('#supply_limit_box').removeClass('is-full').show();
                            const remainingText = data.remaining !== null ?
                                `เหลือ ${data.remaining} คัน` :
                                'ลงทะเบียนได้ (ไม่จำกัด)';
                            $('#limit_status_text').html(
                                '<span class="text-success">' +
                                '<i class="uil uil-check-circle"></i> ' +
                                remainingText +
                                '</span>'
                            );
                            $('#btn_to_step3').prop('disabled', false);
                        }
                    } else {
                        // Supply not found - silent (don't alert user)
                        console.warn('Supply not found:', supplyId);
                        $('#supply_limit_box').hide();
                        $('#btn_to_step3').prop('disabled', true);
                    }
                },
                error: function(xhr) {
                    // Only alert on actual error (not 404)
                    if (xhr.status !== 404) {
                        alert('เกิดข้อผิดพลาดในการตรวจสอบโควต้า (HTTP ' + xhr.status + ')');
                    }
                    console.error('Supply info error:', xhr);
                    $('#supply_limit_box').hide();
                    $('#btn_to_step3').prop('disabled', true);
                }
            });
        });

    });
</script>

{{-- ============================================ --}}
{{-- Step 3: Plate Uniqueness Check (Debounced)    --}}
{{-- (Updated for Select2)                          --}}
{{-- ============================================ --}}
<script>
    $(document).ready(function() {
        let plateCheckTimeout = null;

        // Combined check function
        function triggerPlateCheck() {
            clearTimeout(plateCheckTimeout);

            const plate = $('#plate').val().trim();
            const province = $('#province').val();
            const supplyId = VehicleForm.selectedSupply.id;

            if (!plate || !province || !supplyId) {
                $('#plate_feedback').html('');
                $('#plate').removeClass('is-valid is-invalid');
                VehicleForm.plateIsUnique = null;
                return;
            }

            $('#plate_feedback').html(
                '<small class="text-muted"><i class="uil uil-spinner-alt"></i> กำลังตรวจสอบ...</small>'
            );

            plateCheckTimeout = setTimeout(function() {
                $.ajax({
                    url: "{{ route('staff.vehicles.ajax.check_plate') }}",
                    method: 'GET',
                    data: {
                        plate: plate,
                        province: province,
                        supply_id: supplyId
                    },
                    success: function(response) {
                        if (response.success) {
                            VehicleForm.plateIsUnique = response.is_unique;
                            if (response.is_unique) {
                                $('#plate_feedback').html(
                                    '<small class="text-success"><i class="uil uil-check-circle"></i> ' +
                                    response.message + '</small>'
                                );
                                $('#plate').addClass('is-valid').removeClass('is-invalid');
                            } else {
                                $('#plate_feedback').html(
                                    '<small class="text-danger"><i class="uil uil-times-circle"></i> ' +
                                    response.message + '</small>'
                                );
                                $('#plate').addClass('is-invalid').removeClass('is-valid');
                            }
                        }
                    },
                    error: function(xhr) {
                        $('#plate_feedback').html(
                            '<small class="text-danger">ไม่สามารถตรวจสอบได้</small>'
                        );
                        VehicleForm.plateIsUnique = null;
                    }
                });
            }, 500);
        }

        // Bind: text input for plate
        $('#plate').on('input', triggerPlateCheck);

        // Bind: Select2 for province (must use 'change')
        $('#province').on('change', triggerPlateCheck);
    });
</script>


{{-- ============================================ --}}
{{-- Thai Buddhist Date Input Handler              --}}
{{-- (reused from Step 2 - inspection module)      --}}
{{-- ============================================ --}}
<script>
    $(document).ready(function() {

        // Helper: validate if date is real
        function isValidDate(day, month, yearCE) {
            const d = new Date(yearCE, month - 1, day);
            return d.getFullYear() === yearCE &&
                (d.getMonth() + 1) === month &&
                d.getDate() === day;
        }

        $('.date-th-input').on('input', function(e) {
            let input = $(this).val().replace(/[^0-9]/g, '');
            let formattedDate = '';

            // Auto-insert slashes
            if (input.length > 2) {
                formattedDate += input.substring(0, 2) + '/';
                if (input.length > 4) {
                    formattedDate += input.substring(2, 4) + '/';
                    formattedDate += input.substring(4, 8);
                } else {
                    formattedDate += input.substring(2);
                }
            } else {
                formattedDate = input;
            }

            $(this).val(formattedDate);

            // Find paired feedback element + hidden input
            let feedback = $(this).closest('.col-12, .col-md-6').find('.date-feedback');
            let hiddenInputId = $(this).data('hidden-id');
            let hiddenInput = $('#' + hiddenInputId);

            // Empty input = clear (this field is optional)
            if (formattedDate.length === 0) {
                hiddenInput.val('');
                feedback.html('');
                $(this).removeClass('is-valid is-invalid');
                return;
            }

            // Full date entered (10 chars: dd/mm/yyyy)
            if (formattedDate.length === 10) {
                let parts = formattedDate.split('/');
                let day = parseInt(parts[0]);
                let month = parseInt(parts[1]);
                let yearBE = parseInt(parts[2]);

                // Validate Buddhist year range
                if (yearBE < 2400 || yearBE > 2700) {
                    hiddenInput.val('');
                    feedback.html(
                        '<span class="text-danger">' +
                        '<i class="uil uil-times-circle"></i> ใส่ปี พ.ศ. ให้ถูกต้อง (เช่น 2570)' +
                        '</span>'
                    );
                    $(this).addClass('is-invalid').removeClass('is-valid');
                    return;
                }

                // Convert BE -> CE
                let yearCE = yearBE - 543;

                // Validate real date
                if (!isValidDate(day, month, yearCE)) {
                    hiddenInput.val('');
                    feedback.html(
                        '<span class="text-danger">' +
                        '<i class="uil uil-times-circle"></i> วันที่ไม่ถูกต้อง' +
                        '</span>'
                    );
                    $(this).addClass('is-invalid').removeClass('is-valid');
                    return;
                }

                // All checks passed - format for DB (Y-m-d)
                let dayStr = String(day).padStart(2, '0');
                let monthStr = String(month).padStart(2, '0');
                let finalDateForDB = yearCE + '-' + monthStr + '-' + dayStr;

                hiddenInput.val(finalDateForDB);
                feedback.html(
                    '<span class="text-success">' +
                    '<i class="uil uil-check-circle"></i> รูปแบบวันที่ถูกต้อง' +
                    '</span>'
                );
                $(this).addClass('is-valid').removeClass('is-invalid');
            } else {
                // Incomplete input
                hiddenInput.val('');
                feedback.html('');
                $(this).removeClass('is-valid is-invalid');
            }
        });

    });
</script>


{{-- ============================================ --}}
{{-- Image Preview + Size Validation (5 MB)        --}}
{{-- ============================================ --}}
<script>
    $(document).ready(function() {

        const MAX_IMAGE_SIZE = 5 * 1024 * 1024; // 5 MB
        const ALLOWED_IMAGE_TYPES = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];

        $('#vehicle_image').on('change', function(e) {
            const file = e.target.files[0];

            if (!file) {
                $('#vehicle_preview').hide().attr('src', '');
                return;
            }

            // Validate type
            if (!ALLOWED_IMAGE_TYPES.includes(file.type)) {
                alert('กรุณาเลือกไฟล์รูปภาพ (jpg, png, webp)');
                $(this).val('');
                $('#vehicle_preview').hide();
                return;
            }

            // Validate size
            if (file.size > MAX_IMAGE_SIZE) {
                alert('ขนาดไฟล์รูปภาพต้องไม่เกิน 5 MB');
                $(this).val('');
                $('#vehicle_preview').hide();
                return;
            }

            // Show preview
            const reader = new FileReader();
            reader.onload = function(evt) {
                $('#vehicle_preview').attr('src', evt.target.result).show();
            };
            reader.readAsDataURL(file);
        });

    });
</script>


{{-- ============================================ --}}
{{-- Document Upload Validation (PDF/DOCX, 10 MB)  --}}
{{-- ============================================ --}}
<script>
    $(document).ready(function() {

        const MAX_DOC_SIZE = 10 * 1024 * 1024; // 10 MB
        const ALLOWED_DOC_EXT = ['pdf', 'docx'];

        // Format file size for display
        function formatFileSize(bytes) {
            if (bytes < 1024) return bytes + ' B';
            if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
            return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
        }

        $('#vehicle_document').on('change', function(e) {
            const file = e.target.files[0];

            if (!file) {
                $('#document_info').hide();
                $('#doc_name_box').hide();
                return;
            }

            // Get extension
            const ext = file.name.split('.').pop().toLowerCase();

            // Validate type
            if (!ALLOWED_DOC_EXT.includes(ext)) {
                alert('รองรับเฉพาะไฟล์ PDF หรือ DOCX เท่านั้น');
                $(this).val('');
                $('#document_info').hide();
                $('#doc_name_box').hide();
                return;
            }

            // Validate size
            if (file.size > MAX_DOC_SIZE) {
                alert('ขนาดไฟล์เอกสารต้องไม่เกิน 10 MB');
                $(this).val('');
                $('#document_info').hide();
                $('#doc_name_box').hide();
                return;
            }

            // Show file info
            $('#document_filename').text(file.name);
            $('#document_filesize').text('(' + formatFileSize(file.size) + ')');
            $('#document_info').show();

            // Show doc_name input + auto-fill default name
            $('#doc_name_box').show();
            if (!$('input[name="doc_name"]').val()) {
                const today = new Date();
                const yearBE = today.getFullYear() + 543;
                // Get plate + province to build doc name
                const plate = $('#plate').val().trim().replace(/\s/g, '');
                const province = $('#province').val() || '';
                const plateFull = province ? (plate + ' ' + province) : plate;

                // Build doc name: เอกสาร[ทะเบียน]ประจำปี [พ.ศ.]
                const docName = plateFull ?
                    'เอกสาร ' + plateFull + ' ประจำปี ' + yearBE :
                    'เอกสารประจำปี ' + yearBE; // fallback ถ้ายังไม่กรอกทะเบียน

                $('input[name="doc_name"]').val(docName);
            }

            // Auto-update doc_name when plate or province changes (if file already selected)
            $('#plate, #province').on('input change', function() {
                if ($('#vehicle_document')[0].files.length === 0) return; 

                const plate = $('#plate').val().trim().replace(/\s/g, '');
                const province = $('#province').val() || '';
                const plateFull = province ? (plate + ' ' + province) : plate;
                const yearBE = new Date().getFullYear() + 543;

                const docName = plateFull ?
                    'เอกสาร ' + plateFull + ' ประจำปี ' + yearBE :
                    'เอกสารประจำปี ' + yearBE;

                $('input[name="doc_name"]').val(docName);
            });
        });

    });
</script>


{{-- ============================================ --}}
{{-- Preview Button -> Show Modal (Part 3.3)       --}}
{{-- ============================================ --}}
<script>
    $(document).ready(function() {

        $('#btnPreview').on('click', function() {
            // ============================================
            // Validate required fields before preview
            // ============================================
            const requiredFields = [{
                    id: 'plate',
                    label: 'ทะเบียนรถ'
                },
                {
                    id: 'province',
                    label: 'จังหวัดทะเบียน'
                },
                {
                    id: 'car_brand',
                    label: 'ยี่ห้อรถ'
                },
                {
                    id: 'car_type',
                    label: 'ประเภทรถ'
                },
            ];

            let missing = [];
            requiredFields.forEach(function(field) {
                if (!$('#' + field.id).val()) {
                    missing.push(field.label);
                }
            });

            // Check car_model (text input)
            if (!$('input[name="car_model"]').val().trim()) {
                missing.push('รุ่นรถ');
            }

            if (missing.length > 0) {
                alert('กรุณากรอกข้อมูลที่จำเป็น:\n- ' + missing.join('\n- '));
                return;
            }

            // Check plate uniqueness
            if (VehicleForm.plateIsUnique === false) {
                alert('ทะเบียนรถนี้มีอยู่ใน Supply นี้แล้ว กรุณาตรวจสอบอีกครั้ง');
                $('#plate').focus();
                return;
            }
            if (VehicleForm.plateIsUnique === null) {
                alert('กรุณารอระบบตรวจสอบทะเบียนรถสักครู่');
                return;
            }

            // Check date validity (any date in invalid state)
            if ($('.date-th-input.is-invalid').length > 0) {
                alert('มีวันที่กรอกไม่ถูกต้อง กรุณาตรวจสอบ');
                return;
            }

            // Check supply limit one more time
            if (VehicleForm.selectedSupply.isFull) {
                alert('Supply นี้ลงทะเบียนรถเต็มโควต้าแล้ว');
                return;
            }

            // ============================================
            // Build preview data
            // ============================================
            const cleanPlate = $('#plate').val().trim().replace(/\s/g, '');
            const fullPlate = cleanPlate + ' ' + $('#province').val();

            const previewData = {
                company: VehicleForm.selectedCompany.name,
                supply: VehicleForm.selectedSupply.name,
                car_plate: fullPlate,
                car_brand: $('#car_brand').val(),
                car_type_name: $('#car_type option:selected').text(),
                car_model: $('input[name="car_model"]').val() || '-',
                car_number_record: $('input[name="car_number_record"]').val() || '-',
                car_age: $('input[name="car_age"]').val() || '-',
                car_mileage: $('input[name="car_mileage"]').val() || '-',
                car_trailer_plate: $('input[name="car_trailer_plate"]').val() || '-',
                car_fuel_type: $('input[name="car_fuel_type"]').val() || '-',
                car_weight: $('input[name="car_weight"]').val() || '-',
                car_total_weight: $('input[name="car_total_weight"]').val() || '-',
                car_product: $('input[name="car_product"]').val() || '-',
                car_insure: $('input[name="car_insure"]').val() || '-',
                car_tax: $('#real_car_tax').val(),
                car_register_date: $('#real_car_register_date').val(),
                car_insurance_expire: $('#real_car_insurance_expire').val(),
                status: $('#status').is(':checked') ? 'เปิดใช้งาน' : 'ปิดใช้งาน',
                doc_name: $('input[name="doc_name"]').val() || '-',
            };

            // Image preview src
            const imageSrc = $('#vehicle_preview').attr('src');
            const docName = $('#vehicle_document')[0].files[0]?.name || null;

            // Inject into modal
            populatePreviewModal(previewData, imageSrc, docName);

            // Show modal
            $('#previewModal').modal('show');
        });

        // Confirm submit from modal
        $('#btnConfirmSubmit').on('click', function() {
            $(this).prop('disabled', true).html(
                '<span class="spinner-border spinner-border-sm"></span> กำลังบันทึก...'
            );
            $('#vehicleForm').submit();
        });

    });

    // ============================================
    // Helper: Format date BE for display (Y-m-d -> dd/mm/yyyy พ.ศ.)
    // ============================================
    function formatDateBE(dbDate) {
        if (!dbDate) return '-';
        const parts = dbDate.split('-');
        if (parts.length !== 3) return '-';
        const yearBE = parseInt(parts[0]) + 543;
        return parts[2] + '/' + parts[1] + '/' + yearBE;
    }

    // ============================================
    // Helper: Format date BE for display (Y-m-d -> dd/mm/yyyy พ.ศ.)
    // ============================================
    function formatDateBE(dbDate) {
        if (!dbDate) return '-';
        const parts = dbDate.split('-');
        if (parts.length !== 3) return '-';
        const yearBE = parseInt(parts[0]) + 543;
        return parts[2] + '/' + parts[1] + '/' + yearBE;
    }

    // ============================================
    // Populate Preview Modal
    // (Updated: clear image before set)
    // ============================================
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

        // Reset image first to avoid showing old image
        $('#prev_image').attr('src', '');

        // ============================================
        // Determine if left column has any content
        // ============================================
        const hasImage = imageSrc && imageSrc !== '';
        const hasDoc = !!docName;
        const hasLeftContent = hasImage || hasDoc;

        // Image preview
        if (hasImage) {
            $('#prev_image').attr('src', imageSrc);
            $('#prev_image_box').show();
        } else {
            $('#prev_image_box').hide();
        }

        // Document
        if (hasDoc) {
            $('#prev_doc_filename').text(docName);
            $('#prev_doc_name').text(data.doc_name);
            $('#prev_doc_box').show();
        } else {
            $('#prev_doc_box').hide();
        }

        // ============================================
        // Adjust column widths based on left content
        // ============================================
        if (hasLeftContent) {
            // Has image or doc → split columns
            $('#prev_left_col')
                .removeClass('d-none')
                .removeClass('col-md-12')
                .addClass('col-md-5');
            $('#prev_right_col')
                .removeClass('col-md-12')
                .addClass('col-md-7');
        } else {
            // No image and no doc → hide left, expand right to full width
            $('#prev_left_col').addClass('d-none');
            $('#prev_right_col')
                .removeClass('col-md-7')
                .addClass('col-md-12');
        }
    }
</script>
