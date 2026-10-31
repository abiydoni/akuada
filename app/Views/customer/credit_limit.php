<?= $this->extend('layouts/customer') ?>

<?= $this->section('customer_content') ?>
<?php
$maxTopDays     = get_max_top_days();
$allowedTerms   = get_allowed_top_terms();
$maxCreditLimit = (float)get_system_setting('max_credit_limit', 500000000);
$creditStep     = (float)get_system_setting('credit_step_amount', 25000000);
$limit          = (float)($company['credit_limit_top'] ?? get_system_setting('default_credit_limit', 100000000));
$used           = (float)($company['credit_used_top'] ?? 0);
$remaining      = max(0, $limit - $used);
$percentage     = $limit > 0 ? round(($used / $limit) * 100) : 0;
?>

<div class="space-y-6">
    <div>
        <h1 class="text-xl font-extrabold text-slate-900">Plafon Kredit Term of Payment (TOP)</h1>
        <p class="text-xs text-slate-500 mt-0.5">Kelola batas kredit pengadaan tempo tanpa bunga hingga <?= $maxTopDays ?> hari untuk perusahaan Anda.</p>
    </div>

    <!-- Status Card -->
    <div class="bg-gradient-to-br from-purple-900 via-indigo-900 to-slate-900 rounded-3xl p-8 text-white shadow-xl relative overflow-hidden">
        <div class="max-w-xl space-y-4 relative z-10">
            <span class="px-3 py-1 rounded-full bg-white/20 text-white text-[11px] font-bold uppercase tracking-wider backdrop-blur">
                Fasilitas Pembiayaan B2B <?= esc(get_system_setting('app_name', 'AkuAda')) ?>
            </span>
            <div>
                <span class="text-xs text-purple-200 block">Sisa Plafon Kredit Pengadaan Tersedia:</span>
                <span class="text-3xl sm:text-4xl font-black text-white"><?= format_rupiah($remaining) ?></span>
            </div>
            
            <div class="space-y-1.5 pt-2">
                <div class="flex justify-between text-xs text-purple-200">
                    <span>Terpakai: <strong><?= format_rupiah($used) ?> (<?= $percentage ?>%)</strong></span>
                    <span>Total Limit: <strong><?= format_rupiah($limit) ?></strong></span>
                </div>
                <div class="w-full bg-white/20 rounded-full h-3 overflow-hidden p-0.5">
                    <div class="bg-gradient-to-r from-pink-500 to-purple-400 h-2 rounded-full" style="width: <?= min(100, $percentage) ?>%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Limit Increase Form -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-4 text-xs">
        <div class="pb-3 border-b border-slate-100 flex items-center gap-2">
            <span class="material-symbols-outlined text-purple-600">trending_up</span>
            <h2 class="text-sm font-bold text-slate-800">Ajukan Kenaikan Plafon Kredit Pengadaan (TOP)</h2>
        </div>

        <form action="<?= base_url('customer/credit-limit/request') ?>" method="POST" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nominal Plafon Baru yang Diinginkan (Rp): <span class="text-rose-500">*</span></label>
                    <input type="number" name="requested_limit" required value="<?= min($maxCreditLimit, $limit + ($creditStep * 4)) ?>" step="<?= (int)$creditStep ?>" max="<?= (int)$maxCreditLimit ?>"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 font-extrabold text-base text-purple-900">
                    <p class="text-[10px] text-slate-400 mt-1">Kelipatan <?= format_rupiah($creditStep) ?> (Maksimal <?= format_rupiah($maxCreditLimit) ?> per PT).</p>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Pilihan Termin yang Diperlukan:</label>
                    <select name="preferred_top" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 bg-white font-semibold">
                        <?php foreach ($allowedTerms as $termDay): ?>
                            <option value="<?= $termDay ?>" <?= $termDay === 60 ? 'selected' : '' ?>>Net <?= $termDay ?> Hari Kalender</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block font-bold text-slate-700 mb-1">Alasan / Rencana Pengadaan:</label>
                    <textarea name="notes" rows="3" placeholder="Contoh: Kenaikan volume pengadaan kantor cabang baru semester depan dan proyek infrastruktur kantor." 
                              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600"></textarea>
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="btn-akuada-primary px-6 py-2.5 text-xs">
                    <span class="material-symbols-outlined text-[18px]">send</span>
                    <span>Kirim Pengajuan Kenaikan Plafon</span>
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
