<?= $this->extend('layouts/main') ?>

<?= $this->section('subcontent') ?>

<div class="bg-slate-100 py-6 border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-extrabold text-slate-900 flex items-center gap-2">
            <span class="material-symbols-outlined text-purple-600 text-3xl">assignment_add</span>
            Form Permintaan Penawaran Resmi (Request for Quotation / RFQ)
        </h1>
        <p class="text-xs text-slate-500 mt-1">
            Cantumkan rincian kebutuhan barang operasional, spesifikasi yang diinginkan, dan target harga untuk ditanggapi langsung oleh vendor rekanan resmi.
        </p>
    </div>
</div>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <form action="<?= base_url('rfq/store') ?>" method="POST" class="space-y-6">
        
        <!-- Section 1: Pengadaan Info -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm space-y-4 text-xs">
            <h2 class="text-sm font-bold text-slate-800 pb-3 border-b border-slate-100 flex items-center gap-2">
                <span class="material-symbols-outlined text-purple-600">info</span>
                Informasi Umum Pengadaan B2B
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block font-bold text-slate-700 mb-1">Judul Permintaan Pengadaan: <span class="text-rose-500">*</span></label>
                    <input type="text" name="title" required placeholder="Contoh: Pengadaan 50 Set Laptop & Monitor Divisi IT Cabang Surabaya" 
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 focus:ring-2 focus:ring-purple-100 text-slate-900 font-semibold">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Target Vendor Rekanan:</label>
                    <select name="vendor_company_id" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 focus:ring-2 focus:ring-purple-100 bg-white">
                        <option value="">-- Terbuka Untuk Semua Vendor Terverifikasi --</option>
                        <?php foreach ($vendors as $v): ?>
                            <option value="<?= $v['id'] ?>"><?= esc($v['company_name']) ?> (<?= esc($v['legal_type']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Target Batas Waktu Pengiriman (Delivery Date): <span class="text-rose-500">*</span></label>
                    <input type="date" name="target_delivery_date" required value="<?= date('Y-m-d', strtotime('+14 days')) ?>"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 focus:ring-2 focus:ring-purple-100">
                </div>

                <div class="md:col-span-2">
                    <label class="block font-bold text-slate-700 mb-1">Catatan Tambahan & Ketentuan Tender:</label>
                    <textarea name="notes" rows="3" placeholder="Sebutkan ketentuan garansi, SLA perbaikan on-site, sertifikasi TKDN yang diperlukan, atau simulasi tempo pembayaran (TOP) yang diajukan." 
                              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 focus:ring-2 focus:ring-purple-100"></textarea>
                </div>
            </div>
        </div>

        <!-- Section 2: Daftar Item Kebutuhan Barang -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm space-y-4 text-xs">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-pink-600">format_list_bulleted</span>
                    <h2 class="text-sm font-bold text-slate-800">Daftar Barang yang Diminta Penawarannya</h2>
                </div>
                <button type="button" onclick="addRow()" class="px-3 py-1.5 rounded-lg bg-purple-50 text-purple-700 hover:bg-purple-100 font-bold border border-purple-200 flex items-center gap-1 transition">
                    <span class="material-symbols-outlined text-[16px]">add</span> Tambah Baris
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left" id="rfqItemTable">
                    <thead class="bg-slate-50 text-slate-600 font-bold text-[11px] uppercase">
                        <tr>
                            <th class="p-3">Nama Barang & Spesifikasi Teknis</th>
                            <th class="p-3 w-28">Kuantitas</th>
                            <th class="p-3 w-32">Satuan (UOM)</th>
                            <th class="p-3 w-40">Target Harga (Rp)</th>
                            <th class="p-3">Catatan Khusus</th>
                            <th class="p-3 w-12 text-center">Hapus</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100" id="rfqItemBody">
                        <tr class="item-row">
                            <td class="p-2">
                                <input type="text" name="item_name[]" required placeholder="Contoh: Laptop Core i7 RAM 16GB SSD 512GB" 
                                       class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:border-purple-600">
                            </td>
                            <td class="p-2">
                                <input type="number" name="qty[]" min="1" value="10" required 
                                       class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:border-purple-600 text-center font-bold">
                            </td>
                            <td class="p-2">
                                <select name="uom[]" class="w-full px-2 py-2 rounded-lg border border-slate-200 bg-white">
                                    <option value="Unit">Unit</option>
                                    <option value="Pcs">Pcs</option>
                                    <option value="Box">Box</option>
                                    <option value="Karton">Karton</option>
                                    <option value="Rim">Rim</option>
                                    <option value="Paket">Paket</option>
                                    <option value="Set">Set</option>
                                </select>
                            </td>
                            <td class="p-2">
                                <input type="number" name="target_unit_price[]" placeholder="Rp / Satuan" 
                                       class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:border-purple-600">
                            </td>
                            <td class="p-2">
                                <input type="text" name="item_notes[]" placeholder="Garansi 3 Th Onsite" 
                                       class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:border-purple-600">
                            </td>
                            <td class="p-2 text-center">
                                <button type="button" onclick="removeRow(this)" class="text-slate-400 hover:text-rose-600">
                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="<?= base_url('rfq') ?>" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50 font-semibold text-xs transition">
                Batal
            </a>
            <button type="submit" class="btn-akuada-primary px-6 py-2.5 text-xs">
                <span class="material-symbols-outlined text-[18px]">send</span>
                <span>Kirim Permintaan RFQ Resmi</span>
            </button>
        </div>
    </form>
</div>

<script>
    function addRow() {
        const body = document.getElementById('rfqItemBody');
        const newRow = document.createElement('tr');
        newRow.className = 'item-row';
        newRow.innerHTML = `
            <td class="p-2">
                <input type="text" name="item_name[]" required placeholder="Nama barang & spek" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:border-purple-600">
            </td>
            <td class="p-2">
                <input type="number" name="qty[]" min="1" value="1" required class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:border-purple-600 text-center font-bold">
            </td>
            <td class="p-2">
                <select name="uom[]" class="w-full px-2 py-2 rounded-lg border border-slate-200 bg-white">
                    <option value="Unit">Unit</option>
                    <option value="Pcs">Pcs</option>
                    <option value="Box">Box</option>
                    <option value="Karton">Karton</option>
                    <option value="Rim">Rim</option>
                    <option value="Paket">Paket</option>
                    <option value="Set">Set</option>
                </select>
            </td>
            <td class="p-2">
                <input type="number" name="target_unit_price[]" placeholder="Rp / Satuan" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:border-purple-600">
            </td>
            <td class="p-2">
                <input type="text" name="item_notes[]" placeholder="Catatan spek" class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:border-purple-600">
            </td>
            <td class="p-2 text-center">
                <button type="button" onclick="removeRow(this)" class="text-slate-400 hover:text-rose-600">
                    <span class="material-symbols-outlined text-[18px]">delete</span>
                </button>
            </td>
        `;
        body.appendChild(newRow);
    }

    function removeRow(btn) {
        const rows = document.querySelectorAll('.item-row');
        if (rows.length > 1) {
            btn.closest('tr').remove();
        } else {
            AkuAdaAlert.toast('warning', 'Minimal harus ada 1 barang dalam daftar RFQ.');
        }
    }
</script>

<?= $this->endSection() ?>
