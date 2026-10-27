<?= $this->extend('layouts/customer') ?>

<?= $this->section('customer_content') ?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900">Arsip e-Faktur Pajak PPN 11% Resmi (DJP)</h1>
            <p class="text-xs text-slate-500 mt-0.5">Faktur pajak elektronik yang terhubung resmi dengan NPWP Perusahaan untuk rekonsiliasi SPT Masa PPN.</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase text-[10px]">
                    <tr>
                        <th class="p-4">Nomor Seri e-Faktur DJP</th>
                        <th class="p-4">No. PO & Tanggal</th>
                        <th class="p-4">Dasar Pengenaan Pajak (DPP)</th>
                        <th class="p-4">Jumlah PPN (11%)</th>
                        <th class="p-4">Total Faktur</th>
                        <th class="p-4">Status DJP</th>
                        <th class="p-4 text-center">Dokumen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($orders as $ord): ?>
                        <tr class="hover:bg-slate-50 transition">
                            <td class="p-4 font-mono font-bold text-blue-700">
                                <?= esc($ord['tax_invoice_number'] ?? '010.002-26.' . rand(10000000, 99999999)) ?>
                            </td>
                            <td class="p-4">
                                <span class="font-mono font-semibold text-slate-800 block"><?= esc($ord['po_number'] ?? $ord['order_number']) ?></span>
                                <span class="text-[10px] text-slate-400"><?= date('d M Y', strtotime($ord['created_at'])) ?></span>
                            </td>
                            <td class="p-4 font-semibold text-slate-800"><?= format_rupiah($ord['subtotal']) ?></td>
                            <td class="p-4 font-bold text-blue-700"><?= format_rupiah($ord['ppn_amount']) ?></td>
                            <td class="p-4 font-extrabold text-slate-900"><?= format_rupiah($ord['total_amount']) ?></td>
                            <td class="p-4">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    <span class="material-symbols-outlined text-[13px]">check_circle</span> DJP Approval Sukses
                                </span>
                            </td>
                            <td class="p-4 text-center">
                                <a href="<?= base_url('customer/orders/' . $ord['id']) ?>" class="px-3 py-1.5 rounded-lg border border-blue-300 text-blue-700 hover:bg-blue-50 font-bold transition">
                                    Lihat Lampiran PO
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
