<?= $this->extend('layouts/admin') ?>

<?= $this->section('admin_content') ?>

<div class="space-y-6">
    <!-- Header Card -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full bg-purple-100 text-purple-800 text-[10px] font-bold uppercase tracking-wider">Fasilitas Kredit Korporat</span>
            </div>
            <h1 class="text-xl font-extrabold text-slate-900 mt-1">Manajemen Perusahaan Pembeli & Plafon Kredit TOP</h1>
            <p class="text-xs text-slate-500 mt-0.5">Daftarkan akun buyer baru, atur limit fasilitas tempo (TOP), dan pantau saldo kredit terpakai.</p>
        </div>
        <a href="<?= base_url('admin/buyers/create') ?>" class="btn-akuada-primary px-4 py-2.5 text-xs shadow-md flex items-center gap-2 self-start sm:self-auto">
            <span class="material-symbols-outlined text-[18px]">add_business</span>
            <span>+ Daftarkan Buyer Baru & Plafon TOP</span>
        </a>
    </div>

    <!-- Info Banner Card - Full width, clean padding -->
    <div class="p-4 rounded-2xl bg-gradient-to-r from-purple-50 via-blue-50 to-indigo-50 border border-purple-200/80 text-xs text-purple-950 flex items-start gap-3 shadow-sm">
        <span class="material-symbols-outlined text-purple-600 text-[22px] flex-shrink-0 mt-0.5">account_balance_wallet</span>
        <div class="space-y-0.5">
            <strong class="font-bold text-slate-900 block">Kebijakan Fasilitas Kredit Buyer AkuAda:</strong>
            <p class="text-slate-600 leading-relaxed">
                Setiap akun buyer didaftarkan oleh AkuAda dengan fasilitas plafon kredit Term of Payment (TOP). Buyer dapat memesan barang di katalog web hingga batas sisa plafon, dan pembayaran dilakukan secara tempo setelah barang diterima dan BAST disahkan. Buyer yang belum terdaftar tidak memiliki fasilitas TOP dan wajib menggunakan Bank Transfer VA.
            </p>
        </div>
    </div>

    <!-- Buyers Table Card - Fits 100% width with NO horizontal scroll -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-purple-600"></span>
                <h3 class="font-bold text-slate-800 text-sm">Daftar Corporate Buyer & Fasilitas Kredit</h3>
                <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 font-semibold text-[11px]"><?= count($buyers) ?> Perusahaan</span>
            </div>
        </div>

        <div class="w-full">
            <table class="w-full text-xs text-left border-collapse">
                <thead class="bg-slate-50/90 border-b border-slate-200 text-slate-600 font-semibold uppercase text-[11px] tracking-wider">
                    <tr>
                        <th class="px-4 py-3.5">Perusahaan Pembeli & Legalitas</th>
                        <th class="px-4 py-3.5">Kontak PIC Procurement</th>
                        <th class="px-4 py-3.5 text-right w-52">Plafon TOP & Terpakai</th>
                        <th class="px-4 py-3.5 text-right w-44">Sisa Saldo Plafon</th>
                        <th class="px-4 py-3.5 text-center w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    <?php if (empty($buyers)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-slate-400">
                                    <span class="material-symbols-outlined text-4xl mb-2 text-slate-300">corporate_fare</span>
                                    <p class="font-semibold text-slate-600">Belum ada akun corporate buyer terdaftar</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">Daftarkan perusahaan buyer untuk memberikan akses PO dan limit kredit TOP.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($buyers as $b): ?>
                        <?php 
                            $limit = (float)$b['credit_limit_top'];
                            $used = (float)$b['credit_used_top'];
                            $remaining = max(0, $limit - $used);
                            $usedPercent = $limit > 0 ? min(100, round(($used / $limit) * 100)) : 0;
                        ?>
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <!-- Company & Legality (No text cut-off) -->
                            <td class="px-4 py-3.5 align-top">
                                <div class="flex items-start gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-pink-100 text-pink-700 font-black flex items-center justify-center text-sm shadow-sm flex-shrink-0 mt-0.5">
                                        <?= strtoupper(substr($b['company_name'], 0, 1)) ?>
                                    </div>
                                    <div class="space-y-0.5">
                                        <div class="font-bold text-slate-900 leading-snug break-words"><?= esc($b['company_name']) ?></div>
                                        <div class="flex flex-wrap items-center gap-1.5 pt-0.5">
                                            <span class="px-1.5 py-0.5 rounded bg-purple-50 text-purple-700 font-bold text-[10px] border border-purple-200 uppercase"><?= esc($b['legal_type']) ?></span>
                                            <?php if ($b['is_verified']): ?>
                                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 text-[10px] font-semibold">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Terverifikasi
                                                </span>
                                            <?php else: ?>
                                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-amber-50 text-amber-700 text-[10px] font-semibold">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Belum Verif
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="text-[11px] text-slate-400 font-mono pt-0.5">
                                            NPWP: <?= esc($b['npwp'] ?? '-') ?> | NIB: <?= esc($b['nib'] ?? '-') ?>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- PIC (No text cut-off) -->
                            <td class="px-4 py-3.5 align-top">
                                <div class="font-bold text-slate-800 break-words"><?= esc($b['pic_name']) ?></div>
                                <div class="text-[11px] text-slate-500 font-mono mt-0.5"><?= esc($b['pic_phone'] ?? '-') ?></div>
                                <div class="text-[11px] text-slate-400 break-words"><?= esc($b['email']) ?></div>
                            </td>

                            <!-- Plafon Limit & Used with clean progress -->
                            <td class="px-4 py-3.5 align-top text-right font-mono">
                                <div class="font-extrabold text-purple-900 text-sm"><?= format_rupiah($limit) ?></div>
                                <div class="text-[11px] text-rose-600 font-semibold mt-0.5">
                                    Terpakai: <?= format_rupiah($used) ?> (<?= $usedPercent ?>%)
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-1.5 mt-1.5 overflow-hidden">
                                    <div class="bg-gradient-to-r from-purple-500 to-rose-500 h-1.5 rounded-full" style="width: <?= $usedPercent ?>%"></div>
                                </div>
                            </td>

                            <!-- Remaining Credit -->
                            <td class="px-4 py-3.5 align-top text-right font-mono">
                                <div class="font-black text-emerald-700 text-sm"><?= format_rupiah($remaining) ?></div>
                                <div class="text-[10px] text-emerald-600 font-semibold mt-0.5">Siap Belanja PO</div>
                            </td>

                            <!-- Action -->
                            <td class="px-4 py-3.5 align-top text-center">
                                <button type="button" 
                                        onclick="openLimitModal(<?= $b['id'] ?>, '<?= esc($b['company_name']) ?>', <?= $limit ?>)"
                                        class="px-3 py-1.5 rounded-lg text-xs font-bold bg-purple-50 hover:bg-purple-100 text-purple-700 transition inline-flex items-center gap-1 border border-purple-200 shadow-sm"
                                        title="Ubah Plafon Kredit TOP">
                                    <span class="material-symbols-outlined text-[15px]">edit_note</span>
                                    <span>Ubah Plafon</span>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Ubah Plafon Kredit Buyer -->
