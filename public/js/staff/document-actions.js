/**
 * document-actions.js
 * Handles AJAX upload and delete for vehicle documents (Part 5ก)
 * Place: public/js/staff/document-actions.js
 * Include in show.blade.php: <script src="{{ asset('js/staff/document-actions.js') }}"></script>
 */

$(function () {

    // ----------------------------------------------------------------
    // CSRF token setup for all AJAX requests
    // ----------------------------------------------------------------
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    // ----------------------------------------------------------------
    // State: track which doc is being acted on
    // ----------------------------------------------------------------
    let activeDocId  = null;
    let activeVehId  = null;

    // ----------------------------------------------------------------
    // UPLOAD — open modal
    // ----------------------------------------------------------------
    $(document).on('click', '.btn-doc-upload', function () {
        activeDocId = $(this).data('doc-id');
        activeVehId = $(this).data('veh-id');
        const docName = $(this).data('doc-name');

        // Reset modal state
        $('#uploadDocName').text(docName);
        $('#uploadFileInput').val('');
        $('#uploadPreviewWrap').addClass('d-none');
        $('#uploadPreviewImg').attr('src', '');
        $('#uploadPreviewImg').addClass('d-none');
        $('#uploadPreviewPdf').addClass('d-none');
        $('#uploadProgressWrap').addClass('d-none');
        $('#uploadProgressBar').css('width', '0%');
        $('#btnConfirmUpload').prop('disabled', true);

        $('#modalDocUpload').modal('show');
    });

    // ----------------------------------------------------------------
    // UPLOAD — file selected: show preview
    // ----------------------------------------------------------------
    $('#uploadFileInput').on('change', function () {
        const file = this.files[0];
        if (!file) {
            $('#btnConfirmUpload').prop('disabled', true);
            return;
        }

        $('#uploadPreviewWrap').removeClass('d-none');

        if (file.type.startsWith('image/')) {
            // Image preview
            const reader = new FileReader();
            reader.onload = e => {
                $('#uploadPreviewImg').attr('src', e.target.result).removeClass('d-none');
                $('#uploadPreviewPdf').addClass('d-none');
            };
            reader.readAsDataURL(file);
        } else {
            // PDF preview (filename only)
            $('#uploadPreviewImg').addClass('d-none');
            $('#uploadPreviewPdfName').text(file.name);
            $('#uploadPreviewPdf').removeClass('d-none');
        }

        $('#btnConfirmUpload').prop('disabled', false);
    });

    // ----------------------------------------------------------------
    // UPLOAD — confirm: submit via AJAX FormData
    // ----------------------------------------------------------------
    $('#btnConfirmUpload').on('click', function () {
        const file = $('#uploadFileInput')[0].files[0];
        if (!file || !activeDocId || !activeVehId) return;

        const formData = new FormData();
        formData.append('document_file', file);
        formData.append('_method', 'POST');

        // Build URL: /staff/vehicles/{veh_id}/documents/{doc_id}/upload
        const url = `/staff/vehicles/${activeVehId}/documents/${activeDocId}/upload`;

        // Show progress
        $('#uploadProgressWrap').removeClass('d-none');
        $('#btnConfirmUpload').prop('disabled', true);

        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            xhr: function () {
                const xhr = new XMLHttpRequest();
                xhr.upload.addEventListener('progress', function (e) {
                    if (e.lengthComputable) {
                        const pct = Math.round((e.loaded / e.total) * 100);
                        $('#uploadProgressBar').css('width', pct + '%');
                        $('#uploadProgressText').text(`กำลังอัปโหลด... ${pct}%`);
                    }
                });
                return xhr;
            },
            success: function (res) {
                if (res.success) {
                    $('#modalDocUpload').modal('hide');
                    _showToast('success', res.message);
                    _updateCardAfterUpload(activeDocId, activeVehId, res);
                } else {
                    _showToast('danger', res.message || 'เกิดข้อผิดพลาด');
                    $('#btnConfirmUpload').prop('disabled', false);
                }
            },
            error: function (xhr) {
                const msg = xhr.responseJSON?.message
                    || xhr.responseJSON?.errors?.document_file?.[0]
                    || 'อัปโหลดไม่สำเร็จ';
                _showToast('danger', msg);
                $('#uploadProgressWrap').addClass('d-none');
                $('#btnConfirmUpload').prop('disabled', false);
            }
        });
    });

    // ----------------------------------------------------------------
    // DELETE — confirm then AJAX
    // ----------------------------------------------------------------
    $(document).on('click', '.btn-doc-delete', function () {
        const docId   = $(this).data('doc-id');
        const vehId   = $(this).data('veh-id');
        const docName = $(this).data('doc-name');

        // Simple confirm dialog (can swap with Swal2 if available)
        if (!confirm(`ยืนยันลบไฟล์เอกสาร "${docName}" ?\nการกระทำนี้ไม่สามารถย้อนกลับได้`)) return;

        const url = `/staff/vehicles/${vehId}/documents/${docId}`;

        $.ajax({
            url: url,
            method: 'POST', // Laravel needs _method for DELETE via AJAX
            data: { _method: 'DELETE' },
            success: function (res) {
                if (res.success) {
                    _showToast('success', res.message);
                    _updateCardAfterDelete(docId, vehId);
                } else {
                    _showToast('danger', res.message || 'ลบไม่สำเร็จ');
                }
            },
            error: function (xhr) {
                const msg = xhr.responseJSON?.message || 'เกิดข้อผิดพลาด';
                _showToast('danger', msg);
            }
        });
    });

    // ----------------------------------------------------------------
    // Helper: update card UI after upload (no page reload)
    // ----------------------------------------------------------------
    function _updateCardAfterUpload(docId, vehId, res) {
        const $card = $(`#doc-card-${docId}`);

        // Update status badge
        $(`.doc-status-${docId}`)
            .removeClass('bg-warning-subtle text-warning')
            .addClass('bg-success-subtle text-success')
            .html('<i class="uil uil-check-circle"></i> มีไฟล์แนบ');

        // Rebuild download button (insert before upload btn if not exists)
        const downloadUrl = `/staff/vehicles/${vehId}/documents/${docId}/download`;
        if ($card.find('.btn-doc-download-link').length === 0) {
            $card.find('.btn-doc-upload').before(
                `<a href="${downloadUrl}" class="btn btn-sm btn-outline-primary btn-doc-download-link" title="ดาวน์โหลด">
                    <i class="uil uil-download-alt"></i> ดาวน์โหลด
                </a>`
            );
        }

        // Show delete button if not exists
        if ($card.find('.btn-doc-delete').length === 0) {
            $card.find('.btn-doc-upload').before(
                `<button type="button"
                    class="btn btn-sm btn-outline-danger btn-doc-delete"
                    data-doc-id="${docId}"
                    data-veh-id="${vehId}"
                    data-doc-name="${$card.find('.fw-bold').text().trim()}"
                    title="ลบไฟล์">
                    <i class="uil uil-trash-alt"></i> ลบไฟล์
                </button>`
            );
        }

        // Update upload button label
        $card.find('.btn-doc-upload').html('<i class="uil uil-upload"></i> เปลี่ยนไฟล์');
    }

    // ----------------------------------------------------------------
    // Helper: update card UI after delete (no page reload)
    // ----------------------------------------------------------------
    function _updateCardAfterDelete(docId, vehId) {
        const $card = $(`#doc-card-${docId}`);

        // Update status badge
        $(`.doc-status-${docId}`)
            .removeClass('bg-success-subtle text-success')
            .addClass('bg-warning-subtle text-warning')
            .html('<i class="uil uil-exclamation-circle"></i> ยังไม่มีไฟล์');

        // Remove download + delete buttons
        $card.find('.btn-doc-download-link').remove();
        $card.find('.btn-doc-delete').remove();

        // Reset upload button label
        $card.find('.btn-doc-upload').html('<i class="uil uil-upload"></i> อัปโหลด');
    }

    // ----------------------------------------------------------------
    // Helper: Bootstrap toast notification
    // ----------------------------------------------------------------
    function _showToast(type, message) {
        // type: 'success' | 'danger' | 'warning'
        const id      = 'toast-' + Date.now();
        const bgClass = type === 'success' ? 'bg-success'
                      : type === 'danger'  ? 'bg-danger'
                      : 'bg-warning';
        const icon    = type === 'success' ? 'uil-check-circle'
                      : type === 'danger'  ? 'uil-times-circle'
                      : 'uil-exclamation-circle';

        const toastHtml = `
            <div id="${id}" class="toast align-items-center text-white ${bgClass} border-0 mb-2"
                 role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="uil ${icon} me-1"></i> ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto"
                            data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>`;

        // Ensure toast container exists
        if ($('#toast-container').length === 0) {
            $('body').append(
                '<div id="toast-container" class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index:1100"></div>'
            );
        }

        $('#toast-container').append(toastHtml);
        const toastEl = document.getElementById(id);
        const toast   = new bootstrap.Toast(toastEl, { delay: 4000 });
        toast.show();

        // Auto-remove DOM after hidden
        toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
    }

});