<?php
/**
 * Shared Multi-Party B2B Chat & Messaging Center
 * Usable by Admin, Corporate Buyer, and Mitra Vendor
 */
$currentUserRole = session('role') ?? 'customer';
$currentUserId   = (int)session('user_id');
$currentCompany  = session('company_name') ?? 'Perusahaan';
$activeKey       = $_GET['key'] ?? (!empty($_GET['order_id']) ? ('order_' . (int)$_GET['order_id']) : ($threads[0]['key'] ?? ''));

// Find active thread
$activeThread = null;
foreach ($threads as $t) {
    if ($t['key'] === $activeKey) {
        $activeThread = $t;
        break;
    }
}
if (!$activeThread && !empty($threads)) {
    $activeThread = $threads[0];
    $activeKey = $activeThread['key'];
}
?>

<div class="space-y-4">
    <!-- Top Bar Navigation & Actions -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/90 shadow-2xs">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-0.5">
                <span class="material-symbols-outlined text-[16px] text-purple-600">forum</span>
                <span class="font-bold text-slate-700">Komunikasi & Kolaborasi B2B</span>
                <span>•</span>
                <span class="text-slate-500"><?= esc($currentCompany) ?></span>
            </div>
            <h1 class="text-lg sm:text-xl font-black text-slate-900 tracking-tight">Pusat Pesan & Live Chat Terpadu</h1>
            <?php if ($currentUserRole === 'customer'): ?>
                <p class="text-xs text-slate-500">Pusat bantuan & komunikasi resmi pengadaan dengan CS dan Tim AkuAda.</p>
            <?php elseif ($currentUserRole === 'vendor'): ?>
                <p class="text-xs text-slate-500">Pusat bantuan & komunikasi resmi kemitraan supplier dengan Tim AkuAda.</p>
            <?php else: ?>
                <p class="text-xs text-slate-500">Saluran komunikasi personal 1-on-1 dengan Corporate Buyer, Mitra Vendor, dan Pengunjung Marketplace.</p>
            <?php endif; ?>
        </div>
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <button type="button" onclick="openNewChatModal()" class="w-full sm:w-auto px-4 py-2.5 rounded-xl bg-gradient-to-r from-purple-700 to-indigo-700 hover:from-purple-800 hover:to-indigo-800 text-white font-bold text-xs transition shadow-md shadow-purple-600/20 flex items-center justify-center gap-1.5 flex-shrink-0">
                <span class="material-symbols-outlined text-[18px]">add_comment</span>
                <span>+ Mulai Chat Baru</span>
            </button>
        </div>
    </div>

    <!-- Main Dual-Pane Chat Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-stretch min-h-[640px]">
        
        <!-- LEFT PANE: Conversation & Contact List (4 cols on desktop) -->
        <div class="lg:col-span-4 bg-white rounded-3xl border border-slate-200 shadow-sm flex flex-col h-[700px] overflow-hidden">
            <!-- Search & Filters -->
            <div class="p-4 border-b border-slate-100 space-y-3 bg-slate-50/60">
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-2.5 text-slate-400 text-[18px]">search</span>
                    <input type="text" id="chatSearchInput" onkeyup="filterConversationList()" placeholder="Cari kontak, vendor, atau no. PO..." class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-slate-200 focus:border-purple-600 focus:ring-2 focus:ring-purple-100 bg-white">
                </div>

                <!-- Filter Tabs -->
                <div class="flex items-center gap-1 overflow-x-auto text-[11px] pb-1 scrollbar-none">
                    <button type="button" onclick="filterCategory('all', this)" class="cat-pill active px-2.5 py-1 rounded-lg font-bold bg-purple-700 text-white">Semua</button>
                    <?php if ($currentUserRole === 'admin'): ?>
                        <button type="button" onclick="filterCategory('buyer', this)" class="cat-pill px-2.5 py-1 rounded-lg font-semibold bg-white text-slate-600 border border-slate-200 hover:bg-slate-100">Corporate Buyer</button>
                        <button type="button" onclick="filterCategory('vendor', this)" class="cat-pill px-2.5 py-1 rounded-lg font-semibold bg-white text-slate-600 border border-slate-200 hover:bg-slate-100">Mitra Vendor</button>
                        <button type="button" onclick="filterCategory('guest', this)" class="cat-pill px-2.5 py-1 rounded-lg font-semibold bg-white text-slate-600 border border-slate-200 hover:bg-slate-100">Tamu Web</button>
                    <?php else: ?>
                        <button type="button" onclick="filterCategory('support', this)" class="cat-pill px-2.5 py-1 rounded-lg font-semibold bg-white text-slate-600 border border-slate-200 hover:bg-slate-100">CS & Tim AkuAda</button>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Conversation Items List -->
            <div id="conversationItemsList" class="flex-1 overflow-y-auto divide-y divide-slate-100 p-2 space-y-1">
                <?php if (empty($threads)): ?>
                    <div class="p-8 text-center space-y-2">
                        <span class="material-symbols-outlined text-[32px] text-slate-300">chat_bubble_outline</span>
                        <p class="text-xs text-slate-500">Belum ada percakapan aktif.</p>
                        <button type="button" onclick="openNewChatModal()" class="text-purple-700 font-bold text-xs hover:underline">+ Mulai Chat Sekarang</button>
                    </div>
                <?php else: ?>
                    <?php foreach ($threads as $t): 
                        $isSelected = ($t['key'] === $activeKey);
                        $roleKey = ($t['counterparty_role'] === 'customer' || $t['counterparty_role'] === 'buyer') ? 'buyer' : $t['counterparty_role'];
                        $cat = ($roleKey === 'admin') ? 'support' : $roleKey;

                        // Avatar initials & color
                        $initial = strtoupper(substr($t['counterparty_name'], 0, 1));
                        $avatarBg = 'bg-purple-100 text-purple-800';
                        if ($roleKey === 'vendor') {
                            $avatarBg = 'bg-blue-100 text-blue-800';
                        } elseif ($roleKey === 'buyer') {
                            $avatarBg = 'bg-pink-100 text-pink-800';
                        } elseif ($roleKey === 'guest') {
                            $avatarBg = 'bg-emerald-100 text-emerald-800';
                        } elseif ($roleKey === 'admin') {
                            $avatarBg = 'bg-purple-100 text-purple-900';
                        }
                    ?>
                        <div onclick="selectConversation('<?= esc($t['key']) ?>')" 
                             data-key="<?= esc($t['key']) ?>"
                             data-category="<?= $cat ?>"
                             data-search="<?= strtolower(esc($t['counterparty_name'] . ' ' . $t['title'] . ' ' . ($t['po_number'] ?? '') . ' ' . ($t['order_number'] ?? ''))) ?>"
                             class="thread-card p-3 rounded-2xl cursor-pointer transition flex items-start gap-3 <?= $isSelected ? 'bg-purple-50/90 border-2 border-purple-400 shadow-2xs' : 'hover:bg-slate-50 border border-transparent' ?>">
                            
                            <!-- Avatar -->
                            <div class="relative flex-shrink-0">
                                <div class="w-10 h-10 rounded-xl <?= $avatarBg ?> font-black text-sm flex items-center justify-center">
                                    <?= $initial ?>
                                </div>
                                <?php if ($isSelected): ?>
                                    <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-emerald-500 border-2 border-white rounded-full"></span>
                                <?php endif; ?>
                            </div>

                            <!-- Info -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-1">
                                    <h4 class="text-xs font-bold text-slate-900 truncate"><?= esc($t['counterparty_name']) ?></h4>
                                    <span class="text-[10px] text-slate-400 font-mono flex-shrink-0"><?= esc($t['time_formatted'] ?? '') ?></span>
                                </div>

                                <div class="flex items-center gap-1.5 mt-0.5 flex-wrap">
                                    <span class="px-1.5 py-0.2 rounded text-[9px] font-bold <?= $avatarBg ?>">
                                        <?= esc($t['counterparty_badge']) ?>
                                    </span>
                                    <?php if (!empty($t['po_number'])): ?>
                                        <span class="px-1.5 py-0.2 rounded text-[9px] font-mono font-bold bg-slate-100 text-slate-700">
                                            <?= esc($t['po_number']) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <p class="text-[11px] text-slate-500 truncate mt-1">
                                    <?php if (!empty($t['last_sender'])): ?>
                                        <span class="font-medium text-slate-700"><?= esc($t['last_sender']) ?>:</span>
                                    <?php endif; ?>
                                    <?= esc($t['last_message'] ?: 'Mulai obrolan...') ?>
                                </p>
                            </div>

                            <?php if (!empty($t['unread_count']) && $t['unread_count'] > 0): ?>
                                <div class="flex flex-col items-end gap-1 flex-shrink-0">
                                    <span class="px-2 py-0.5 rounded-full bg-rose-600 text-white text-[10px] font-black shadow-sm animate-pulse">
                                        <?= (int)$t['unread_count'] ?> baru
                                    </span>
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-ping"></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- RIGHT PANE: Chat Conversation Window (8 cols on desktop) -->
        <div class="lg:col-span-8 bg-white rounded-3xl border border-slate-200 shadow-sm flex flex-col h-[700px] overflow-hidden">
            
            <?php if (!$activeThread): ?>
                <!-- Empty state when no thread selected -->
                <div class="flex-1 flex flex-col items-center justify-center p-8 text-center space-y-4">
                    <div class="w-16 h-16 rounded-3xl bg-purple-50 text-purple-600 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[36px]">chat_bubble_outline</span>
                    </div>
                    <h3 class="text-base font-bold text-slate-800">Pilih Percakapan untuk Memulai</h3>
                    <p class="text-xs text-slate-500 max-w-sm">
                        Pilih kontak dari daftar di sebelah kiri atau klik tombol "+ Mulai Chat Baru" untuk berdiskusi dengan vendor, buyer, atau admin AkuAda.
                    </p>
                    <button type="button" onclick="openNewChatModal()" class="px-5 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs inline-flex items-center gap-2 shadow-md">
                        <span class="material-symbols-outlined text-[18px]">add_comment</span>
                        <span>Mulai Chat Baru</span>
                    </button>
                </div>
            <?php else: ?>
                <!-- Active Conversation Header -->
                <div class="p-4 sm:p-5 border-b border-slate-100 bg-white flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 flex-shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-2xl bg-purple-100 text-purple-800 font-black text-base flex items-center justify-center shadow-xs">
                            <?= strtoupper(substr($activeThread['counterparty_name'], 0, 1)) ?>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-sm font-extrabold text-slate-900" id="headerCounterpartyName"><?= esc($activeThread['counterparty_name']) ?></h3>
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-purple-100 text-purple-800" id="headerCounterpartyBadge">
                                    <?= esc($activeThread['counterparty_badge']) ?>
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 mt-0.5 flex items-center gap-1.5" id="headerThreadTitle">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block"></span>
                                <span><?= esc($activeThread['title']) ?></span>
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                        <?php if (!empty($activeThread['order_id'])): ?>
                            <?php
                                $poLink = base_url($currentUserRole . '/orders/' . $activeThread['order_id']);
                            ?>
                            <a href="<?= $poLink ?>" target="_blank" class="px-3 py-1.5 rounded-xl bg-purple-50 hover:bg-purple-100 text-purple-800 font-bold text-xs border border-purple-200 transition flex items-center gap-1.5 shadow-2xs">
                                <span class="material-symbols-outlined text-[16px]">receipt_long</span>
                                <span>Buka Dokumen PO #<?= esc($activeThread['po_number'] ?? $activeThread['order_number']) ?> ↗</span>
                            </a>
                        <?php endif; ?>
                        <button type="button" id="chatSoundBtn" onclick="toggleChatSound()" title="Aktif/Mute Suara Notifikasi" class="px-2.5 py-1.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 transition flex items-center gap-1.5 text-xs font-semibold">
                            <span class="material-symbols-outlined text-[17px] text-purple-600" id="chatSoundIcon">volume_up</span>
                            <span id="chatSoundLabel" class="hidden sm:inline text-[11px]">Suara ON</span>
                        </button>
                        <button type="button" onclick="loadMessages(true)" title="Segarkan Pesan" class="p-2 rounded-xl text-slate-500 hover:bg-slate-100 border border-slate-200 transition">
                            <span class="material-symbols-outlined text-[18px]">refresh</span>
                        </button>
                    </div>
                </div>

                <!-- Messages Scroll Area -->
                <div id="messagesContainer" class="flex-1 p-4 sm:p-6 overflow-y-auto space-y-4 bg-slate-50/50">
                    <div id="messagesLoading" class="text-center py-8">
                        <span class="material-symbols-outlined text-purple-600 animate-spin text-[28px]">progress_activity</span>
                        <p class="text-xs text-slate-400 mt-2">Memuat percakapan...</p>
                    </div>
                    <div id="messagesList" class="space-y-4"></div>
                </div>

                <!-- Quick Action Chips for Fast Replies -->
                <div class="px-4 py-2 bg-white border-t border-slate-100 flex items-center gap-1.5 overflow-x-auto text-[11px] scrollbar-none flex-shrink-0">
                    <span class="text-slate-400 font-bold text-[10px] mr-1 flex-shrink-0">Cepat:</span>
                    <?php if ($currentUserRole === 'customer'): ?>
                        <button type="button" onclick="insertQuickReply('Halo, mohon konfirmasi kesiapan barang dan jadwal pengiriman.')" class="px-2.5 py-1 rounded-full bg-slate-100 hover:bg-purple-50 hover:text-purple-700 text-slate-600 border border-slate-200 transition whitespace-nowrap">Kapan kirim?</button>
                        <button type="button" onclick="insertQuickReply('Apakah surat jalan dan e-Faktur Pajak PPN sudah dapat diunduh?')" class="px-2.5 py-1 rounded-full bg-slate-100 hover:bg-purple-50 hover:text-purple-700 text-slate-600 border border-slate-200 transition whitespace-nowrap">Tanya Surat Jalan & Faktur</button>
                        <button type="button" onclick="insertQuickReply('Terima kasih, pesanan telah kami terima dalam kondisi baik.')" class="px-2.5 py-1 rounded-full bg-slate-100 hover:bg-purple-50 hover:text-purple-700 text-slate-600 border border-slate-200 transition whitespace-nowrap">Barang diterima baik</button>
                    <?php elseif ($currentUserRole === 'vendor'): ?>
                        <button type="button" onclick="insertQuickReply('Halo Tim AkuAda, pesanan saat ini sedang dalam penyiapan di gudang kami.')" class="px-2.5 py-1 rounded-full bg-slate-100 hover:bg-blue-50 hover:text-blue-700 text-slate-600 border border-slate-200 transition whitespace-nowrap">Penyiapan gudang</button>
                        <button type="button" onclick="insertQuickReply('Halo Tim AkuAda, pesanan telah selesai dikirim dengan armada/ekspedisi.')" class="px-2.5 py-1 rounded-full bg-slate-100 hover:bg-blue-50 hover:text-blue-700 text-slate-600 border border-slate-200 transition whitespace-nowrap">Status kirim</button>
                        <button type="button" onclick="insertQuickReply('Halo Tim AkuAda, mohon info terkait status pencairan settlement dana tagihan kami.')" class="px-2.5 py-1 rounded-full bg-slate-100 hover:bg-blue-50 hover:text-blue-700 text-slate-600 border border-slate-200 transition whitespace-nowrap">Tanya settlement</button>
                    <?php else: ?>
                        <button type="button" onclick="insertQuickReply('Halo, ada yang bisa tim Superadmin AkuAda bantu untuk transaksi ini?')" class="px-2.5 py-1 rounded-full bg-slate-100 hover:bg-purple-50 hover:text-purple-700 text-slate-600 border border-slate-200 transition whitespace-nowrap">Ada kendala transaksi?</button>
                        <button type="button" onclick="insertQuickReply('Pembayaran telah diverifikasi resmi oleh Finance AkuAda. Plafon kredit telah disesuaikan.')" class="px-2.5 py-1 rounded-full bg-slate-100 hover:bg-purple-50 hover:text-purple-700 text-slate-600 border border-slate-200 transition whitespace-nowrap">Verifikasi Finance</button>
                    <?php endif; ?>
                </div>

                <!-- Message Input Footer -->
                <div class="p-4 bg-white border-t border-slate-200 flex-shrink-0">
                    <form id="chatSendForm" onsubmit="handleSendMessage(event)" class="flex items-end gap-2">
                        <?= csrf_field() ?>
                        <input type="hidden" id="activeConvKeyInput" name="conversation_key" value="<?= esc($activeKey) ?>">
                        <input type="hidden" id="activeOrderIdInput" name="order_id" value="<?= esc($activeThread['order_id'] ?? '') ?>">

                        <div class="flex-1 relative">
                            <textarea id="chatMessageInput" 
                                      name="message" 
                                      rows="2" 
                                      placeholder="Tulis pesan ke <?= esc($activeThread['counterparty_name']) ?>... (Enter untuk kirim, Shift+Enter untuk baris baru)" 
                                      class="w-full px-4 py-2.5 text-xs rounded-2xl border border-slate-300 focus:border-purple-600 focus:ring-4 focus:ring-purple-100 bg-slate-50 focus:bg-white resize-none transition leading-relaxed"></textarea>
                        </div>

                        <button type="submit" id="chatSendButton" class="px-5 py-3 rounded-2xl bg-gradient-to-r from-purple-700 to-indigo-700 hover:from-purple-800 hover:to-indigo-800 text-white font-bold text-xs transition shadow-md shadow-purple-600/20 flex items-center gap-1.5 flex-shrink-0">
                            <span>Kirim</span>
                            <span class="material-symbols-outlined text-[16px]">send</span>
                        </button>
                    </form>
                    <div class="flex items-center justify-between mt-1.5 text-[10px] text-slate-400 px-1">
                        <span>🔒 Komunikasi tercatat aman untuk perlindungan transaksi B2B AkuAda.</span>
                        <span id="chatSendingIndicator" class="text-purple-600 font-bold hidden">Mengirim...</span>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal: Mulai Chat Baru -->
