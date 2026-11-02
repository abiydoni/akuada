<?php
/**
 * Komponen Real-Time Live Chat Transaksi PO
 * Variables:
 * - $orderId (int)
 * - $orderNumber (string)
 * - $role (string: 'customer', 'vendor', 'admin')
 */
$chatId = 'chat_box_' . $orderId;
?>

<div class="bg-white rounded-3xl border border-purple-200/80 shadow-lg overflow-hidden flex flex-col h-[520px]" id="<?= $chatId ?>">
    <!-- Chat Header -->
    <div class="px-5 py-4 bg-gradient-to-r from-slate-900 via-purple-950 to-slate-900 text-white flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-purple-600 via-pink-500 to-amber-400 flex items-center justify-center text-white shadow-md">
                <span class="material-symbols-outlined text-[20px]">forum</span>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="text-sm font-extrabold tracking-tight">Diskusi & Live Chat PO</h3>
                    <span class="flex h-2 w-2 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    <span class="text-[10px] text-emerald-400 font-semibold tracking-wide uppercase">Aktif</span>
                </div>
                <p class="text-[11px] text-slate-300 font-mono">PO: <?= esc($orderNumber) ?></p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <span class="text-[10px] bg-white/10 px-2.5 py-1 rounded-full text-purple-200 font-semibold hidden sm:inline-flex items-center gap-1">
                <span class="material-symbols-outlined text-[13px]">lock</span>
                Terenkripsi & Terpantau Admin
            </span>
        </div>
    </div>

    <!-- Messages Container (Scrollable) -->
    <div id="<?= $chatId ?>_messages" class="flex-1 overflow-y-auto p-4 sm:p-5 space-y-3 bg-gradient-to-b from-slate-50/70 to-purple-50/20 text-xs">
        <div class="flex items-center justify-center py-6 text-slate-400 gap-2" id="<?= $chatId ?>_loading">
            <span class="material-symbols-outlined animate-spin text-[18px]">progress_activity</span>
            <span>Memuat pesan diskusi transaksi...</span>
        </div>
    </div>

    <!-- Chat Input Form -->
    <div class="p-3 sm:p-4 bg-white border-t border-slate-200/80">
        <form id="<?= $chatId ?>_form" class="flex items-center gap-2" onsubmit="event.preventDefault(); submitChatMessage_<?= $orderId ?>();">
            <div class="relative flex-1">
                <input type="text" 
                       id="<?= $chatId ?>_input" 
                       name="message" 
                       placeholder="Ketik pesan mengenai pesanan ini... (Tekan Enter)" 
                       autocomplete="off"
                       class="w-full pl-4 pr-10 py-2.5 rounded-2xl border border-slate-300 bg-slate-50/60 focus:bg-white focus:border-purple-600 focus:ring-2 focus:ring-purple-500/20 text-xs font-medium text-slate-800 transition outline-none">
                <button type="button" onclick="document.getElementById('<?= $chatId ?>_input').focus()" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-purple-600">
                    <span class="material-symbols-outlined text-[18px]">edit_note</span>
                </button>
            </div>
            <button type="submit" 
                    id="<?= $chatId ?>_btn" 
                    class="px-4 py-2.5 rounded-2xl bg-gradient-to-r from-purple-700 to-pink-600 hover:from-purple-800 hover:to-pink-700 text-white font-bold text-xs flex items-center gap-1.5 shadow-md shadow-purple-500/20 transition-all flex-shrink-0 disabled:opacity-50">
                <span class="material-symbols-outlined text-[16px]">send</span>
                <span class="hidden sm:inline">Kirim</span>
            </button>
        </form>
        <div class="flex items-center justify-between text-[10px] text-slate-400 px-1 pt-1.5">
            <span>Koordinasi cepat langsung dengan <?= $role === 'customer' ? 'Mitra Vendor & Tim CS AkuAda' : ($role === 'vendor' ? 'Corporate Buyer & Tim CS AkuAda' : 'Buyer & Mitra Vendor') ?></span>
            <span class="hidden sm:inline">Tekan <kbd class="px-1 py-0.5 bg-slate-100 rounded border border-slate-200 font-mono text-[9px]">Enter</kbd> untuk kirim</span>
        </div>
    </div>
</div>