<div id="limitModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full shadow-2xl border border-slate-200 overflow-hidden text-xs animate-in fade-in zoom-in-95 duration-150">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-gradient-to-r from-purple-50 to-pink-50">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-purple-600">account_balance_wallet</span>
                <h3 class="font-bold text-slate-900 text-sm">Alokasi Plafon Kredit TOP</h3>
            </div>
            <button type="button" onclick="closeLimitModal()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>

        <form action="<?= base_url('admin/buyers/limit') ?>" method="POST" class="p-6 space-y-4">
            <input type="hidden" name="company_id" id="modal_buyer_id">
            
            <div>
                <span class="text-slate-400 text-[11px] block">Perusahaan Pembeli:</span>
                <strong id="modal_buyer_name" class="text-slate-900 text-sm font-bold block pt-0.5"></strong>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Nominal Plafon Kredit (Rp):</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 font-bold">Rp</span>
                    <input type="number" name="credit_limit_top" id="modal_limit_value" step="5000000" min="0" required
                           class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-purple-300 focus:border-purple-600 font-mono font-bold text-base text-purple-900">
                </div>
                <p class="text-[10px] text-slate-400 mt-1">Gunakan kelipatan Rp 5.000.000 atau nominal sesuai analisis kelayakan kredit.</p>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="closeLimitModal()" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-600 hover:bg-slate-50 font-semibold">
                    Batal
                </button>
                <button type="submit" class="btn-akuada-primary px-5 py-2 text-xs">
                    Simpan Plafon Baru
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openLimitModal(companyId, companyName, currentLimit) {
    document.getElementById('modal_buyer_id').value = companyId;
    document.getElementById('modal_buyer_name').textContent = companyName;
    document.getElementById('modal_limit_value').value = currentLimit;
    document.getElementById('limitModal').classList.remove('hidden');
}

function closeLimitModal() {
    document.getElementById('limitModal').classList.add('hidden');
}
</script>

<?= $this->endSection() ?>
