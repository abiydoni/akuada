<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>
<?php
$buyerInfo       = get_active_buyer_credit_info();
$creditLimit     = $buyerInfo['credit_limit_top'];
$creditUsed      = $buyerInfo['credit_used_top'];
$creditRemaining = max(0, $creditLimit - $creditUsed);
$percentage      = $creditLimit > 0 ? round(($creditUsed / $creditLimit) * 100) : 0;
$appName         = get_system_setting('app_name', 'AkuAda');
?>
<div class="min-h-screen flex flex-col bg-slate-100">

    <!-- Customer Top Navbar -->
    <header class="bg-slate-900 text-white border-b border-pink-900/60 sticky top-0 z-30 shadow-md">
        <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="<?= base_url('customer/dashboard') ?>" class="flex items-center gap-2.5 group">
                    <img src="<?= base_url('apple-touch-icon.png') ?>" alt="AkuAda" class="w-9 h-9 rounded-xl object-contain shadow-md shadow-pink-500/20 group-hover:scale-105 transition-transform">
                    <div class="flex items-center">
                        <span class="text-lg font-black tracking-tight text-white font-sans">
                            Aku<span class="text-pink-400">Ada</span><span class="text-pink-500 font-black">.</span>
                        </span>
                        <span class="text-pink-400 font-semibold text-xs tracking-wider uppercase px-2 py-0.5 rounded bg-pink-950/80 border border-pink-800/60 ml-2">Portal Corporate Buyer</span>
                    </div>
                </a>
            </div>

            <div class="flex items-center gap-4">
                <a href="<?= base_url('customer/chat') ?>" class="portal-top-chat text-xs text-pink-200 hover:text-white flex items-center gap-1.5 bg-pink-950/80 hover:bg-pink-900 px-3 py-1.5 rounded-lg border border-pink-800 font-bold transition shadow-xs relative"><span class="material-symbols-outlined text-[16px] text-pink-400">forum</span><span>Chat Bantuan AkuAda</span><span id="globalChatUnreadBadge" class="hidden px-1.5 py-0.2 rounded-full bg-rose-500 text-white text-[9px] font-black animate-pulse">0</span></a> <a href="<?= base_url('/') ?>" class="text-xs text-slate-300 hover:text-white flex items-center gap-1 bg-slate-800 px-3 py-1.5 rounded-lg border border-slate-700">
                    <span class="material-symbols-outlined text-[15px]">storefront</span> Katalog Marketplace
                </a>
                
                <div class="flex items-center gap-2.5 border-l border-slate-700 pl-4">
                    <img src="<?= esc(session('user_avatar') ?? 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=128&q=80') ?>" class="w-8 h-8 rounded-full border border-pink-400 object-cover">
                    <div class="text-left hidden sm:block">
                        <span class="block text-xs font-bold text-white"><?= esc(session('user_name') ?? 'Procurement Manager') ?></span>
                        <span class="block text-[10px] text-pink-300"><?= esc($buyerInfo['company_name'] ?? session('company_name')) ?></span>
                    </div>
                    <a href="<?= base_url('auth/logout') ?>" data-confirm="Apakah Anda yakin ingin keluar dari sesi akun Anda saat ini?" class="ml-1 px-2.5 py-1.5 rounded-lg bg-rose-500/20 hover:bg-rose-500 text-rose-300 hover:text-white border border-rose-500/30 text-xs font-bold transition flex items-center gap-1" title="Keluar dari Portal Buyer">
                        <span class="material-symbols-outlined text-[15px]">logout</span>
                        <span class="hidden md:inline">Keluar</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full flex-grow flex flex-col md:flex-row gap-6">
        
        <!-- Customer Sidebar Navigation -->
        <aside class="w-full md:w-64 flex-shrink-0">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-4 space-y-6 sticky top-24">
                
                <!-- Buyer Cashback Balance Card -->
                <div class="p-3.5 rounded-xl bg-gradient-to-br from-purple-50 via-pink-50/50 to-white border border-purple-200 shadow-2xs">
                    <div class="flex items-center justify-between text-xs mb-1">
                        <span class="font-bold text-purple-900 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[15px] text-pink-600">savings</span>
                            Saldo Cashback
                        </span>
                        <span class="text-[10px] font-bold px-1.5 py-0.2 rounded-full bg-pink-100 text-pink-700 border border-pink-200">
                            <?= esc(get_buyer_cashback_rate((int)($buyerInfo['id'] ?? 0))) ?>% Lunas
                        </span>
                    </div>
                    <p class="text-base font-black font-mono text-purple-950 mt-1">
                        <?= format_rupiah($buyerInfo['cashback_balance'] ?? 0) ?>
                    </p>
                    <p class="text-[10px] text-slate-500 mt-0.5 mb-2 leading-tight">Akumulasi reward transaksi PO lunas</p>
                    <div class="pt-2 border-t border-purple-100 flex items-center justify-between text-[11px]">
                        <span class="text-slate-400 text-[10px]">Bisa ditarik ke bank</span>
                        <a href="<?= base_url('customer/cashback') ?>" class="font-bold text-pink-600 hover:text-pink-700 hover:underline flex items-center gap-0.5">
                            Tarik Saldo &rarr;
                        </a>
                    </div>
                </div>

                <!-- Monthly Credit Quota (Batas Nota Belum Lunas) -->
                <?php
                    $bCompanyId = session('company_id');
                    $isCompVerified = !empty($buyerInfo['is_verified']);
                    $activeUnpaidNotes = $bCompanyId ? get_buyer_unpaid_notes_count((int)$bCompanyId) : 0;
                    $monthlyNotesLimit = get_max_unpaid_notes_limit();
                    $hasNotesQuota = ($activeUnpaidNotes < $monthlyNotesLimit);
                ?>
                <?php if ($isCompVerified): ?>
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/80 text-xs space-y-1.5">
                        <div class="flex items-center justify-between text-[11px]">
                            <span class="font-bold text-slate-700 flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px] text-purple-600">receipt_long</span>
                                Batas Kredit Nota
                            </span>
                            <span class="text-[10px] font-bold px-1.5 py-0.2 rounded font-mono <?= $hasNotesQuota ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' ?>">
                                <?= $activeUnpaidNotes ?> / <?= $monthlyNotesLimit ?> Nota
                            </span>
                        </div>
                        <div class="w-full bg-slate-200 rounded-full h-1.5 overflow-hidden">
                            <div class="<?= $hasNotesQuota ? 'bg-purple-600' : 'bg-rose-600' ?> h-1.5 rounded-full" style="width: <?= min(100, round(($activeUnpaidNotes / $monthlyNotesLimit) * 100)) ?>%"></div>
                        </div>
                        <div class="flex justify-between items-center text-[10px] text-slate-500 font-medium pt-0.5">
                            <span><?= $hasNotesQuota ? 'Sisa ' . ($monthlyNotesLimit - $activeUnpaidNotes) . ' nota bulan ini' : 'Batas 3 nota tercapai' ?></span>
                            <a href="<?= base_url('customer/payables') ?>" class="text-purple-700 hover:underline">Bayar Tagihan</a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="p-3 rounded-xl bg-amber-50/90 border border-amber-300 text-xs space-y-1">
                        <div class="flex items-center justify-between text-[11px]">
                            <span class="font-bold text-amber-950 flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px] text-amber-600">hourglass_top</span>
                                Fasilitas Kredit (TOP)
                            </span>
                            <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-amber-200 text-amber-900">Menunggu Verifikasi</span>
                        </div>
                        <p class="text-[10px] text-amber-800 leading-tight">
                            Legalitas sedang ditinjau Admin. Transaksi dapat dilakukan via <strong>Bank Virtual Account</strong>.
                        </p>
                    </div>
                <?php endif; ?>

                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-600 px-3">Pengadaan Korporat</span>
                    <nav class="mt-2 space-y-1">
                        <a href="<?= base_url('customer/dashboard') ?>" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold <?= current_url() == base_url('customer/dashboard') ? 'bg-pink-600 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100' ?>">
                            <span class="material-symbols-outlined text-[18px]">space_dashboard</span> Dashboard Pengadaan
                        </a>
                        <a href="<?= base_url('customer/chat') ?>" class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-bold <?= str_contains(current_url(), 'customer/chat') ? 'bg-pink-600 text-white shadow-sm' : 'text-slate-800 bg-pink-50/70 border border-pink-200/80 hover:bg-pink-100/80 text-pink-950' ?>">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-[19px] text-pink-700">forum</span>
                                <span>Pusat Chat & Bantuan AkuAda</span>
                            </div>
                            <div class="flex items-center gap-1">
                                <span id="sidebarChatUnreadBadge" class="chat-unread-badge hidden px-1.5 py-0.2 rounded-full bg-rose-600 text-white text-[9px] font-black tracking-wider animate-pulse">0</span>
                                <span class="px-1.5 py-0.2 rounded-md bg-pink-600 text-white text-[9px] font-black tracking-wider">LIVE</span>
                            </div>
                        </a>
                        <a href="<?= base_url('customer/orders') ?>" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold <?= str_contains(current_url(), 'customer/orders') ? 'bg-pink-600 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100' ?>">
                            <span class="material-symbols-outlined text-[18px]">receipt_long</span> Purchase Order (PO) & BAST
                        </a>
                        <a href="<?= base_url('customer/payables') ?>" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold <?= str_contains(current_url(), 'customer/payables') ? 'bg-pink-600 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100' ?>">
                            <span class="material-symbols-outlined text-[18px]">payments</span> Tagihan & Bayar Hutang TOP
                        </a>
                        <a href="<?= base_url('customer/cashback') ?>" class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold <?= str_contains(current_url(), 'customer/cashback') ? 'bg-pink-600 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100' ?>">
                            <div class="flex items-center gap-2.5">
                                <span class="material-symbols-outlined text-[18px]">savings</span>
                                <span>Saldo Cashback & Tarik Dana</span>
                            </div>
                            <?php if (!empty($buyerInfo['cashback_balance']) && $buyerInfo['cashback_balance'] > 0): ?>
                                <span class="px-1.5 py-0.2 rounded-full bg-emerald-500 text-white text-[9px] font-mono font-black">ADA SALDO</span>
                            <?php endif; ?>
                        </a>

                        <a href="<?= base_url('customer/rfqs') ?>" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold <?= str_contains(current_url(), 'customer/rfqs') ? 'bg-pink-600 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100' ?>">
                            <span class="material-symbols-outlined text-[18px]">request_quote</span> Permintaan RFQ Saya
                        </a>
                    </nav>
                </div>

                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-600 px-3">Fiskal & Legal Perusahaan</span>
                    <nav class="mt-2 space-y-1">
                        <a href="<?= base_url('customer/tax-invoices') ?>" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold <?= str_contains(current_url(), 'customer/tax-invoices') ? 'bg-pink-600 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100' ?>">
                            <span class="material-symbols-outlined text-[18px]">receipt</span> e-Faktur Pajak PPN <?= esc(format_tax_rate()) ?>
                        </a>
                        <a href="<?= base_url('customer/company-profile') ?>" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold <?= str_contains(current_url(), 'customer/company-profile') ? 'bg-pink-600 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100' ?>">
                            <span class="material-symbols-outlined text-[18px]">badge</span> NPWP & Legalitas PT
                        </a>
                    </nav>
                </div>
            </div>
        </aside>

        <!-- Customer Content Area -->
        <section class="flex-1 min-w-0">
            <?= $this->renderSection('customer_content') ?>
        </section>

    </div>
</div>

<!-- Floating Live Chat Widget (Melayang Kanan Bawah untuk Buyer) -->
<?= view('components/floating_chat') ?>

<?= $this->endSection() ?>
