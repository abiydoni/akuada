<?= $this->extend('layouts/customer') ?>

<?= $this->section('customer_content') ?>

<div class="space-y-6">
    <!-- Header with Breadcrumbs & Actions -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="<?= base_url('customer/orders') ?>" class="hover:text-pink-600">Riwayat Purchase Order</a>
                <span>/</span>
                <span class="font-mono text-purple-700 font-bold"><?= esc($order['po_number'] ?? $order['order_number']) ?></span>
            </div>
            <h1 class="text-xl font-extrabold text-slate-900">Status Pemrosesan & Pelacakan PO</h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="<?= base_url('customer/orders/invoice/' . $order['id']) ?>" target="_blank" class="px-4 py-2 rounded-xl bg-purple-100 hover:bg-purple-200 text-purple-900 font-extrabold text-xs transition flex items-center gap-1.5 shadow-sm border border-purple-200">
                <span class="material-symbols-outlined text-[18px] text-purple-700">receipt_long</span>
                <span>Faktur / Invoice AkuAda</span>
            </a>
            <button type="button" onclick="window.print()" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition flex items-center gap-1.5 shadow-sm">
                <span class="material-symbols-outlined text-[16px]">print</span>
                <span>Cetak Lembar PO</span>
            </button>
            <?php if ($order['order_status'] !== 'bast_signed' && $order['order_status'] !== 'completed'): ?>
                <button type="button" onclick="confirmSignBast()" class="btn-akuada-primary text-xs px-4 py-2">
                    <span class="material-symbols-outlined text-[18px]">verified</span>
                    <span>Sahkan BAST Resmi</span>
                </button>
            <?php else: ?>
                <span class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-100 text-emerald-800 font-bold text-xs border border-emerald-300">
                    <span class="material-symbols-outlined text-[18px]">check_circle</span>
                    <span>BAST Telah Disahkan</span>
                </span>
            <?php endif; ?>
        </div>
    </div>

    <!-- 1. Real-Time Order Progress Stepper (Timeline Tahapan Pemesanan) -->
    <div class="bg-white rounded-3xl border border-purple-200 shadow-md p-6 sm:p-8 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-purple-600">route</span>
                <h3 class="text-sm font-bold text-slate-900">Status & Tahapan Pemrosesan Pengadaan (Lifecycle Timeline)</h3>
            </div>
            <div>
                <?= badge_order_status($order['order_status']) ?>
            </div>
        </div>

        <!-- Stepper Component -->
        <?= render_order_progress_stepper($order['order_status']) ?>

        <!-- Historical Activity Log -->
        <?php if (!empty($order['status_logs'])): ?>
            <div class="mt-6 pt-5 border-t border-slate-100 space-y-3">
                <span class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-pink-600 text-[18px]">history</span>
                    Riwayat Jejak Pelacakan Pesanan:
                </span>
                
                <div class="relative pl-6 space-y-4 before:content-[''] before:absolute before:left-2.5 before:top-2 before:bottom-2 before:w-0.5 before:bg-gradient-to-b before:from-purple-600 before:via-blue-500 before:to-pink-500">
                    <?php foreach ($order['status_logs'] as $log): ?>
                        <div class="relative flex items-start gap-3 text-xs">
                            <div class="absolute -left-6 top-1 w-5 h-5 rounded-full bg-white border-2 border-purple-600 flex items-center justify-center">
                                <div class="w-2 h-2 rounded-full bg-pink-500"></div>
                            </div>
                            <div class="bg-slate-50 p-3 rounded-xl border border-slate-200 w-full space-y-1">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <strong class="font-bold text-slate-900 text-xs"><?= esc($log['title']) ?></strong>
                                    <span class="text-[10px] text-slate-400 font-mono"><?= date('d M Y, H:i WIB', strtotime($log['created_at'])) ?></span>
                                </div>
                                <?php if (!empty($log['description'])): ?>
                                    <p class="text-slate-600 text-[11px] leading-relaxed"><?= esc($log['description']) ?></p>
                                <?php endif; ?>
                                <span class="text-[10px] text-purple-700 font-semibold block pt-0.5">Oleh: <?= esc($log['updated_by']) ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Invoice Notice Card -->
    <div class="p-4 sm:p-5 rounded-2xl bg-gradient-to-r from-purple-50 via-blue-50 to-pink-50 border border-purple-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex items-start gap-3">
            <div class="w-10 h-10 rounded-xl bg-purple-600 text-white flex items-center justify-center flex-shrink-0 mt-0.5">
                <span class="material-symbols-outlined text-[20px]">receipt_long</span>
            </div>
            <div>
                <strong class="text-slate-900 font-bold text-xs block">Penerbitan Faktur Penjualan Resmi: PT Aku Ada Indonesia</strong>
                <p class="text-slate-600 text-[11px] mt-0.5">
                    Sebagai hotel / perusahaan pembeli, Anda menerima invoice resmi langsung dari <strong>PT Aku Ada Indonesia</strong> (No. <span class="font-mono font-bold text-purple-700"><?= esc($order['invoice_number'] ?? ('INV/AKUADA/' . date('Ym') . '/' . str_pad($order['id'], 4, '0', STR_PAD_LEFT))) ?></span>) untuk keperluan pembukuan AP & pelunasan TOP.
                </p>
            </div>
        </div>
        <a href="<?= base_url('customer/orders/invoice/' . $order['id']) ?>" target="_blank" class="px-4 py-2 rounded-xl bg-gradient-to-r from-purple-700 to-indigo-700 hover:from-purple-800 hover:to-indigo-800 text-white font-extrabold text-xs transition flex items-center gap-1.5 shadow-md shadow-purple-500/20 flex-shrink-0">
            <span class="material-symbols-outlined text-[16px]">visibility</span>
            <span>Buka Lembar Invoice</span>
        </a>
    </div>

    <!-- 2. Official PO Document Card -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xl p-8 sm:p-10 space-y-8 print:p-0 print:border-none print:shadow-none">
        
        <!-- Document Top Bar: Logo, PO Number, Status -->
        <div class="flex flex-col sm:flex-row justify-between items-start gap-4 pb-6 border-b-2 border-slate-900">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-purple-700 via-blue-600 to-pink-500 flex items-center justify-center text-white font-bold text-xl">
                        A
                    </div>
                    <div>
                        <span class="text-xl font-black text-slate-900 tracking-tight">AkuAda<span class="text-pink-500">.</span></span>
                        <span class="block text-[10px] uppercase font-bold text-purple-700 tracking-wider">Official E-Procurement B2B</span>
                    </div>
                </div>
                <p class="text-[11px] text-slate-500 max-w-sm pt-1">
                    Gedung AkuAda Procurement Tower Lt. 18, Jakarta Selatan | Telp: (021) 5088-9900
                </p>
            </div>

            <div class="text-left sm:text-right space-y-1">
                <span class="text-xs uppercase font-extrabold tracking-widest text-slate-400 block">Surat Pesanan Resmi</span>
                <h2 class="text-lg sm:text-xl font-black text-purple-950 font-mono"><?= esc($order['po_number'] ?? $order['order_number']) ?></h2>
                <div class="pt-1 flex sm:justify-end gap-2">
                    <?= badge_order_status($order['order_status']) ?>
                    <?= badge_payment_method($order['payment_method']) ?>
                </div>
            </div>
        </div>

        <!-- 2 Columns: Buyer & Vendor Corporate Data -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 text-xs">
            <div class="p-4 rounded-2xl bg-purple-50/50 border border-purple-100 space-y-1.5">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-purple-700 block">Diterbitkan Oleh (Corporate Buyer):</span>
                <h4 class="font-bold text-slate-900 text-sm"><?= esc($order['buyer_name']) ?></h4>
                <p class="text-slate-600">NPWP Badan: <strong class="font-mono"><?= esc($order['buyer_npwp'] ?? '-') ?></strong></p>
                <p class="text-slate-600">NIB: <strong class="font-mono"><?= esc($order['buyer_nib'] ?? '-') ?></strong></p>
                <p class="text-slate-600">Alamat Kirim: <?= esc($order['shipping_address']) ?></p>
                <p class="text-slate-600">PIC: <?= esc($order['buyer_pic']) ?> (<?= esc($order['buyer_phone']) ?>)</p>
            </div>

            <div class="p-4 rounded-2xl bg-blue-50/50 border border-blue-100 space-y-1.5">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-blue-700 block">Ditujukan Kepada (Mitra Vendor):</span>
                <h4 class="font-bold text-slate-900 text-sm"><?= esc($order['vendor_name']) ?></h4>
                <p class="text-slate-600">NPWP Vendor: <strong class="font-mono"><?= esc($order['vendor_npwp'] ?? '-') ?></strong></p>
                <p class="text-slate-600">Alamat: <?= esc($order['vendor_address'] ?? 'Jakarta Timur') ?></p>
                <p class="text-slate-600">No. Kontak: <?= esc($order['vendor_phone'] ?? '-') ?></p>
                <p class="text-blue-700 font-semibold pt-1">No. e-Faktur DJP: <span class="font-mono font-bold"><?= esc($order['tax_invoice_number'] ?? '-') ?></span></p>
            </div>
        </div>

        <!-- Items Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-900 text-white font-bold uppercase text-[10px]">
                    <tr>
                        <th class="p-3">No</th>
                        <th class="p-3">Uraian Kebutuhan Barang</th>
                        <th class="p-3">SKU</th>
                        <th class="p-3">Kuantitas</th>
                        <th class="p-3">Harga Satuan B2B</th>
                        <th class="p-3">PPN 11%</th>
                        <th class="p-3 text-right">Subtotal Nilai</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <?php foreach ($order['items'] as $idx => $it): ?>
                        <tr>
                            <td class="p-3 text-slate-400 font-mono"><?= $idx + 1 ?></td>
                            <td class="p-3">
                                <strong class="text-slate-900 block"><?= esc($it['product_name']) ?></strong>
                            </td>
                            <td class="p-3 font-mono text-slate-500"><?= esc($it['sku']) ?></td>
                            <td class="p-3 font-bold"><?= $it['qty'] ?> <?= esc($it['uom']) ?></td>
                            <td class="p-3"><?= format_rupiah($it['unit_price']) ?></td>
                            <td class="p-3 font-semibold text-blue-700"><?= format_rupiah($it['ppn_amount']) ?></td>
                            <td class="p-3 text-right font-extrabold text-slate-900"><?= format_rupiah($it['subtotal']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="border-t-2 border-slate-900 text-xs font-bold divide-y divide-slate-100">
                    <tr>
                        <td colspan="6" class="p-3 text-right text-slate-600">Subtotal Pengadaan Barang:</td>
                        <td class="p-3 text-right text-slate-900"><?= format_rupiah($order['subtotal']) ?></td>
                    </tr>
                    <tr>
                        <td colspan="6" class="p-3 text-right text-blue-700">Pajak Pertambahan Nilai (PPN 11%):</td>
                        <td class="p-3 text-right text-blue-700"><?= format_rupiah($order['ppn_amount']) ?></td>
                    </tr>
                    <tr class="text-sm bg-purple-50/50">
                        <td colspan="6" class="p-3 text-right text-purple-950 font-black">TOTAL NILAI PURCHASE ORDER:</td>
                        <td class="p-3 text-right text-purple-950 font-black text-base"><?= format_rupiah($order['total_amount']) ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- B2B Terms & Signatures -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-4 border-t border-slate-100 text-xs">
            <div class="space-y-2">
                <span class="font-bold text-slate-800 uppercase tracking-wider text-[11px] block">Ketentuan Term of Payment (TOP):</span>
                <ul class="list-disc list-inside text-slate-600 space-y-1">
                    <li>Pembayaran mengacu nomor PO resmi: <strong><?= esc($order['po_number'] ?? $order['order_number']) ?></strong>.</li>
                    <li>Jatuh tempo pelunasan: <strong><?= $order['due_date'] ? date('d F Y', strtotime($order['due_date'])) : '30 Hari setelah BAST' ?></strong>.</li>
                    <li>Nomor Surat Jalan / BAST: <strong><?= esc($order['bast_number'] ?? 'Diterbitkan saat pengiriman') ?></strong>.</li>
                    <li>Nomor Faktur Pajak e-Faktur: <strong><?= esc($order['tax_invoice_number'] ?? 'Terlampir') ?></strong>.</li>
                    <li>Layanan Platform E-Procurement: <strong>Sudah termasuk seluruh biaya operasional & jaminan kepatuhan AkuAda.</strong></li>
                </ul>
            </div>

            <!-- Signature Columns -->
            <div class="grid grid-cols-2 gap-4 text-center">
                <div class="border border-slate-200 p-4 rounded-xl space-y-8">
                    <span class="text-[10px] text-slate-400 uppercase font-bold block">Penyedia (Vendor)</span>
                    <div class="h-10"></div>
                    <div>
                        <strong class="block text-slate-900 underline"><?= esc($order['vendor_name']) ?></strong>
                        <span class="text-[10px] text-slate-400">Head of Corporate Sales</span>
                    </div>
                </div>

                <div class="border border-purple-200 bg-purple-50/20 p-4 rounded-xl space-y-8">
                    <span class="text-[10px] text-purple-700 uppercase font-bold block">Disetujui Oleh (Buyer)</span>
                    <div class="h-10 flex items-center justify-center">
                        <?php if ($order['order_status'] === 'bast_signed' || $order['order_status'] === 'completed'): ?>
                            <span class="px-2 py-1 rounded bg-emerald-100 text-emerald-800 text-[10px] font-bold border border-emerald-300">
                                BAST VALID & TTD SAH
                            </span>
                        <?php endif; ?>
                    </div>
                    <div>
                        <strong class="block text-slate-900 underline"><?= esc($order['buyer_pic'] ?? 'Rina Kartika') ?></strong>
                        <span class="text-[10px] text-slate-400">Senior Procurement Manager</span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Sign BAST Hidden Form & Modal Script -->
<form id="signBastForm" action="<?= base_url('customer/orders/sign-bast') ?>" method="POST" style="display: none;">
    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
    <input type="hidden" name="signer_name" value="<?= esc(session('user_name') ?? 'Rina Kartika') ?>">
</form>

<script>
    function confirmSignBast() {
        AkuAdaAlert.confirm(
            'Sahkan Berita Acara Serah Terima (BAST)?',
            'Dengan mengesahkan dokumen BAST ini, Anda mengonfirmasi bahwa seluruh barang telah diterima dalam kondisi baik dan lengkap sesuai nomor PO. Periode tempo pembayaran TOP resmi berjalan.',
            function() {
                document.getElementById('signBastForm').submit();
            }
        );
    }
</script>

<?= $this->endSection() ?>
