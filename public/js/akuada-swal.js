/**
 * AkuAda SweetAlert2 Standardized Notifications & Compact Confirmations
 * - Unified compact modal alerts and toasts (ukuran kecil, elegan & modern)
 * - Automatic interceptor for all native confirms & alerts across all pages
 */

(function(window) {
    'use strict';

    // Inject compact SweetAlert2 custom CSS styling
    const style = document.createElement('style');
    style.id = 'akuada-swal-compact-css';
    style.textContent = `
        /* Compact Modal Popup */
        .swal2-popup.swal2-compact-popup {
            width: 380px !important;
            max-width: 92vw !important;
            padding: 22px 24px !important;
            border-radius: 18px !important;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.12), 0 8px 10px -6px rgba(0, 0, 0, 0.08) !important;
            border: 1px solid rgba(226, 232, 240, 0.9) !important;
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
        }

        /* Scaled Modal Icons: font-size 10px ensures all em-calculated child SVG/lines/masks scale down proportionally to 50px */
        .swal2-compact-popup .swal2-icon {
            font-size: 10px !important;
            margin: 6px auto 14px auto !important;
        }
        .swal2-compact-popup .swal2-icon.swal2-success {
            border-color: #10b981 !important;
            color: #10b981 !important;
        }
        .swal2-compact-popup .swal2-icon.swal2-success [class^='swal2-success-line'] {
            background-color: #10b981 !important;
        }
        .swal2-compact-popup .swal2-icon.swal2-success .swal2-success-ring {
            border-color: rgba(16, 185, 129, 0.3) !important;
        }
        .swal2-compact-popup .swal2-icon.swal2-error {
            border-color: #ef4444 !important;
            color: #ef4444 !important;
        }
        .swal2-compact-popup .swal2-icon.swal2-error [class^='swal2-x-mark-line'] {
            background-color: #ef4444 !important;
        }
        .swal2-compact-popup .swal2-icon.swal2-warning {
            border-color: #f59e0b !important;
            color: #f59e0b !important;
        }
        .swal2-compact-popup .swal2-icon.swal2-info {
            border-color: #3b82f6 !important;
            color: #3b82f6 !important;
        }
        .swal2-compact-popup .swal2-icon.swal2-question {
            border-color: #8b5cf6 !important;
            color: #8b5cf6 !important;
        }

        /* Typography */
        .swal2-compact-popup .swal2-title {
            font-size: 16px !important;
            font-weight: 700 !important;
            color: #0f172a !important;
            padding: 0 !important;
            margin: 0 0 8px 0 !important;
            line-height: 1.35 !important;
        }
        .swal2-compact-popup .swal2-html-container {
            font-size: 13px !important;
            line-height: 1.55 !important;
            color: #475569 !important;
            margin: 0 0 18px 0 !important;
            padding: 0 !important;
        }

        /* Compact Buttons */
        .swal2-compact-popup .swal2-actions {
            margin: 0 !important;
            gap: 10px !important;
            width: 100% !important;
            justify-content: center !important;
        }
        .swal2-compact-popup .swal2-confirm {
            background-color: #7c3aed !important;
            color: #ffffff !important;
            font-size: 12.5px !important;
            font-weight: 600 !important;
            padding: 9px 22px !important;
            border-radius: 10px !important;
            box-shadow: 0 4px 6px -1px rgba(124, 58, 237, 0.25) !important;
            transition: all 0.15s ease !important;
            margin: 0 !important;
        }
        .swal2-compact-popup .swal2-confirm:hover {
            background-color: #6d28d9 !important;
        }
        .swal2-compact-popup .swal2-cancel {
            background-color: #f1f5f9 !important;
            color: #475569 !important;
            border: 1px solid #cbd5e1 !important;
            font-size: 12.5px !important;
            font-weight: 600 !important;
            padding: 9px 18px !important;
            border-radius: 10px !important;
            transition: all 0.15s ease !important;
            margin: 0 !important;
        }
        .swal2-compact-popup .swal2-cancel:hover {
            background-color: #e2e8f0 !important;
            color: #1e293b !important;
        }

        /* Compact Toast - maintain SweetAlert2 native icon math */
        .swal2-popup.swal2-toast.swal2-compact-toast {
            width: auto !important;
            min-width: 280px !important;
            max-width: 440px !important;
            padding: 10px 16px !important;
            border-radius: 14px !important;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.12), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
            border: 1px solid rgba(226, 232, 240, 0.9) !important;
            background: #ffffff !important;
        }
        .swal2-popup.swal2-toast.swal2-compact-toast .swal2-title {
            font-size: 13px !important;
            font-weight: 600 !important;
            color: #1e293b !important;
            margin: 0 !important;
            padding: 0 !important;
            line-height: 1.4 !important;
        }
        .swal2-popup.swal2-toast.swal2-compact-toast .swal2-html-container {
            font-size: 12px !important;
            color: #64748b !important;
            margin: 2px 0 0 0 !important;
            padding: 0 !important;
        }
    `;
    if (!document.getElementById('akuada-swal-compact-css')) {
        document.head.appendChild(style);
    }

    const AkuAdaAlert = {
        // Compact Toast notification
        toast: function(icon = 'success', title = '') {
            if (typeof Swal === 'undefined') {
                return console.log('[Toast]', icon, title);
            }
            
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3500,
                timerProgressBar: true,
                customClass: {
                    popup: 'swal2-compact-toast'
                },
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });

            return Toast.fire({
                icon: icon,
                title: title
            });
        },

        // Compact Success modal
        success: function(title, text = '', confirmBtnText = 'OK, Mengerti') {
            if (typeof Swal === 'undefined') return alert(title + (text ? '\n' + text : ''));
            return Swal.fire({
                icon: 'success',
                title: title,
                text: text,
                confirmButtonText: confirmBtnText,
                customClass: {
                    popup: 'swal2-compact-popup'
                }
            });
        },

        // Compact Error modal
        error: function(title, text = '') {
            if (typeof Swal === 'undefined') return alert(title + (text ? '\n' + text : ''));
            return Swal.fire({
                icon: 'error',
                title: title,
                text: text,
                confirmButtonText: 'Tutup',
                customClass: {
                    popup: 'swal2-compact-popup'
                }
            });
        },

        // Compact Warning modal
        warning: function(title, text = '') {
            if (typeof Swal === 'undefined') return alert(title + (text ? '\n' + text : ''));
            return Swal.fire({
                icon: 'warning',
                title: title,
                text: text,
                confirmButtonText: 'OK',
                customClass: {
                    popup: 'swal2-compact-popup'
                }
            });
        },

        // Compact Confirmation dialog
        confirm: function(title, text, confirmCallback, cancelCallback, confirmBtnText = 'Ya, Lanjutkan', icon = 'question') {
            if (typeof Swal === 'undefined') {
                if (window.confirm((title ? title + '\n' : '') + text)) {
                    if (typeof confirmCallback === 'function') confirmCallback();
                } else {
                    if (typeof cancelCallback === 'function') cancelCallback();
                }
                return;
            }

            return Swal.fire({
                title: title || 'Konfirmasi Tindakan',
                text: text || 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
                icon: icon,
                showCancelButton: true,
                confirmButtonText: confirmBtnText,
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    popup: 'swal2-compact-popup'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    if (typeof confirmCallback === 'function') confirmCallback();
                } else if (result.isDismissed) {
                    if (typeof cancelCallback === 'function') cancelCallback();
                }
            });
        },

        // B2B RFQ Submitted Dialog
        rfqSuccess: function(rfqNumber) {
            if (typeof Swal === 'undefined') return;
            return Swal.fire({
                icon: 'success',
                title: 'RFQ Berhasil Terkirim!',
                html: `
                    <div class="text-left text-xs text-slate-600 bg-purple-50 p-3 rounded-xl border border-purple-200 mt-2 space-y-1.5">
                        <p class="font-bold text-purple-900">Nomor RFQ: <span class="text-pink-600 font-mono">${rfqNumber}</span></p>
                        <p>Vendor terverifikasi akan meninjau spesifikasi dan mengirimkan penawaran harga resmi.</p>
                    </div>
                `,
                confirmButtonText: 'Lihat Status RFQ',
                showCancelButton: true,
                cancelButtonText: 'Tutup',
                customClass: {
                    popup: 'swal2-compact-popup'
                }
            });
        }
    };

    // Auto-convert native onsubmit="return confirm(...)" and onclick="return confirm(...)"
    function scanAndConvertConfirms() {
        // 1. Scan Forms
        document.querySelectorAll('form').forEach(function(form) {
            const onsub = form.getAttribute('onsubmit');
            if (onsub && onsub.includes('confirm(')) {
                const match = onsub.match(/confirm\s*\(\s*['"`](.*?)['"`]\s*\)/);
                if (match && match[1]) {
                    form.removeAttribute('onsubmit');
                    form.setAttribute('data-confirm', match[1]);
                }
            }
        });

        // 2. Scan Buttons & Links
        document.querySelectorAll('a[onclick], button[onclick]').forEach(function(el) {
            const onclick = el.getAttribute('onclick');
            if (onclick && onclick.includes('confirm(')) {
                const match = onclick.match(/confirm\s*\(\s*['"`](.*?)['"`]\s*\)/);
                if (match && match[1]) {
                    el.removeAttribute('onclick');
                    el.setAttribute('data-confirm', match[1]);
                }
            }
        });
    }

    // Global listener for logout actions (Automatic SweetAlert confirmation)
    document.addEventListener('click', function(e) {
        const logoutLink = e.target.closest('a[href*="auth/logout"], a[href$="/logout"], .btn-logout');
        if (!logoutLink) return;
        if (logoutLink.dataset.swalConfirmed === 'true') return;

        e.preventDefault();
        e.stopPropagation();

        AkuAdaAlert.confirm(
            'Konfirmasi Keluar Akun',
            'Apakah Anda yakin ingin keluar dari sesi akun Anda saat ini?',
            function() {
                logoutLink.dataset.swalConfirmed = 'true';
                window.location.href = logoutLink.href;
            },
            null,
            'Ya, Keluar',
            'question'
        );
        return false;
    }, true);

    // Global listener for forms with data-confirm
    document.addEventListener('submit', function(e) {
        const form = e.target;
        if (!form || form.tagName !== 'FORM') return;
        const confirmMsg = form.getAttribute('data-confirm');
        if (confirmMsg && !form.dataset.swalConfirmed) {
            e.preventDefault();
            e.stopPropagation();
            AkuAdaAlert.confirm('Konfirmasi', confirmMsg, function() {
                form.dataset.swalConfirmed = 'true';
                form.submit();
            });
            return false;
        }
    }, true);

    // Global listener for links/buttons with data-confirm
    document.addEventListener('click', function(e) {
        const el = e.target.closest('[data-confirm]');
        if (!el) return;

        // If it's a form submit button, let the form handler handle it
        if (el.tagName === 'BUTTON' && el.type === 'submit') return;

        const confirmMsg = el.getAttribute('data-confirm');
        if (confirmMsg && !el.dataset.swalConfirmed) {
            e.preventDefault();
            e.stopPropagation();
            AkuAdaAlert.confirm('Konfirmasi', confirmMsg, function() {
                el.dataset.swalConfirmed = 'true';
                if (el.tagName === 'A') {
                    window.location.href = el.href;
                } else if (typeof el.click === 'function') {
                    el.click();
                }
            });
            return false;
        }
    }, true);

    // Intercept native browser alert with compact SweetAlert toast
    const originalAlert = window.alert;
    window.alert = function(msg) {
        if (typeof Swal !== 'undefined') {
            AkuAdaAlert.toast('info', String(msg));
        } else {
            originalAlert(msg);
        }
    };

    // Auto run converter on DOMContentLoaded and dynamic mutations
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', scanAndConvertConfirms);
    } else {
        scanAndConvertConfirms();
    }
    document.addEventListener('click', function() { setTimeout(scanAndConvertConfirms, 60); });

    // Expose globally
    window.AkuAdaAlert = AkuAdaAlert;

})(window);
