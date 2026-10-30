<?= $this->extend('layouts/admin') ?>

<?= $this->section('admin_content') ?>

<div class="space-y-6">
    <!-- Header Card -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full bg-purple-100 text-purple-800 text-[10px] font-bold uppercase tracking-wider">Taksonomi Katalog</span>
            </div>
            <h1 class="text-xl font-extrabold text-slate-900 mt-1">Manajemen Kategori Pengadaan B2B</h1>
            <p class="text-xs text-slate-500 mt-0.5">Kelola kategori pengadaan barang dan jasa operasional korporat di platform AkuAda.</p>
        </div>
        <button onclick="document.getElementById('modal-add-category').classList.remove('hidden')" 
                class="btn-akuada-primary px-4 py-2.5 text-xs shadow-md flex items-center gap-2 self-start sm:self-auto">
            <span class="material-symbols-outlined text-[18px]">add_circle</span>
            <span>+ Tambah Kategori Baru</span>
        </button>
    </div>

    <!-- Categories Table Card - Fits 100% width with NO horizontal scroll -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-purple-600"></span>
                <h3 class="font-bold text-slate-800 text-sm">Daftar Kategori Terdaftar</h3>
                <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 font-semibold text-[11px]"><?= count($categories) ?> Kategori</span>
            </div>
        </div>

        <div class="w-full">
            <table class="w-full text-xs text-left border-collapse">
                <thead class="bg-slate-50/90 border-b border-slate-200 text-slate-600 font-semibold uppercase text-[11px] tracking-wider">
                    <tr>
                        <th class="px-4 py-3.5 text-center w-16">Urutan</th>
                        <th class="px-4 py-3.5 w-60">Ikon & Nama Kategori</th>
                        <th class="px-4 py-3.5 w-44 font-mono">Slug URL</th>
                        <th class="px-4 py-3.5">Deskripsi Lengkap</th>
                        <th class="px-4 py-3.5 text-center w-32">Jumlah Produk</th>
                        <th class="px-4 py-3.5 text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    <?php if (empty($categories)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-slate-400">
                                    <span class="material-symbols-outlined text-4xl mb-2 text-slate-300">category</span>
                                    <p class="font-semibold text-slate-600">Belum ada kategori pengadaan</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">Klik "+ Tambah Kategori Baru" untuk membuat kategori pertama.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($categories as $cat): ?>
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <!-- Sort Order -->
                            <td class="px-4 py-3.5 align-top text-center font-bold text-slate-400 font-mono text-xs">
                                <?= $cat['sort_order'] ?>
                            </td>

                            <!-- Category Name & Icon (Full text, no cut-off) -->
                            <td class="px-4 py-3.5 align-top">
                                <div class="flex items-start gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center font-bold shadow-sm flex-shrink-0 mt-0.5">
                                        <span class="material-symbols-outlined text-[20px]"><?= esc($cat['icon'] ?: 'category') ?></span>
                                    </div>
                                    <div class="space-y-0.5">
                                        <div class="font-bold text-slate-900 leading-snug break-words"><?= esc($cat['name']) ?></div>
                                        <div class="text-[10px] text-slate-400 font-mono">icon: <?= esc($cat['icon'] ?: 'category') ?></div>
                                    </div>
                                </div>
                            </td>

                            <!-- Slug -->
                            <td class="px-4 py-3.5 align-top font-mono text-purple-700 font-semibold break-all">
                                <?= esc($cat['slug']) ?>
                            </td>

                            <!-- Description (Full text, no truncate) -->
                            <td class="px-4 py-3.5 align-top text-slate-600 leading-relaxed break-words">
                                <?= esc($cat['description'] ?? '-') ?>
                            </td>

                            <!-- Product Count -->
                            <td class="px-4 py-3.5 align-top text-center">
                                <span class="px-2.5 py-1 rounded-full font-bold text-[10px] <?= ($cat['product_count'] > 0) ? 'bg-purple-100 text-purple-800' : 'bg-slate-100 text-slate-500' ?> inline-block">
                                    <?= $cat['product_count'] ?? 0 ?> Produk
                                </span>
                            </td>

                            <!-- Action -->
                            <td class="px-4 py-3.5 align-top text-center">
                                <div class="inline-flex items-center gap-1.5">
                                    <button onclick="editCategory(<?= htmlspecialchars(json_encode($cat), ENT_QUOTES, 'UTF-8') ?>)" 
                                            class="p-1.5 hover:bg-purple-50 text-purple-600 rounded-lg transition border border-purple-200" title="Edit Kategori">
                                        <span class="material-symbols-outlined text-[17px]">edit</span>
                                    </button>
                                    <a href="<?= base_url('admin/categories/delete/' . $cat['id']) ?>" 
                                       onclick="return confirm('Apakah Anda yakin ingin menghapus kategori <?= esc($cat['name']) ?>?');" 
                                       class="p-1.5 hover:bg-rose-50 text-rose-600 rounded-lg transition border border-rose-200" title="Hapus Kategori">
                                        <span class="material-symbols-outlined text-[17px]">delete</span>
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

