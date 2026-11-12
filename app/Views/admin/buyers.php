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
            <p class="text-xs text-slate-500 mt-0.5">Daftarkan akun buyer baru, atur limit fasilitas tempo (TOP), pantau saldo kredit, dan setujui pengajuan limit kredit buyer.</p>
        </div>
        <a href="<?= base_url('admin/buyers/create') ?>" class="btn-akuada-primary px-4 py-2.5 text-xs shadow-md flex items-center gap-2 self-start sm:self-auto">
            <span class="material-symbols-outlined text-[18px]">add_business</span>
            <span>+ Daftarkan Buyer Baru & Plafon TOP</span>
        </a>
    </div>

    <!-- Info Banner Card -->
    <div class="p-4 rounded-2xl bg-gradient-to-r from-purple-50 via-blue-50 to-indigo-50 border border-purple-200/80 text-xs text-purple-950 flex items-start gap-3 shadow-sm">
        <span class="material-symbols-outlined text-purple-600 text-[22px] flex-shrink-0 mt-0.5">account_balance_wallet</span>
        <div class="space-y-0.5">
            <strong class="font-bold text-slate-900 block">Kebijakan Fasilitas Kredit Buyer AkuAda:</strong>
            <p class="text-slate-600 leading-relaxed">
                Setiap akun buyer didaftarkan oleh AkuAda dengan fasilitas plafon kredit Term of Payment (TOP). Buyer dapat memesan barang di katalog web hingga batas sisa plafon, dan pembayaran dilakukan secara tempo setelah barang diterima dan BAST disahkan. Buyer yang belum terdaftar tidak memiliki fasilitas TOP dan wajib menggunakan Bank Transfer VA.
            </p>
        </div>
    </div>

    <!-- Buyers Table Card -->
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
                        <th class="px-3 py-3.5 w-16 text-center">ID</th>
                        <th class="px-4 py-3.5">Perusahaan Pembeli & Legalitas</th>
                        <th class="px-4 py-3.5">Kontak PIC Procurement</th>
                        <th class="px-4 py-3.5 text-right w-44">Batas Nota Belum Lunas</th>
                        <th class="px-4 py-3.5 text-right w-48">Cashback & Persentase</th>
                        <th class="px-4 py-3.5 text-center w-52">Aksi Manajemen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    <?php if (empty($buyers)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
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
                            $hasPendingIncrease = !empty($creditRequests[$b['id']]);
                            $reqData = $hasPendingIncrease ? $creditRequests[$b['id']] : null;
                        ?>
                        <tr class="hover:bg-slate-50/80 transition-colors <?= $hasPendingIncrease ? 'bg-amber-50/30' : '' ?>">
                            <!-- ID Buyer -->
                            <td class="px-3 py-3.5 align-top text-center font-mono font-bold text-slate-700">
                                <span class="px-2 py-1 rounded-lg bg-slate-100 text-slate-900 border border-slate-200 block text-xs">
                                    #<?= esc($b['id']) ?>
                                </span>
                            </td>

                            <!-- Company & Legality -->
                            <td class="px-4 py-3.5 align-top">
                                <div class="flex items-start gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-pink-100 text-pink-700 font-black flex items-center justify-center text-sm shadow-sm flex-shrink-0 mt-0.5">
                                        <?= strtoupper(substr($b['company_name'], 0, 1)) ?>
                                    </div>
                                    <div class="space-y-0.5">
                                        <div class="font-bold text-slate-900 leading-snug break-words">
                                            <?= esc($b['company_name']) ?>
                                            <span class="ml-1 text-[10px] font-mono font-bold px-1.5 py-0.2 rounded bg-pink-50 text-pink-700 border border-pink-200">ID: <?= esc($b['id']) ?></span>
                                        </div>
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

                                        <?php
                                            $docCount = (!empty($b['npwp_file']) ? 1 : 0) + (!empty($b['nib_file']) ? 1 : 0) + (!empty($b['deed_file']) ? 1 : 0) + (!empty($b['id_card_file']) ? 1 : 0) + (!empty($b['sppkp_file']) ? 1 : 0);
                                        ?>
                                        <div class="pt-1 flex flex-wrap items-center gap-1">
                                            <button type="button" 
                                                    onclick="openDocsModal('<?= esc($b['company_name']) ?>', <?= $b['is_verified'] ? 'true' : 'false' ?>, '<?= base_url('admin/buyers/toggle/' . $b['id']) ?>', '<?= esc($b['npwp_file'] ?? '') ?>', '<?= esc($b['nib_file'] ?? '') ?>', '<?= esc($b['deed_file'] ?? '') ?>', '<?= esc($b['id_card_file'] ?? '') ?>', '<?= esc($b['sppkp_file'] ?? '') ?>', '<?= esc($b['business_field'] ?? '-') ?>', '<?= esc($b['city'] ?? '-') ?>', '<?= esc($b['province'] ?? '-') ?>', '<?= esc(addslashes($b['address'] ?? '-')) ?>')"
                                                    class="px-2 py-0.5 rounded-lg <?= $docCount > 0 ? 'bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200' : 'bg-slate-100 text-slate-500' ?> text-[10px] font-bold inline-flex items-center gap-1 transition">
                                                <span class="material-symbols-outlined text-[13px]">folder_open</span>
                                                <span>Berkas Legalitas (<?= $docCount ?> Dokumen)</span>
                                            </button>
                                        </div>

                                        <?php if ($hasPendingIncrease): ?>
                                            <div class="mt-1.5 p-2 rounded-xl bg-amber-100 border border-amber-300 text-amber-900">
                                                <span class="font-extrabold text-[10px] uppercase block flex items-center gap-1">
                                                    <span class="material-symbols-outlined text-[13px]">notification_important</span>
                                                    Permintaan Kenaikan Plafon Baru:
                                                </span>
                                                <strong class="font-mono text-purple-950 block"><?= format_rupiah($reqData['requested_limit'] ?? 0) ?></strong>
                                                <span class="text-[10px] text-slate-600 italic block mt-0.5">"<?= esc($reqData['notes'] ?? '-') ?>"</span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>

                            <!-- PIC -->
                            <td class="px-4 py-3.5 align-top">
                                <div class="font-bold text-slate-800 break-words"><?= esc($b['pic_name']) ?></div>
                                <div class="text-[11px] text-slate-500 font-mono mt-0.5"><?= esc($b['pic_phone'] ?? '-') ?></div>
                                <div class="text-[11px] text-slate-400 break-words"><?= esc($b['email']) ?></div>
                            </td>

                            <!-- Batas Nota Belum Lunas (Batas Kredit Pengganti Plafon) -->
                            <?php
                                $bUnpaid = get_buyer_unpaid_notes_count((int)$b['id']);
                                $bMaxNotes = get_max_unpaid_notes_limit();
                                $bQuotaOk = ($bUnpaid < $bMaxNotes);
                            ?>
                            <td class="px-4 py-3.5 align-top text-right font-mono">
                                <div class="font-extrabold text-sm <?= $bQuotaOk ? 'text-emerald-700' : 'text-rose-700' ?>">
                                    <?= $bUnpaid ?> / <?= $bMaxNotes ?> Nota
                                </div>
                                <div class="text-[10px] <?= $bQuotaOk ? 'text-emerald-600' : 'text-rose-600' ?> font-semibold mt-0.5">
                                    <?= $bQuotaOk ? 'Sisa ' . ($bMaxNotes - $bUnpaid) . ' nota bulan ini' : 'Batas 3 nota tercapai' ?>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-1.5 mt-1.5 overflow-hidden">
                                    <div class="<?= $bQuotaOk ? 'bg-purple-600' : 'bg-rose-600' ?> h-1.5 rounded-full" style="width: <?= min(100, round(($bUnpaid / $bMaxNotes) * 100)) ?>%"></div>
                                </div>
                            </td>

                            <!-- Saldo Cashback & Persentase Rate -->
                            <?php
                                $buyerRate = get_buyer_cashback_rate((int)$b['id']);
                                $isCustomRate = !empty($b['cashback_rate']) && (float)$b['cashback_rate'] > 0;
                            ?>
                            <td class="px-4 py-3.5 align-top text-right font-mono">
                                <div class="font-black text-purple-900 text-sm"><?= format_rupiah($b['cashback_balance'] ?? 0) ?></div>
                                <div class="flex items-center justify-end gap-1 mt-1 font-sans">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full <?= $isCustomRate ? 'bg-purple-100 text-purple-800 border border-purple-200' : 'bg-slate-100 text-slate-700' ?> text-[10px] font-bold">
                                        <span class="material-symbols-outlined text-[12px] text-purple-600">redeem</span>
                                        <?= esc(number_format($buyerRate, 2)) ?>%
                                        <?php if ($isCustomRate): ?>
                                            <span class="text-[9px] text-purple-600 font-semibold">(Khusus)</span>
                                        <?php else: ?>
                                            <span class="text-[9px] text-slate-500 font-normal">(Default)</span>
                                        <?php endif; ?>
                                    </span>
                                </div>
                            </td>

                            <!-- Actions -->
                            <td class="px-4 py-3.5 align-top text-center">
                                <div class="grid grid-cols-2 gap-1.5">
                                    <!-- Edit Profil -->
                                    <a href="<?= base_url('admin/buyers/edit/' . $b['id']) ?>" 
                                       class="px-2.5 py-1.5 rounded-lg text-xs font-bold bg-purple-50 hover:bg-purple-100 text-purple-700 transition inline-flex items-center justify-center gap-1 border border-purple-200"
                                       title="Edit Data Lengkap Buyer">
                                        <span class="material-symbols-outlined text-[15px]">edit</span>
                                        <span>Edit</span>
                                    </a>

                                    <!-- Atur Cashback Modal -->
                                    <button type="button" 
                                            onclick="openCashbackModal(<?= $b['id'] ?>, '<?= esc($b['company_name']) ?>', '<?= esc($b['cashback_rate'] ?? '') ?>', <?= esc(get_buyer_cashback_rate()) ?>)"
                                            class="px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-slate-50 hover:bg-slate-100 text-slate-700 transition inline-flex items-center justify-center gap-1 border border-slate-200"
                                            title="Atur persentase cashback">
                                        <span class="material-symbols-outlined text-[15px]">percent</span>
                                        <span>Cashback</span>
                                    </button>

                                    <!-- Toggle Verifikasi -->
                                    <a href="<?= base_url('admin/buyers/toggle/' . $b['id']) ?>" 
                                       data-confirm="Ubah status verifikasi untuk <?= esc($b['company_name']) ?>?"
                                       class="px-2.5 py-1.5 rounded-lg text-xs font-semibold transition text-center inline-flex items-center justify-center gap-0.5 <?= $b['is_verified'] ? 'bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200' ?>"
                                       title="<?= $b['is_verified'] ? 'Cabut verifikasi akun buyer' : 'Verifikasi legalitas akun buyer' ?>">
                                        <span class="material-symbols-outlined text-[14px]"><?= $b['is_verified'] ? 'block' : 'verified' ?></span>
                                        <span><?= $b['is_verified'] ? 'Cabut' : 'Verif' ?></span>
                                    </a>

                                    <!-- Hapus Buyer -->
                                    <a href="<?= base_url('admin/buyers/delete/' . $b['id']) ?>" 
                                       data-confirm="Apakah Anda yakin ingin MENGHAPUS PERMANEN buyer <?= esc($b['company_name']) ?>? Seluruh akun login dan berkasnya akan dihapus!"
                                       class="px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-rose-50 hover:bg-rose-100 text-rose-700 transition inline-flex items-center justify-center gap-0.5 border border-rose-200"
                                       title="Hapus Buyer Permanen">
                                        <span class="material-symbols-outlined text-[15px]">delete</span>
                                        <span>Hapus</span>
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

<!-- Modal Ubah Persentase Cashback Buyer -->
<div id="cashbackModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full shadow-2xl border border-slate-200 overflow-hidden text-xs animate-in fade-in zoom-in-95 duration-150">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-gradient-to-r from-purple-50 to-pink-50">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-purple-600">savings</span>
                <h3 class="font-bold text-slate-900 text-sm">Pengaturan Persentase Cashback Buyer</h3>
            </div>
            <button type="button" onclick="closeCashbackModal()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>

        <form action="<?= base_url('admin/buyers/update-cashback') ?>" method="POST" class="p-6 space-y-4">
            <?= csrf_field() ?>
            <input type="hidden" name="company_id" id="modal_cashback_buyer_id">
            
            <div>
                <span class="text-slate-400 text-[11px] block">Perusahaan Pembeli:</span>
                <strong id="modal_cashback_buyer_name" class="text-slate-900 text-sm font-bold block pt-0.5"></strong>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Persentase Fee Cashback (%):</label>
                <div class="relative">
                    <input type="number" name="cashback_rate" id="modal_cashback_rate_value" step="0.01" min="0" max="100" placeholder="Biarkan kosong untuk ikut default sistem"
                           class="w-full pr-10 pl-4 py-2.5 rounded-xl border border-purple-300 focus:border-purple-600 font-mono font-bold text-base text-purple-900">
                    <span class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 font-bold">%</span>
                </div>
                <p class="text-[11px] text-slate-500 mt-1.5 leading-relaxed">
                    Setiap transaksi belanja yang lunas oleh buyer ini akan mendapatkan cashback saldo sebesar persentase ini. Default sistem saat ini: <strong id="modal_cashback_default_hint" class="text-purple-700 font-bold">1.0%</strong>. Kosongkan nilai jika ingin mengikuti setting global sistem.
                </p>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="closeCashbackModal()" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-600 hover:bg-slate-50 font-semibold">
                    Batal
                </button>
                <button type="submit" class="btn-akuada-primary px-5 py-2 text-xs">
                    Simpan Cashback
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Berkas Legalitas & Kepatuhan Buyer -->
<div id="docsModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-2xl w-full shadow-2xl border border-slate-200 overflow-hidden text-xs animate-in fade-in zoom-in-95 duration-150">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-gradient-to-r from-purple-50 via-indigo-50 to-blue-50">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-purple-600 text-2xl">verified_user</span>
                <div>
                    <h3 class="font-extrabold text-slate-900 text-sm">Berkas Legalitas & Kepatuhan Usaha Buyer</h3>
                    <p class="text-[11px] text-slate-500" id="docsModalCompanyName">-</p>
                </div>
            </div>
            <button type="button" onclick="closeDocsModal()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>

        <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">
            <!-- Profil Singkat -->
            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 grid grid-cols-2 gap-2 text-[11px]">
                <div>
                    <span class="text-slate-400 block text-[10px]">Bidang / Sektor Usaha:</span>
                    <strong class="text-slate-800" id="docsModalSector">-</strong>
                </div>
                <div>
                    <span class="text-slate-400 block text-[10px]">Kota & Provinsi Domisili:</span>
                    <strong class="text-slate-800" id="docsModalLocation">-</strong>
                </div>
                <div class="col-span-2">
                    <span class="text-slate-400 block text-[10px]">Alamat Gudang / Kantor:</span>
                    <span class="text-slate-700" id="docsModalAddress">-</span>
                </div>
            </div>

            <!-- List 5 Dokumen -->
            <div class="space-y-2.5">
                <h4 class="font-bold text-slate-800 text-xs flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-purple-600 text-[16px]">folder</span>
                    <span>Kelengkapan Berkas Dokumen Diunggah:</span>
                </h4>

                <!-- 1. NPWP -->
                <div class="p-3 rounded-xl border border-slate-200 flex items-center justify-between gap-3 hover:bg-slate-50/70 transition">
                    <div class="flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-purple-600 text-xl">credit_card</span>
                        <div>
                            <div class="font-bold text-slate-900">1. Kartu NPWP Perusahaan / SKT Pajak</div>
                            <span class="text-[10px] text-slate-400">Validasi e-Faktur Pajak PPN & identitas wajib pajak</span>
                        </div>
                    </div>
                    <div id="docsNpwpBtn"></div>
                </div>

                <!-- 2. NIB -->
                <div class="p-3 rounded-xl border border-slate-200 flex items-center justify-between gap-3 hover:bg-slate-50/70 transition">
                    <div class="flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-blue-600 text-xl">license</span>
                        <div>
                            <div class="font-bold text-slate-900">2. Nomor Induk Berusaha (NIB OSS)</div>
                            <span class="text-[10px] text-slate-400">Izin berusaha resmi Kementerian Investasi/BKPM</span>
                        </div>
                    </div>
                    <div id="docsNibBtn"></div>
                </div>

                <!-- 3. Akta -->
                <div class="p-3 rounded-xl border border-slate-200 flex items-center justify-between gap-3 hover:bg-slate-50/70 transition">
                    <div class="flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-amber-600 text-xl">gavel</span>
                        <div>
                            <div class="font-bold text-slate-900">3. Akta Pendirian / SK Kemenkumham</div>
                            <span class="text-[10px] text-slate-400">Bukti keabsahan badan hukum & susunan direksi</span>
                        </div>
                    </div>
                    <div id="docsDeedBtn"></div>
                </div>

                <!-- 4. KTP PIC -->
                <div class="p-3 rounded-xl border border-slate-200 flex items-center justify-between gap-3 hover:bg-slate-50/70 transition">
                    <div class="flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-emerald-600 text-xl">badge</span>
                        <div>
                            <div class="font-bold text-slate-900">4. Foto / Scan KTP PIC Pengadaan</div>
                            <span class="text-[10px] text-slate-400">Verifikasi identitas penanggung jawab pemesanan</span>
                        </div>
                    </div>
                    <div id="docsIdCardBtn"></div>
                </div>

                <!-- 5. SPPKP -->
                <div class="p-3 rounded-xl border border-slate-200 flex items-center justify-between gap-3 hover:bg-slate-50/70 transition">
                    <div class="flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-slate-500 text-xl">receipt_long</span>
                        <div>
                            <div class="font-bold text-slate-900">5. Surat Pengukuhan PKP (SPPKP)</div>
                            <span class="text-[10px] text-slate-400">Khusus pengkreditan Faktur Pajak PPN Masukan</span>
                        </div>
                    </div>
                    <div id="docsSppkpBtn"></div>
                </div>
            </div>

            <!-- Footer Action in Modal -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                <span id="docsModalStatusBadge"></span>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="closeDocsModal()" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-600 hover:bg-slate-50 font-semibold">
                        Tutup
                    </button>
                    <a id="docsModalToggleLink" href="#" data-confirm="Ubah status verifikasi legalitas perusahaan ini?" class="px-4 py-2 rounded-xl font-bold text-white transition">
                        -
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function openCashbackModal(companyId, companyName, currentRate, defaultRate) {
    document.getElementById('modal_cashback_buyer_id').value = companyId;
    document.getElementById('modal_cashback_buyer_name').textContent = companyName;
    document.getElementById('modal_cashback_rate_value').value = currentRate !== '' ? currentRate : '';
    document.getElementById('modal_cashback_default_hint').textContent = defaultRate + '%';
    document.getElementById('cashbackModal').classList.remove('hidden');
}

function closeCashbackModal() {
    document.getElementById('cashbackModal').classList.add('hidden');
}

function renderDocBtn(filePath, label) {
    if (filePath && filePath.trim() !== '') {
        const fullUrl = '<?= base_url() ?>/' + filePath.replace(/^\//, '');
        return `<a href="${fullUrl}" target="_blank" class="px-3 py-1.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-[11px] inline-flex items-center gap-1 shadow-xs transition">
            <span class="material-symbols-outlined text-[14px]">open_in_new</span>
            <span>Lihat ${label}</span>
        </a>`;
    }
    return `<span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-400 font-medium text-[10px] inline-flex items-center gap-1">
        <span class="material-symbols-outlined text-[13px]">cancel</span> Belum Diunggah
    </span>`;
}

function openDocsModal(companyName, isVerified, toggleUrl, npwpFile, nibFile, deedFile, idCardFile, sppkpFile, sector, city, province, address) {
    document.getElementById('docsModalCompanyName').textContent = companyName;
    document.getElementById('docsModalSector').textContent = sector || '-';
    document.getElementById('docsModalLocation').textContent = (city || '-') + ', ' + (province || '-');
    document.getElementById('docsModalAddress').textContent = address || '-';

    document.getElementById('docsNpwpBtn').innerHTML = renderDocBtn(npwpFile, 'NPWP');
    document.getElementById('docsNibBtn').innerHTML = renderDocBtn(nibFile, 'NIB');
    document.getElementById('docsDeedBtn').innerHTML = renderDocBtn(deedFile, 'Akta');
    document.getElementById('docsIdCardBtn').innerHTML = renderDocBtn(idCardFile, 'KTP');
    document.getElementById('docsSppkpBtn').innerHTML = renderDocBtn(sppkpFile, 'SPPKP');

    const toggleBtn = document.getElementById('docsModalToggleLink');
    toggleBtn.href = toggleUrl;

    const statusBadge = document.getElementById('docsModalStatusBadge');
    if (isVerified) {
        statusBadge.innerHTML = `<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 text-[11px] font-bold border border-emerald-200">
            <span class="material-symbols-outlined text-[14px]">verified</span> Legalitas Terverifikasi
        </span>`;
        toggleBtn.textContent = 'Cabut Verifikasi';
        toggleBtn.className = 'px-4 py-2 rounded-xl font-bold text-white bg-rose-600 hover:bg-rose-700 transition';
        toggleBtn.setAttribute('data-confirm', 'Apakah Anda yakin ingin menonaktifkan status verifikasi ' + companyName + '?');
    } else {
        statusBadge.innerHTML = `<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 text-[11px] font-bold border border-amber-200">
            <span class="material-symbols-outlined text-[14px]">hourglass_empty</span> Menunggu Verifikasi Dokumen
        </span>`;
        toggleBtn.textContent = 'Sahkan & Verifikasi Legalitas';
        toggleBtn.className = 'px-4 py-2 rounded-xl font-bold text-white bg-emerald-600 hover:bg-emerald-700 transition shadow-md shadow-emerald-600/20';
        toggleBtn.setAttribute('data-confirm', 'Apakah seluruh dokumen legalitas ' + companyName + ' sudah valid dan siap disahkan?');
    }

    document.getElementById('docsModal').classList.remove('hidden');
}

function closeDocsModal() {
    document.getElementById('docsModal').classList.add('hidden');
}
</script>

<?= $this->endSection() ?>
