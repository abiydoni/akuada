<?php
/**
 * Modern Floating Live Chat Widget for Corporate Buyer & Marketplace
 * Floating bottom-right corner with smooth expandable drawer
 */
$isLoggedIn = !empty(session('user_id')) || !empty(session('is_logged_in'));
$currentUserRole = session('user_role') ?: (session('role') ?: 'guest');
$currentUserName = session('user_name') ?: 'Pengguna';
$currentCompany = session('company_name') ?: 'Perusahaan';
$fullChatUrl = ($currentUserRole === 'customer') ? base_url('customer/chat') : (($currentUserRole === 'vendor') ? base_url('vendor/chat') : base_url('admin/chat'));
?>

<!-- Floating Chat Root Container -->
<div id="floatingChatWidget" class="relative z-50 font-sans">
    
    <!-- 1. Floating Action Trigger Button (Kanan Bawah) -->
    <div id="floatingChatBtn" 
         onclick="toggleFloatingChat()" 
         class="fixed bottom-5 right-5 sm:bottom-6 sm:right-6 z-50 cursor-pointer group select-none">
        <div class="flex items-center gap-2.5 px-4 py-3 rounded-full bg-gradient-to-r from-purple-700 via-indigo-700 to-pink-600 hover:from-purple-800 hover:to-pink-700 text-white font-extrabold text-xs shadow-2xl shadow-purple-600/40 hover:scale-105 active:scale-95 transition-all duration-200 border-2 border-white/30 backdrop-blur">
            <div class="relative flex items-center justify-center">
                <span class="material-symbols-outlined text-[22px] transition-transform group-hover:rotate-12">forum</span>
                <span class="absolute -top-1 -right-1 flex h-2.5 w-2.5">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500 border border-white"></span>
                </span>
            </div>
            <span class="tracking-tight hidden xs:inline sm:inline">Live Chat B2B</span>
            <span id="floatingUnreadBadge" class="hidden px-1.5 py-0.2 rounded-full bg-rose-500 text-white text-[10px] font-black shadow-xs">1</span>
        </div>
    </div>

    <!-- 2. Expandable Floating Chat Window (Popup Kanan Bawah) -->
    <div id="floatingChatWindow" 
         class="fixed bottom-20 right-4 sm:bottom-24 sm:right-6 z-50 w-[380px] sm:w-[410px] max-w-[calc(100vw-2rem)] h-[560px] max-h-[85vh] rounded-3xl bg-white shadow-2xl border border-purple-200/90 hidden flex-col overflow-hidden transition-all duration-200">
        
        <!-- Header -->
        <div class="bg-gradient-to-r from-purple-800 via-indigo-800 to-pink-600 p-4 text-white flex items-center justify-between gap-3 shadow-sm flex-shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-white/15 backdrop-blur border border-white/20 flex items-center justify-center text-white font-black text-sm">
                    <span class="material-symbols-outlined text-[22px]">domain</span>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-sm font-black tracking-tight">AkuAda Live Chat</h3>
                        <span class="px-1.5 py-0.2 rounded bg-emerald-500/20 text-emerald-300 border border-emerald-400/30 text-[9px] font-bold flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 inline-block animate-pulse"></span>
                            Online
                        </span>
                    </div>
                    <p class="text-[11px] text-purple-100/90 leading-tight">Layanan CS & Koordinasi Pengadaan B2B</p>
                </div>
            </div>

            <div class="flex items-center gap-1">
                <?php if ($isLoggedIn && $currentUserRole === 'customer'): ?>
                    <a href="<?= $fullChatUrl ?>" title="Buka Layar Penuh di Halaman Chat" class="p-1.5 rounded-xl hover:bg-white/20 text-white/90 transition">
                        <span class="material-symbols-outlined text-[18px]">open_in_new</span>
                    </a>
                <?php endif; ?>
                <button type="button" onclick="toggleFloatingChat()" class="p-1.5 rounded-xl hover:bg-white/20 text-white/90 transition">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>
        </div>

        <!-- Conversation / Channel Selector -->
        <div class="bg-purple-50/80 px-3.5 py-2.5 border-b border-purple-100 flex items-center justify-between gap-2 flex-shrink-0 text-xs">
            <div class="flex items-center gap-1.5 flex-1 min-w-0">
                <span class="material-symbols-outlined text-[16px] text-purple-700 flex-shrink-0">chat</span>
                <select id="floatingConvSelect" onchange="onFloatingConvChanged()" class="bg-white border border-purple-200 text-slate-800 rounded-xl px-2.5 py-1 text-[11px] font-bold w-full focus:outline-none focus:border-purple-600 truncate">
                    <option value="">Memuat kontak...</option>
                </select>
            </div>
            <?php if (!$isLoggedIn): ?>
                <a href="<?= base_url('login') ?>" class="text-[10px] font-bold text-purple-700 hover:underline flex-shrink-0">
                    Masuk Akun
                </a>
            <?php endif; ?>
        </div>

        <!-- Messages Feed Container -->
        <div id="floatingMessagesArea" class="flex-1 p-3.5 sm:p-4 overflow-y-auto space-y-3 bg-slate-50/60">
            <div id="floatingLoadingState" class="py-8 text-center text-slate-400">
                <span class="material-symbols-outlined animate-spin text-[24px] text-purple-600">progress_activity</span>
                <p class="text-[11px] mt-1">Memuat obrolan...</p>
            </div>
            <div id="floatingMessagesList" class="space-y-3"></div>
        </div>

        <!-- Quick Reply Chips -->
        <div class="px-3 py-1.5 bg-white border-t border-slate-100 flex items-center gap-1.5 overflow-x-auto text-[10px] scrollbar-none flex-shrink-0">
            <span class="text-slate-400 font-bold mr-0.5">Tanya:</span>
            <button type="button" onclick="insertFloatingQuickReply('Halo, apakah stok barang ini tersedia untuk pengadaan korporat?')" class="px-2 py-0.5 rounded-full bg-slate-100 hover:bg-purple-50 hover:text-purple-700 text-slate-600 border border-slate-200 transition whitespace-nowrap">Stok siap?</button>
            <button type="button" onclick="insertFloatingQuickReply('Bagaimana ketentuan pengajuan plafon kredit tempo TOP 30/60 hari?')" class="px-2 py-0.5 rounded-full bg-slate-100 hover:bg-purple-50 hover:text-purple-700 text-slate-600 border border-slate-200 transition whitespace-nowrap">Tempo TOP?</button>
            <button type="button" onclick="insertFloatingQuickReply('Apakah setiap pembelian resmi diterbitkan Faktur Pajak PPN 0% / BAST?')" class="px-2 py-0.5 rounded-full bg-slate-100 hover:bg-purple-50 hover:text-purple-700 text-slate-600 border border-slate-200 transition whitespace-nowrap">e-Faktur?</button>
        </div>

        <!-- Chat Input Footer -->
        <div class="p-3 bg-white border-t border-slate-200 flex-shrink-0">
            <form id="floatingChatForm" onsubmit="handleSendFloatingMessage(event)" class="space-y-2">
                <?= csrf_field() ?>
                <input type="hidden" id="floatingActiveKeyInput" name="conversation_key" value="">
                
                <?php if ($isLoggedIn): ?>
                    <!-- Logged in identity: otomatis menggunakan akun login, tanpa meminta nama PIC / PT -->
                    <div class="flex items-center gap-1.5 text-[10px] text-slate-500 bg-slate-50 border border-slate-200/80 rounded-xl px-2.5 py-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block flex-shrink-0"></span>
                        <span class="truncate">Chat sebagai: <strong class="text-slate-800"><?= esc($currentUserName) ?></strong> (<span class="text-purple-700 font-semibold"><?= esc($currentCompany) ?></span>)</span>
                    </div>
                <?php else: ?>
                    <!-- Guest PIC input for unauthenticated marketplace visitors -->
                    <div class="grid grid-cols-2 gap-1.5 text-[10px]">
                        <input type="text" name="guest_name" placeholder="Nama Anda (PIC)..." class="px-2.5 py-1 rounded-lg border border-slate-200 bg-slate-50 text-[11px]">
                        <input type="text" name="guest_company" placeholder="Nama Perusahaan / PT..." class="px-2.5 py-1 rounded-lg border border-slate-200 bg-slate-50 text-[11px]">
                    </div>
                <?php endif; ?>

                <div class="flex items-center gap-1.5">
                    <input type="text" 
                           id="floatingMessageInput" 
                           name="message" 
                           placeholder="Ketik pesan pertanyaan pengadaan..." 
                           autocomplete="off"
                           class="flex-1 px-3.5 py-2 text-xs rounded-xl border border-slate-300 focus:border-purple-600 focus:ring-2 focus:ring-purple-100 bg-slate-50 focus:bg-white transition">
                    
                    <button type="submit" 
                            id="floatingSendBtn"
                            class="p-2 sm:px-3 sm:py-2 rounded-xl bg-purple-700 hover:bg-purple-800 text-white font-bold text-xs transition shadow-md shadow-purple-600/20 flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-[18px]">send</span>
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