<!-- Modal Tambah Kategori -->
<div id="modal-add-category" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 text-xs space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <span class="material-symbols-outlined text-purple-600">add_circle</span>
                Tambah Kategori Pengadaan Baru
            </h2>
            <button onclick="document.getElementById('modal-add-category').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <form action="<?= base_url('admin/categories/store') ?>" method="POST" class="space-y-4">
            <div>
                <label class="block font-bold text-slate-700 mb-1">Nama Kategori: <span class="text-rose-500">*</span></label>
                <input type="text" name="name" required placeholder="Contoh: Genset & Kelistrikan Gedung" 
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 font-bold">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Ikon (Material Symbol):</label>
                    <input type="text" name="icon" placeholder="electric_bolt" value="category"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 font-mono">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nomor Urutan Tampil:</label>
                    <input type="number" name="sort_order" value="10" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 font-bold text-center">
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Slug URL (Opsional):</label>
                <input type="text" name="slug" placeholder="genset-kelistrikan-gedung" 
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 font-mono text-purple-700">
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Deskripsi Kategori:</label>
                <textarea name="description" rows="3" placeholder="Deskripsi pengadaan barang ini..."
                          class="w-full px-3.5 py-2 rounded-xl border border-slate-300 focus:border-purple-600"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('modal-add-category').classList.add('hidden')" class="px-4 py-2 rounded-xl border border-slate-300 font-bold text-slate-600">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold transition">
                    Simpan Kategori
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Kategori -->
<div id="modal-edit-category" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 text-xs space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <span class="material-symbols-outlined text-purple-600">edit</span>
                Edit Kategori Pengadaan
            </h2>
            <button onclick="document.getElementById('modal-edit-category').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <form id="form-edit-category" action="" method="POST" class="space-y-4">
            <div>
                <label class="block font-bold text-slate-700 mb-1">Nama Kategori: <span class="text-rose-500">*</span></label>
                <input type="text" name="name" id="edit_name" required 
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 font-bold">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Ikon (Material Symbol):</label>
                    <input type="text" name="icon" id="edit_icon" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 font-mono">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nomor Urutan Tampil:</label>
                    <input type="number" name="sort_order" id="edit_sort_order" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 font-bold text-center">
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Slug URL:</label>
                <input type="text" name="slug" id="edit_slug" 
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 font-mono text-purple-700 font-semibold">
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Deskripsi Kategori:</label>
                <textarea name="description" id="edit_description" rows="3" 
                          class="w-full px-3.5 py-2 rounded-xl border border-slate-300 focus:border-purple-600"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('modal-edit-category').classList.add('hidden')" class="px-4 py-2 rounded-xl border border-slate-300 font-bold text-slate-600">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function editCategory(cat) {
    document.getElementById('edit_name').value = cat.name || '';
    document.getElementById('edit_slug').value = cat.slug || '';
    document.getElementById('edit_icon').value = cat.icon || 'category';
    document.getElementById('edit_sort_order').value = cat.sort_order || 0;
    document.getElementById('edit_description').value = cat.description || '';
    
    document.getElementById('form-edit-category').action = '<?= base_url('admin/categories/update/') ?>' + cat.id;
    document.getElementById('modal-edit-category').classList.remove('hidden');
}
</script>

<?= $this->endSection() ?>