<script>
(function() {
    const orderId = <?= (int)$orderId ?>;
    const boxId = '<?= $chatId ?>';
    const messagesEl = document.getElementById(boxId + '_messages');
    const inputEl = document.getElementById(boxId + '_input');
    const btnEl = document.getElementById(boxId + '_btn');
    const loadingEl = document.getElementById(boxId + '_loading');

    let lastMessageCount = 0;
    let isInitialLoad = true;

    function renderMessages(messages) {
        if (!messages || messages.length === 0) {
            messagesEl.innerHTML = `
                <div class="h-full flex flex-col items-center justify-center text-center p-6 text-slate-400 space-y-2">
                    <div class="w-12 h-12 rounded-2xl bg-purple-100 text-purple-600 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[24px]">chat_bubble_outline</span>
                    </div>
                    <strong class="text-slate-700 text-xs">Belum Ada Riwayat Percakapan</strong>
                    <p class="text-[11px] max-w-xs text-slate-500">Mulai kirimkan pesan untuk mendiskusikan jadwal pengiriman, spesifikasi barang, atau konfirmasi penerimaan fisik di gudang.</p>
                </div>
            `;
            return;
        }

        let html = '';
        let lastDate = '';

        messages.forEach(msg => {
            if (msg.date_formatted !== lastDate) {
                lastDate = msg.date_formatted;
                html += `
                    <div class="flex items-center justify-center my-3">
                        <span class="px-3 py-1 rounded-full bg-slate-200/80 text-slate-600 text-[10px] font-bold tracking-wide uppercase">${msg.date_formatted}</span>
                    </div>
                `;
            }

            const isMe = msg.is_me;
            let roleBadge = '';
            if (msg.sender_role === 'customer') {
                roleBadge = '<span class="px-1.5 py-0.5 rounded bg-purple-100 text-purple-800 text-[9px] font-bold">Buyer</span>';
            } else if (msg.sender_role === 'vendor') {
                roleBadge = '<span class="px-1.5 py-0.5 rounded bg-blue-100 text-blue-800 text-[9px] font-bold">Vendor</span>';
            } else {
                roleBadge = '<span class="px-1.5 py-0.5 rounded bg-amber-100 text-amber-800 text-[9px] font-bold">Superadmin CS</span>';
            }

            if (isMe) {
                html += `
                    <div class="flex flex-col items-end space-y-1">
                        <div class="max-w-[85%] sm:max-w-[70%] bg-gradient-to-r from-purple-700 to-indigo-600 text-white p-3 rounded-2xl rounded-tr-none shadow-sm">
                            <p class="leading-relaxed whitespace-pre-wrap break-words">${msg.message}</p>
                            <div class="flex items-center justify-end gap-1 mt-1 text-[10px] text-purple-200 font-mono">
                                <span>${msg.time_formatted}</span>
                                <span class="material-symbols-outlined text-[12px]">done_all</span>
                            </div>
                        </div>
                    </div>
                `;
            } else {
                html += `
                    <div class="flex flex-col items-start space-y-1">
                        <div class="flex items-center gap-1.5 pl-1">
                            <strong class="text-slate-800 text-[11px] font-bold">${msg.sender_name}</strong>
                            ${roleBadge}
                        </div>
                        <div class="max-w-[85%] sm:max-w-[70%] bg-white border border-slate-200/90 text-slate-800 p-3 rounded-2xl rounded-tl-none shadow-sm">
                            <p class="leading-relaxed whitespace-pre-wrap break-words">${msg.message}</p>
                            <div class="flex items-center justify-end gap-1 mt-1 text-[10px] text-slate-400 font-mono">
                                <span>${msg.time_formatted}</span>
                            </div>
                        </div>
                    </div>
                `;
            }
        });

        messagesEl.innerHTML = html;

        // Auto-scroll to bottom if initial load or new messages arrived
        if (isInitialLoad || messages.length > lastMessageCount) {
            messagesEl.scrollTop = messagesEl.scrollHeight;
        }
        lastMessageCount = messages.length;
        isInitialLoad = false;
    }

    function fetchChatMessages() {
        fetch('<?= base_url('chat/messages/') ?>' + orderId, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                renderMessages(data.messages);
            }
        })
        .catch(err => console.error('Chat poll error:', err));
    }

    window['submitChatMessage_' + orderId] = function() {
        const text = inputEl.value.trim();
        if (!text) return;

        btnEl.disabled = true;
        const formData = new FormData();
        formData.append('order_id', orderId);
        formData.append('message', text);

        // Append CSRF token if meta tag or form exists
        const csrfTokenMeta = document.querySelector('meta[name="csrf-token-name"]');
        const csrfHashMeta = document.querySelector('meta[name="csrf-hash"]');
        if (csrfTokenMeta && csrfHashMeta) {
            formData.append(csrfTokenMeta.content, csrfHashMeta.content);
        }

        fetch('<?= base_url('chat/send') ?>', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            btnEl.disabled = false;
            if (data.status === 'success') {
                inputEl.value = '';
                fetchChatMessages();
            } else {
                AkuAdaAlert.error('Gagal Mengirim', data.message || 'Terjadi kesalahan pengiriman.');
            }
        })
        .catch(err => {
            btnEl.disabled = false;
            console.error('Chat send error:', err);
        });
    };

    // Initial load
    fetchChatMessages();

    // Auto-polling every 3.5 seconds
    const interval = setInterval(fetchChatMessages, 3500);

    // Clean up on unload
    window.addEventListener('beforeunload', () => clearInterval(interval));
})();
</script>

