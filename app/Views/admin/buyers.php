<?= $this->extend('layouts/admin') ?>

<?= $this->section('admin_content') ?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900">Manajemen Perusahaan Buyer & Plafon Kredit TOP</h1>
            <p class="text-xs text-slate-500 mt-0.5">Atur limit plafon kredit Term of Payment (TOP 14-90 Hari) untuk akun korporat terdaftar.</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase text-[10px]">
                    <tr>
                        <th class="p-4">Nama Perusahaan Pembeli</th>
                        <th class="p-4">NPWP & NIB</th>
                        <th class="p-4">PIC Procurement</th>
                        <th class="p-4">Plafon Kredit TOP</th>
                        <th class="p-4">Kredit Terpakai</th>
                        <th class="p-4">Sisa Plafon</th>
                        <th class="p-4 text-center">Ubah Plafon</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($buyers as $b): ?>
                        <?php 
                            $limit = (float)$b['credit_limit_top'];
                            $used = (float)$b['credit_used_top'];
                            $remaining = max(0, $limit - $used);
                        ?>
                        <tr class="hover:bg-slate-50 transition">
                            <td class="p-4">
                                <strong class="text-slate-900 block"><?= esc($b['company_name']) ?></strong>
                                <span class="text-[10px] text-purple-700 font-semibold"><?= esc($b['legal_type']) ?></span>
                            </td>
                            <td class="p-4 font-mono">
                                <span class="block">NPWP: <?= esc($b['npwp'] ?? '-') ?></span>
                                <span class="text-[10px] text-slate-400">NIB: <?= esc($b['nib'] ?? '-') ?></span>
                            </td>
                            <td class="p-4">
                                <strong class="text-slate-800 block"><?= esc($b['pic_name']) ?></strong>
                                <span class="text-[10px] text-slate-500"><?= esc($b['pic_phone']) ?></span>
                            </td>
                            <td class="p-4 font-extrabold text-purple-900"><?= format_rupiah($limit) ?></td>
                            <td class="p-4 font-bold text-rose-600"><?= format_rupiah($used) ?></td>
                            <td class="p-4 font-extrabold text-emerald-600"><?= format_rupiah($remaining) ?></td>
                            <td class="p-4 text-center">
                                <form action="<?= base_url('admin/buyers/limit') ?>" method="POST" class="inline-flex items-center gap-1.5">
                                    <input type="hidden" name="company_id" value="<?= $b['id'] ?>">
                                    <input type="number" name="credit_limit_top" value="<?= $limit ?>" step="10000000" 
                                           class="w-32 px-2 py-1 text-xs border border-slate-300 rounded-lg text-right font-bold">
                                    <button type="submit" class="px-2.5 py-1 rounded bg-purple-600 hover:bg-purple-700 text-white font-bold transition">
                                        Simpan
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