<div id="newChatModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 space-y-5 shadow-2xl border border-purple-200 animate-in fade-in zoom-in duration-150">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-10 h-10 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center font-bold">
                    <span class="material-symbols-outlined text-[20px]">add_comment</span>
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-slate-900"><?= $currentUserRole === 'admin' ? 'Mulai Percakapan / Chat Baru' : 'Mulai Chat dengan Tim AkuAda' ?></h3>
                    <p class="text-xs text-slate-500"><?= $currentUserRole === 'admin' ? 'Kirim pesan langsung ke Buyer atau Vendor' : 'Konsultasi dan koordinasi langsung dengan Tim Official AkuAda' ?></p>
                </div>
            </div>
            <button type="button" onclick="closeNewChatModal()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <form id="startNewChatForm" onsubmit="handleStartNewChat(event)" class="space-y-4">
            <?= csrf_field() ?>

            <!-- Pilih Tujuan Chat -->
            <div>
                <label class="block font-bold text-slate-700 text-xs mb-1">Tujuan / Pihak yang Ingin Dihubungi:</label>
                <?php if ($currentUserRole === 'customer' || $currentUserRole === 'vendor'): ?>
                    <select id="modalRecipientSelect" name="recipient_target" onchange="onRecipientChanged()" required class="w-full px-3.5 py-2.5 rounded-xl border border-purple-300 bg-purple-50/70 focus:border-purple-600 text-xs font-bold text-purple-900">
                        <option value="admin:0:Official Support AkuAda" selected>⭐ Official Support & Tim Platform AkuAda</option>
                    </select>
                    <p class="text-[10px] text-slate-500 mt-1"><?= $currentUserRole === 'vendor' ? 'Khusus akun Mitra Vendor, seluruh komunikasi dan koordinasi terpusat langsung ke Tim AkuAda.' : 'Khusus akun Corporate Buyer, seluruh koordinasi terpusat dan didampingi langsung oleh CS & Tim Pengadaan AkuAda.' ?></p>
                <?php else: ?>
                    <select id="modalRecipientSelect" name="recipient_target" onchange="onRecipientChanged()" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 text-xs bg-white font-semibold">
                        <option value="">-- Pilih Mitra atau Bantuan CS --</option>
                        
                        <!-- CS Support Platform -->
                        <option value="admin:0:Official Support AkuAda">⭐ Official Support & CS Platform AkuAda</option>
                        
                        <optgroup label="Daftar Perusahaan">
                            <?php foreach (($companies ?? []) as $c): ?>
                                <option value="<?= $c['type'] ?>:<?= $c['id'] ?>:<?= esc($c['company_name']) ?>">
                                    <?= $c['type'] === 'vendor' ? '🏢' : '🏨' ?> <?= esc($c['company_name']) ?> (<?= ucfirst($c['type']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </optgroup>
                    </select>
                <?php endif; ?>
            </div>

            <!-- Tautkan ke Pesanan PO (Opsional) -->
            <div>
                <label class="block font-bold text-slate-700 text-xs mb-1">Tautkan ke Purchase Order (PO) Spesifik (Opsional):</label>
                <select id="modalOrderSelect" name="order_id" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 focus:border-purple-600 text-xs bg-white">
                    <option value="">-- Tidak Ditautkan / Diskusi Umum --</option>
                    <?php foreach (($orders ?? []) as $ord): ?>
                        <option value="<?= $ord['id'] ?>">
                            PO: <?= esc($ord['po_number'] ?? $ord['order_number']) ?> (<?= esc($ord['order_status']) ?> - Rp <?= number_format($ord['total_amount'], 0, ',', '.') ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <p class="text-[10px] text-slate-400 mt-1">Jika dipilih, percakapan akan memiliki tag PO resmi sehingga riwayat transaksi terhubung.</p>
            </div>

            <!-- Subjek Percakapan -->
            <div>
                <label class="block font-bold text-slate-700 text-xs mb-1">Subjek / Topik Obrolan:</label>
                <input type="text" id="modalSubjectInput" name="subject" placeholder="Contoh: Tanya Ketersediaan Stok Minyak Goreng / Diskon Volume" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 focus:border-purple-600 text-xs">
            </div>

            <!-- Pesan Pembuka -->
            <div>
                <label class="block font-bold text-slate-700 text-xs mb-1">Pesan Pertama Anda:</label>
                <textarea id="modalMessageInput" name="message" rows="3" required placeholder="Tuliskan pesan pertanyaan atau penawaran Anda..." class="w-full px-3.5 py-2 rounded-xl border border-slate-300 focus:border-purple-600 text-xs"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="closeNewChatModal()" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50 font-bold text-xs">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-purple-700 hover:bg-purple-800 text-white font-bold text-xs transition flex items-center gap-1.5 shadow-md">
                    <span class="material-symbols-outlined text-[16px]">send</span>
                    <span>Kirim & Buka Chat</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let activeConversationKey = '<?= esc($activeKey) ?>';
    let pollInterval = null;

    document.addEventListener('DOMContentLoaded', function() {
        updateChatSoundUI();
        if (activeConversationKey) {
            loadMessages();
            // Start polling every 3.5 seconds
            pollInterval = setInterval(function() {
                loadMessages(false);
            }, 3500);
        }

        // Enter key to send message in textarea
        const msgInput = document.getElementById('chatMessageInput');
        if (msgInput) {
            msgInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    document.getElementById('chatSendForm').dispatchEvent(new Event('submit'));
                }
            });
        }
    });

    function selectConversation(key) {
        if (!key) return;
        activeConversationKey = key;
        
        // Update URL query without full reload
        const url = new URL(window.location);
        url.searchParams.set('key', key);
        window.history.pushState({}, '', url);

        // Update active card styling
        document.querySelectorAll('.thread-card').forEach(card => {
            if (card.getAttribute('data-key') === key) {
                card.classList.add('bg-purple-50/90', 'border-2', 'border-purple-400', 'shadow-2xs');
                card.classList.remove('border-transparent');
            } else {
                card.classList.remove('bg-purple-50/90', 'border-2', 'border-purple-400', 'shadow-2xs');
                card.classList.add('border-transparent');
            }
        });

        // Update inputs
        const keyInput = document.getElementById('activeConvKeyInput');
        if (keyInput) keyInput.value = key;

        loadMessages(true);
    }

    function loadMessages(showLoading = false) {
        if (!activeConversationKey) return;

        const loadingEl = document.getElementById('messagesLoading');
        const listEl = document.getElementById('messagesList');
        if (showLoading && loadingEl) loadingEl.style.display = 'block';

        fetch('<?= base_url('chat/messages') ?>/' + encodeURIComponent(activeConversationKey), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            if (loadingEl) loadingEl.style.display = 'none';
            if (data.status === 'success' && listEl) {
                renderMessages(data.messages, data.current_user_id, data.current_role);
            }
        })
        .catch(err => {
            if (loadingEl) loadingEl.style.display = 'none';
            console.error('Chat load error:', err);
        });
    }

    function renderMessages(messages, currentUserId, currentRole) {
        const listEl = document.getElementById('messagesList');
        if (!listEl) return;

        if (!messages || messages.length === 0) {
            listEl.innerHTML = `
                <div class="py-12 text-center text-slate-400 space-y-2">
                    <span class="material-symbols-outlined text-[36px] text-slate-300">forum</span>
                    <p class="text-xs">Belum ada percakapan pada topik ini.</p>
                    <p class="text-[11px] text-slate-400">Ketik pesan di bawah untuk memulai obrolan resmi.</p>
                </div>
            `;
            return;
        }

        let html = '';
        let lastDate = '';

        messages.forEach(m => {
            if (m.date_formatted !== lastDate) {
                lastDate = m.date_formatted;
                html += `
                    <div class="flex items-center justify-center my-3">
                        <span class="px-3 py-1 rounded-full bg-slate-200/70 text-slate-600 text-[10px] font-bold tracking-wide uppercase">
                            ${m.date_formatted}
                        </span>
                    </div>
                `;
            }

            const isMe = m.is_me;
            const roleBadgeClass = m.sender_role === 'vendor' ? 'bg-blue-100 text-blue-800' : (m.sender_role === 'admin' ? 'bg-purple-100 text-purple-900' : (m.sender_role === 'guest' ? 'bg-emerald-100 text-emerald-800' : 'bg-pink-100 text-pink-800'));
            const roleName = m.sender_role === 'vendor' ? 'Vendor' : (m.sender_role === 'admin' ? 'Superadmin' : (m.sender_role === 'guest' ? 'Tamu Publik' : 'Buyer'));

            if (isMe) {
                html += `
                    <div class="flex justify-end items-end gap-2">
                        <div class="max-w-md sm:max-w-lg space-y-1">
                            <div class="p-3.5 rounded-2xl rounded-tr-xs bg-gradient-to-r from-purple-700 to-indigo-700 text-white text-xs leading-relaxed shadow-sm">
                                ${m.message}
                            </div>
                            <div class="flex items-center justify-end gap-1 text-[10px] text-slate-400 font-mono">
                                <span>${m.time_formatted} WIB</span>
                                <span class="material-symbols-outlined text-[13px] text-emerald-500">done_all</span>
                            </div>
                        </div>
                    </div>
                `;
            } else {
                html += `
                    <div class="flex justify-start items-start gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-purple-100 text-purple-800 font-black text-xs flex items-center justify-center flex-shrink-0 mt-0.5">
                            ${(m.sender_name || 'U').charAt(0).toUpperCase()}
                        </div>
                        <div class="max-w-md sm:max-w-lg space-y-1">
                            <div class="flex items-center gap-1.5 text-[11px] flex-wrap">
                                <strong class="text-slate-900 font-bold">${m.sender_name}</strong>
                                ${m.sender_company_name ? '<span class="text-slate-500 font-semibold text-[10px]">(' + m.sender_company_name + ')</span>' : ''}
                                <span class="px-1.5 py-0.2 rounded text-[9px] font-bold ${roleBadgeClass}">${roleName}</span>
                            </div>
                            <div class="p-3.5 rounded-2xl rounded-tl-xs bg-white border border-slate-200/90 text-slate-800 text-xs leading-relaxed shadow-2xs">
                                ${m.message}
                            </div>
                            <div class="text-[10px] text-slate-400 font-mono">
                                <span>${m.time_formatted} WIB</span>
                            </div>
                        </div>
                    </div>
                `;
            }
        });

        // Check if new message arrived to play chime
        if (typeof window.lastChatMsgCount !== 'undefined' && messages.length > window.lastChatMsgCount) {
            const newest = messages[messages.length - 1];
            if (!newest.is_me && typeof AkuAdaAudio !== 'undefined') {
                AkuAdaAudio.playChime('chat');
            }
        }
        window.lastChatMsgCount = messages.length;

        // Only update if changed or first render
        const wasScrolledToBottom = (listEl.scrollHeight - listEl.clientHeight <= listEl.scrollTop + 50);
        listEl.innerHTML = html;
        if (wasScrolledToBottom) {
            scrollMessagesToBottom();
        }
    }

    function updateChatSoundUI() {
        const isMuted = typeof AkuAdaAudio !== 'undefined' ? AkuAdaAudio.isMuted() : false;
        const icon = document.getElementById('chatSoundIcon');
        const label = document.getElementById('chatSoundLabel');
        if (icon) icon.textContent = isMuted ? 'volume_off' : 'volume_up';
        if (label) label.textContent = isMuted ? 'Suara Mute' : 'Suara ON';
    }

    function toggleChatSound() {
        if (typeof AkuAdaAudio !== 'undefined') {
            const unmuted = AkuAdaAudio.toggleMute();
            updateChatSoundUI();
            if (unmuted) {
                AkuAdaAudio.playChime('chat');
            }
        }
    }

    function scrollMessagesToBottom() {
        const container = document.getElementById('messagesContainer');
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    }

    function handleSendMessage(e) {
        e.preventDefault();
        const input = document.getElementById('chatMessageInput');
        const text = input ? input.value.trim() : '';
        if (!text) return;

        const sendBtn = document.getElementById('chatSendButton');
        const indicator = document.getElementById('chatSendingIndicator');
        if (sendBtn) sendBtn.disabled = true;
        if (indicator) indicator.classList.remove('hidden');

        const formData = new FormData(document.getElementById('chatSendForm'));

        fetch('<?= base_url('chat/send') ?>', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            if (sendBtn) sendBtn.disabled = false;
            if (indicator) indicator.classList.add('hidden');
            if (data.status === 'success') {
                input.value = '';
                loadMessages(false);
                setTimeout(scrollMessagesToBottom, 150);
            } else {
                alert(data.message || 'Gagal mengirim pesan.');
            }
        })
        .catch(err => {
            if (sendBtn) sendBtn.disabled = false;
            if (indicator) indicator.classList.add('hidden');
            console.error('Send message error:', err);
            alert('Terjadi kesalahan saat mengirim pesan.');
        });
    }

    function insertQuickReply(text) {
        const input = document.getElementById('chatMessageInput');
        if (input) {
            input.value = text;
            input.focus();
        }
    }

    function filterConversationList() {
        const q = (document.getElementById('chatSearchInput')?.value || '').toLowerCase();
        document.querySelectorAll('.thread-card').forEach(card => {
            const searchData = card.getAttribute('data-search') || '';
            if (searchData.includes(q)) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    }

    function filterCategory(category, btn) {
        document.querySelectorAll('.cat-pill').forEach(p => {
            p.classList.remove('active', 'bg-purple-700', 'text-white');
            p.classList.add('bg-white', 'text-slate-600', 'border', 'border-slate-200');
        });
        btn.classList.add('active', 'bg-purple-700', 'text-white');
        btn.classList.remove('bg-white', 'text-slate-600', 'border');

        document.querySelectorAll('.thread-card').forEach(card => {
            const cardCat = card.getAttribute('data-category') || '';
            if (category === 'all' || cardCat === category) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    }

    // Modal Mulai Chat Baru
    function openNewChatModal() {
        const modal = document.getElementById('newChatModal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    }

    function closeNewChatModal() {
        const modal = document.getElementById('newChatModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    }

    function onRecipientChanged() {
        // Option to filter POs if needed
    }

    function handleStartNewChat(e) {
        e.preventDefault();
        const recipientVal = document.getElementById('modalRecipientSelect').value;
        const orderId = document.getElementById('modalOrderSelect').value;
        const subject = document.getElementById('modalSubjectInput').value.trim();
        const message = document.getElementById('modalMessageInput').value.trim();

        if (!recipientVal || !message) {
            alert('Silakan pilih pihak tujuan dan masukkan pesan pertama.');
            return;
        }

        const parts = recipientVal.split(':');
        const targetRole = parts[0];
        const targetCompanyId = parseInt(parts[1]) || 0;

        let convKey = '';
        if ('<?= $currentUserRole ?>' === 'admin') {
            if (targetCompanyId) {
                convKey = 'admin_comp_' + targetCompanyId;
            } else {
                convKey = 'admin_comp_<?= (int)session('company_id') ?>';
            }
        } else {
            // Customer dan Vendor selalu menggunakan saluran personal 1-on-1 dengan AkuAda
            convKey = 'admin_comp_<?= (int)session('company_id') ?>';
        }

        const formData = new FormData();
        formData.append('conversation_key', convKey);
        if (orderId) formData.append('order_id', orderId);
        formData.append('receiver_role', targetRole);
        if (targetCompanyId) formData.append('receiver_company_id', targetCompanyId);
        if (subject) formData.append('subject', subject);
        formData.append('message', message);
        formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

        fetch('<?= base_url('chat/send') ?>', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                closeNewChatModal();
                // Redirect or reload with new key
                window.location.href = '<?= current_url() ?>?key=' + encodeURIComponent(convKey);
            } else {
                alert(data.message || 'Gagal memulai chat.');
            }
        })
        .catch(err => {
            console.error('Start chat error:', err);
            alert('Terjadi kesalahan saat memulai chat.');
        });
    }
</script>