<script>
    let isFloatingChatOpen = false;
    let floatingActiveKey = '';
    let floatingPollInterval = null;

    function toggleFloatingChat() {
        const win = document.getElementById('floatingChatWindow');
        if (!win) return;

        isFloatingChatOpen = !isFloatingChatOpen;

        if (isFloatingChatOpen) {
            win.classList.remove('hidden');
            win.classList.add('flex');
            initFloatingChatData();

            if (!floatingPollInterval) {
                floatingPollInterval = setInterval(function() {
                    if (isFloatingChatOpen && floatingActiveKey) {
                        fetchFloatingMessages(false);
                    }
                }, 3500);
            }
        } else {
            win.classList.add('hidden');
            win.classList.remove('flex');
            if (floatingPollInterval) {
                clearInterval(floatingPollInterval);
                floatingPollInterval = null;
            }
        }
    }

    function initFloatingChatData() {
        fetch('<?= base_url('chat/conversations') ?>', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success' && data.threads) {
                renderFloatingDropdown(data.threads, data.guest_key);
            }
        })
        .catch(err => {
            console.error('Error fetching floating chat conversations:', err);
        });
    }

    function renderFloatingDropdown(threads, guestKey) {
        const select = document.getElementById('floatingConvSelect');
        if (!select) return;

        select.innerHTML = '';
        threads.forEach((t, idx) => {
            const opt = document.createElement('option');
            opt.value = t.key;
            opt.textContent = (t.counterparty_name || t.title) + (t.po_number ? ' (' + t.po_number + ')' : '');
            select.appendChild(opt);
        });

        if (threads.length > 0) {
            if (!floatingActiveKey || !threads.some(t => t.key === floatingActiveKey)) {
                floatingActiveKey = threads[0].key;
            }
            select.value = floatingActiveKey;
            document.getElementById('floatingActiveKeyInput').value = floatingActiveKey;
            fetchFloatingMessages(true);
        }
    }

    function onFloatingConvChanged() {
        const select = document.getElementById('floatingConvSelect');
        if (!select) return;
        floatingActiveKey = select.value;
        document.getElementById('floatingActiveKeyInput').value = floatingActiveKey;
        fetchFloatingMessages(true);
    }

    function fetchFloatingMessages(showLoading = false) {
        if (!floatingActiveKey) return;
        const loadingEl = document.getElementById('floatingLoadingState');
        const listEl = document.getElementById('floatingMessagesList');
        if (showLoading && loadingEl) loadingEl.style.display = 'block';

        fetch('<?= base_url('chat/messages') ?>/' + encodeURIComponent(floatingActiveKey), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            if (loadingEl) loadingEl.style.display = 'none';
            if (data.status === 'success') {
                renderFloatingMessages(data.messages);
            }
        })
        .catch(err => {
            if (loadingEl) loadingEl.style.display = 'none';
            console.error('Fetch floating messages error:', err);
        });
    }

    function renderFloatingMessages(messages) {
        const listEl = document.getElementById('floatingMessagesList');
        if (!listEl) return;

        if (!messages || messages.length === 0) {
            listEl.innerHTML = `
                <div class="py-10 text-center text-slate-400 space-y-1">
                    <span class="material-symbols-outlined text-[32px] text-slate-300">chat_bubble_outline</span>
                    <p class="text-xs font-bold text-slate-600">Mulai Obrolan Resmi</p>
                    <p class="text-[10px] text-slate-400">Ketik pesan di bawah untuk terhubung langsung dengan tim kami.</p>
                </div>
            `;
            return;
        }

        let html = '';
        messages.forEach(m => {
            if (m.is_me) {
                html += `
                    <div class="flex justify-end items-end gap-1.5">
                        <div class="max-w-[85%] space-y-0.5">
                            <div class="p-2.5 rounded-2xl rounded-tr-xs bg-gradient-to-r from-purple-700 to-indigo-700 text-white text-[11px] leading-relaxed shadow-xs">
                                ${m.message}
                            </div>
                            <div class="text-[9px] text-slate-400 font-mono text-right flex items-center justify-end gap-1">
                                <span>${m.time_formatted}</span>
                                <span class="material-symbols-outlined text-[12px] text-emerald-500">done_all</span>
                            </div>
                        </div>
                    </div>
                `;
            } else {
                html += `
                    <div class="flex justify-start items-start gap-2">
                        <div class="w-7 h-7 rounded-lg bg-purple-100 text-purple-800 font-black text-[11px] flex items-center justify-center flex-shrink-0 mt-0.5">
                            ${(m.sender_name || 'U').charAt(0).toUpperCase()}
                        </div>
                        <div class="max-w-[85%] space-y-0.5">
                            <span class="text-[10px] font-bold text-slate-700 block">${m.sender_name}</span>
                            <div class="p-2.5 rounded-2xl rounded-tl-xs bg-white border border-slate-200/90 text-slate-800 text-[11px] leading-relaxed shadow-2xs">
                                ${m.message}
                            </div>
                            <span class="text-[9px] text-slate-400 font-mono block">${m.time_formatted}</span>
                        </div>
                    </div>
                `;
            }
        });

        listEl.innerHTML = html;
        scrollFloatingChatToBottom();
    }

    function scrollFloatingChatToBottom() {
        const area = document.getElementById('floatingMessagesArea');
        if (area) {
            area.scrollTop = area.scrollHeight;
        }
    }

    function insertFloatingQuickReply(text) {
        const input = document.getElementById('floatingMessageInput');
        if (input) {
            input.value = text;
            input.focus();
        }
    }

    function handleSendFloatingMessage(e) {
        e.preventDefault();
        const input = document.getElementById('floatingMessageInput');
        const text = input ? input.value.trim() : '';
        if (!text) return;

        const sendBtn = document.getElementById('floatingSendBtn');
        if (sendBtn) sendBtn.disabled = true;

        const formData = new FormData(document.getElementById('floatingChatForm'));

        fetch('<?= base_url('chat/send') ?>', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            if (sendBtn) sendBtn.disabled = false;
            if (data.status === 'success') {
                input.value = '';
                fetchFloatingMessages(false);
            } else {
                alert(data.message || 'Gagal mengirim pesan.');
            }
        })
        .catch(err => {
            if (sendBtn) sendBtn.disabled = false;
            console.error('Floating send error:', err);
        });
    }
</script>

