<?= $this->extend('layouts/main') ?>

<?= $this->section('subcontent') ?>

<div class="bg-slate-100 py-8 border-b border-slate-200">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-2 text-xs text-slate-500 mb-2">
            <a href="<?= base_url('/') ?>" class="hover:text-purple-600">Beranda</a>
            <span>/</span>
            <span class="text-slate-800 font-semibold">Pusat Informasi Pengadaan</span>
        </div>
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-purple-100 text-purple-700 flex items-center justify-center shadow-sm">
                <span class="material-symbols-outlined text-2xl"><?= esc($page['icon']) ?></span>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900"><?= esc($page['title']) ?></h1>
        </div>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="bg-white rounded-3xl p-8 sm:p-10 border border-slate-200 shadow-sm space-y-6">
        <div class="text-sm text-slate-700 leading-relaxed whitespace-pre-line space-y-4">
            <?= esc($page['body']) ?>
        </div>

        <div class="pt-6 border-t border-slate-100 flex items-center justify-between">
            <a href="<?= base_url('products') ?>" class="btn-akuada-primary text-xs px-5 py-2.5">
                <span class="material-symbols-outlined text-[18px]">shopping_bag</span>
                <span>Kembali ke Katalog Belanja B2B</span>
            </a>
            <a href="<?= base_url('rfq/create') ?>" class="px-5 py-2.5 rounded-xl border border-purple-300 text-purple-700 hover:bg-purple-50 font-bold text-xs transition">
                Ajukan RFQ Tender
            </a>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
