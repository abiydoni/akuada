<?= $this->extend('layouts/admin') ?>

<?= $this->section('admin_content') ?>

<div class="space-y-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full bg-purple-100 text-purple-800 text-[10px] font-bold">Otoritas Pendaftaran Superadmin</span>
            </div>
            <h1 class="text-xl font-extrabold text-slate-900 mt-1">Verifikasi & Manajemen Mitra Vendor B2B</h1>
            <p class="text-xs text-slate-500 mt-0.5">Hanya Superadmin yang berhak mendaftarkan vendor baru dan menetapkan tarif komisi kesepakatan PKS tiap vendor.</p>
        </div>
        <a href="<?= base_url('admin/vendors/create') ?>" class="btn-akuada-primary px-4 py-2.5 text-xs shadow-md flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px]">person_add</span>
            <span>+ Daftarkan Mitra Vendor Baru</span>
        </a>
    </div>

    <!-- Info Notice Box -->
    <div class="p-4 rounded-2xl bg-gradient-to-r from-purple-50 via-blue-50 to-pink-50 border border-purple-200 text-xs flex items-start gap-3">
        <span class="material-symbols-outlined text-purple-700 text-[22px] flex-shrink-0 mt-0.5">policy</span>
        <div class="space-y-1">
            <strong class="text-slate-900 font-bold block">Kebijakan Fee & Kemitraan Vendor B2B:</strong>
            <p class="text-slate-600 leading-relaxed">
                Pendaftaran mitra supplier dilakukan secara terpusat oleh Admin setelah uji kelayakan legalitas (NPWP & NIB) dan penandatanganan Perjanjian Kerjasama (PKS). <strong>Tarif fee platform dapat disesuaikan berbeda-beda untuk tiap vendor</strong> sesuai kesepakatan kontrak. Setelah terdaftar, vendor memiliki wewenang penuh mengelola katalog barang yang mereka jual sendiri.
            </p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase text-[10px]">
                    <tr>
                        <th class="p-4">Nama Perusahaan Vendor</th>
                        <th class="p-4">Legalitas (NPWP / NIB)</th>
                        <th class="p-4 text-purple-900">Fee Kesepakatan PKS</th>
                        <th class="p-4">Kontak PIC</th>
                        <th class="p-4">Kota</th>
                        <th class="p-4">Status Akun</th>
                        <th class="p-4 text-center">Kelola Fee & Verifikasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($vendors as $vendor): ?>
                        <tr class="hover:bg-slate-50 transition">
                            <td class="p-4">
                                <div class="flex items-center gap-2">
                                    <span class="w-8 h-8 rounded-lg bg-purple-100 text-purple-700 font-bold flex items-center justify-center text-xs">
                                        <?= substr($vendor['company_name'], 0, 1) ?>
                                    </span>
                                    <div>
                                        <strong class="text-slate-900 block"><?= esc($vendor['company_name']) ?></strong>
                                        <span class="text-[10px] text-slate-400 font-mono"><?= esc($vendor['email']) ?></span>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4">
                                <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-bold text-[10px] inline-block mb-1"><?= esc($vendor['legal_type']) ?></span>
                                <span class="text-[10px] text-slate-500 block font-mono">NPWP: <?= esc($vendor['npwp'] ?? '-') ?></span>
                                <span class="text-[10px] text-slate-400 block font-mono">NIB: <?= esc($vendor['nib'] ?? '-') ?></span>
                            </td>
                            <td class="p-4">
                                <div class="flex items-center gap-1.5">
                                    <span class="px-2.5 py-1 rounded-lg bg-gradient-to-r from-purple-100 to-indigo-100 text-purple-900 font-black text-xs border border-purple-200">
                                        <?= number_format((float)($vendor['commission_rate'] ?? 2.5), 1) ?>%
                                    </span>
                                    <button type="button" onclick="openFeeModal(<?= $vendor['id'] ?>, '<?= esc($vendor['company_name']) ?>', <?= (float)($vendor['commission_rate'] ?? 2.5) ?>)" 
                                            class="p-1 rounded hover:bg-purple-100 text-purple-700 transition" title="Ubah Kesepakatan Fee">
                                        <span class="material-symbols-outlined text-[16px]">edit</span>
                                    </button>
                                </div>
                                <span class="text-[10px] text-slate-400 block mt-0.5">Sesuai Perjanjian PKS</span>
                            </td>
                            <td class="p-4">
                                <span class="font-semibold text-slate-800 block"><?= esc($vendor['pic_name']) ?></span>
                                <span class="text-[10px] text-slate-500 font-mono"><?= esc($vendor['pic_phone']) ?></span>
                            </td>
                            <td class="p-4 text-slate-600"><?= esc($vendor['city'] ?? 'Jakarta') ?></td>
                            <td class="p-4">
                                <?php if ($vendor['is_verified']): ?>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        <span class="material-symbols-outlined text-[13px]">verified</span> Terverifikasi
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                        <span class="material-symbols-outlined text-[13px]">pending</span> Menunggu Audit
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="p-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button type="button" onclick="openFeeModal(<?= $vendor['id'] ?>, '<?= esc($vendor['company_name']) ?>', <?= (float)($vendor['commission_rate'] ?? 2.5) ?>)" 
                                            class="px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-purple-50 hover:bg-purple-100 text-purple-700 transition flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[14px]">tune</span>
                                        <span>Atur Fee</span>
                                    </button>
                                    <a href="<?= base_url('admin/vendors/toggle/' . $vendor['id']) ?>" 
                                       class="px-2.5 py-1.5 rounded-lg text-xs font-semibold transition <?= $vendor['is_verified'] ? 'bg-rose-50 text-rose-700 hover:bg-rose-100' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' ?>">
                                        <?= $vendor['is_verified'] ? 'Cabut' : 'Verifikasi' ?>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Ubah Fee Kesepakatan Vendor -->
