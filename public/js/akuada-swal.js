/**
 * AkuAda SweetAlert2 Standardized Notifications
 * Unified modal alerts and toasts with AkuAda Purple-Blue-Pink Theme
 */

const AkuAdaAlert = {
  // Toast notification
  toast: function(icon, title) {
    if (typeof Swal === 'undefined') return alert(title);
    
    const Toast = Swal.mixin({
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 3500,
      timerProgressBar: true,
      didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer);
        toast.addEventListener('mouseleave', Swal.resumeTimer);
      }
    });

    Toast.fire({
      icon: icon || 'success',
      title: title
    });
  },

  // Success modal
  success: function(title, text, confirmBtnText = 'OK, Mengerti') {
    if (typeof Swal === 'undefined') return alert(title + ': ' + text);
    return Swal.fire({
      icon: 'success',
      title: title,
      text: text,
      confirmButtonText: confirmBtnText,
      confirmButtonColor: '#7c3aed',
      customClass: {
        popup: 'rounded-2xl shadow-2xl',
        confirmButton: 'px-5 py-2.5 rounded-lg text-white font-semibold'
      }
    });
  },

  // Error modal
  error: function(title, text) {
    if (typeof Swal === 'undefined') return alert(title + ': ' + text);
    return Swal.fire({
      icon: 'error',
      title: title,
      text: text,
      confirmButtonColor: '#ec4899',
      customClass: {
        popup: 'rounded-2xl shadow-2xl',
        confirmButton: 'px-5 py-2.5 rounded-lg text-white font-semibold'
      }
    });
  },

  // Confirmation dialog
  confirm: function(title, text, confirmCallback, cancelCallback) {
    if (typeof Swal === 'undefined') {
      if (confirm(text)) confirmCallback();
      return;
    }
    return Swal.fire({
      title: title,
      text: text,
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#7c3aed',
      cancelButtonColor: '#64748b',
      confirmButtonText: 'Ya, Lanjutkan',
      cancelButtonText: 'Batal',
      reverseButtons: true,
      customClass: {
        popup: 'rounded-2xl shadow-2xl',
        confirmButton: 'px-5 py-2.5 rounded-lg text-white font-semibold',
        cancelButton: 'px-5 py-2.5 rounded-lg font-semibold'
      }
    }).then((result) => {
      if (result.isConfirmed && typeof confirmCallback === 'function') {
        confirmCallback();
      } else if (result.isDismissed && typeof cancelCallback === 'function') {
        cancelCallback();
      }
    });
  },

  // B2B RFQ Submitted Dialog
  rfqSuccess: function(rfqNumber) {
    return Swal.fire({
      icon: 'success',
      title: 'Permintaan Penawaran (RFQ) Terkirim!',
      html: `
        <div class="text-left text-sm text-slate-600 bg-purple-50 p-4 rounded-xl border border-purple-200 mt-2 space-y-2">
          <p class="font-bold text-purple-900">Nomor RFQ: <span class="text-pink-600">${rfqNumber}</span></p>
          <p>Vendor terverifikasi akan meninjau spesifikasi dan memberikan penawaran harga resmi (Quotation) lengkap dengan simulasi termin TOP.</p>
          <p class="text-xs text-slate-500">Anda dapat memantau status negosiasi di <strong>Dashboard Perusahaan Buyer</strong>.</p>
        </div>
      `,
      confirmButtonColor: '#7c3aed',
      confirmButtonText: 'Lihat Status RFQ',
      showCancelButton: true,
      cancelButtonText: 'Kembali Belanja'
    });
  }
};

window.AkuAdaAlert = AkuAdaAlert;
