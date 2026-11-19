<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Link Pengiriman Tidak Ditemukan - AkuAda') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="icon" type="image/x-icon" href="<?= base_url('favicon.ico') ?>">
    <style> body { font-family: 'Plus Jakarta Sans', sans-serif; } </style>
</head>
<body class="bg-slate-100 flex items-center justify-center min-h-screen p-4 text-slate-800">
    <div class="max-w-md w-full bg-white rounded-3xl p-6 shadow-xl border border-slate-200 text-center space-y-4">
        <div class="w-16 h-16 rounded-3xl bg-rose-100 text-rose-600 flex items-center justify-center mx-auto shadow-inner">
            <span class="material-symbols-outlined text-3xl">wrong_location</span>
        </div>
        <div>
            <h1 class="text-lg font-black text-slate-900">Link Pengiriman Tidak Valid</h1>
            <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                Tautan pengiriman ini tidak ditemukan, sudah kadaluarsa, atau pesanan telah selesai diproses.
            </p>
        </div>
        <div class="p-3 rounded-2xl bg-slate-50 text-xs text-slate-600 text-left border border-slate-100">
            <span class="font-bold block text-slate-800 mb-1">Panduan Pengemudi / Kurir:</span>
            Hubungi koordinator pengiriman atau tim logistik vendor Anda untuk meminta tautan link antaran terbaru melalui WhatsApp.
        </div>
        <a href="<?= base_url('/') ?>" class="inline-flex items-center justify-center gap-1.5 w-full py-2.5 rounded-xl bg-slate-900 text-white font-bold text-xs hover:bg-slate-800 transition">
            <span class="material-symbols-outlined text-[16px]">home</span>
            <span>Kembali ke Beranda AkuAda</span>
        </a>
    </div>
</body>
</html>