<div id="feeModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full shadow-2xl border border-slate-200 overflow-hidden text-xs animate-in fade-in zoom-in-95 duration-150">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-gradient-to-r from-purple-50 to-indigo-50">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-purple-600">handshake</span>
                <h3 class="font-bold text-slate-900 text-sm">Ubah Tarif Fee Kesepakatan Vendor</h3>
            </div>
            <button type="button" onclick="closeFeeModal()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>

        <form action="<?= base_url('admin/vendors/update-fee') ?>" method="POST" class="p-6 space-y-4">
            <input type="hidden" name="vendor_id" id="modal_vendor_id">
            
            <div>
                <span class="text-slate-400 text-[11px] block">Nama Mitra Vendor:</span>
                <strong id="modal_vendor_name" class="text-slate-900 text-sm font-bold block pt-0.5"></strong>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Tarif Fee Komisi Kesepakatan Kontrak (%):</label>
                <div class="flex items-center gap-2">
                    <input type="number" step="0.1" min="0" max="100" name="commission_rate" id="modal_commission_rate" required
                           class="w-full px-4 py-2.5 rounded-xl border border-purple-300 focus:border-purple-600 font-black text-lg text-purple-900 text-center">
                    <span class="text-base font-bold text-purple-700">%</span>
                </div>
                <p class="text-[10px] text-slate-400 mt-1">Tarif dinegosiasikan per vendor sesuai jenis industri & volume pengadaan.</p>
            </div>

            <div class="p-3 bg-purple-50/70 rounded-xl border border-purple-200">
                <label class="flex items-start gap-2 cursor-pointer">
                    <input type="checkbox" name="sync_products" value="1" checked class="mt-0.5 text-purple-600 rounded">
                    <div>
                        <strong class="text-purple-950 font-bold block">Sinkronkan ke Seluruh Produk Aktif Vendor Ini</strong>
                        <span class="text-[10px] text-purple-700">Perbarui tarif fee pada semua produk yang saat ini dijual oleh vendor ini.</span>
                    </div>
                </label>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="closeFeeModal()" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-600 hover:bg-slate-50 font-semibold">
                    Batal
                </button>
                <button type="submit" class="btn-akuada-primary px-5 py-2 text-xs">
                    Simpan Perubahan Fee
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openFeeModal(vendorId, vendorName, currentFee) {
    document.getElementById('modal_vendor_id').value = vendorId;
    document.getElementById('modal_vendor_name').textContent = vendorName;
    document.getElementById('modal_commission_rate').value = currentFee;
    document.getElementById('feeModal').classList.remove('hidden');
}

function closeFeeModal() {
    document.getElementById('feeModal').classList.add('hidden');
}
</script>

<?= $this->endSection() ?>
