<?= $this->extend('layouts/admin') ?>

<?= $this->section('admin_content') ?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900">Pengawasan Master Barang Pengadaan</h1>
            <p class="text-xs text-slate-500 mt-0.5">Daftar seluruh item barang yang didaftarkan oleh mitra vendor di platform AkuAda.</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase text-[10px]">
                    <tr>
                        <th class="p-4">Barang</th>
                        <th class="p-4">SKU / Brand</th>
                        <th class="p-4">Vendor Pemasok</th>
                        <th class="p-4">Kategori</th>
                        <th class="p-4">Harga B2B Satuan</th>
                        <th class="p-4">Komisi Platform Fee</th>
                        <th class="p-4">MOQ & UOM</th>
                        <th class="p-4">PPN 11%</th>
                        <th class="p-4">TKDN</th>
                        <th class="p-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($products as $p): ?>
                        <?php 
                            $feePct = (float)($p['vendor_fee_percent'] ?? get_default_vendor_fee_rate());
                            $feePerUnit = round((float)$p['price_regular'] * ($feePct / 100));
                        ?>
                        <tr class="hover:bg-slate-50 transition">
                            <td class="p-4">
                                <div class="flex items-center gap-3">
                                    <img src="<?= esc($p['image_url']) ?>" class="w-10 h-10 object-cover rounded-lg border border-slate-200">
                                    <span class="font-bold text-slate-900 line-clamp-1 max-w-xs"><?= esc($p['name']) ?></span>
                                </div>
                            </td>
                            <td class="p-4 font-mono font-semibold text-purple-700">
                                <?= esc($p['sku']) ?>
                                <span class="text-[10px] text-slate-400 block"><?= esc($p['brand']) ?></span>
                            </td>
                            <td class="p-4 text-slate-700"><?= esc($p['vendor_name']) ?></td>
                            <td class="p-4 text-slate-500"><?= esc($p['category_name']) ?></td>
                            <td class="p-4 font-bold text-slate-900"><?= format_rupiah($p['price_regular']) ?></td>
                            <td class="p-4">
                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-800 font-bold text-[10px] border border-emerald-200">
                                    <?= $feePct ?>% (<?= format_rupiah($feePerUnit) ?>)
                                </span>
                            </td>
                            <td class="p-4">
                                <span class="font-bold"><?= $p['moq'] ?></span> <?= esc($p['uom']) ?>
                            </td>
                            <td class="p-4">
                                <span class="badge-ppn text-[10px]">PPN 11%</span>
                            </td>
                            <td class="p-4 font-semibold text-purple-700"><?= $p['tkdn_percent'] > 0 ? $p['tkdn_percent'] . '%' : '-' ?></td>
                            <td class="p-4">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Aktif</span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
