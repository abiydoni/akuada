<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'AkuAda - B2B Marketplace & E-Procurement Perusahaan') ?></title>
    
    <!-- Modern Typography: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Google Material Symbols Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    
    <!-- Tailwind CSS with Custom Brand Configuration -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        akuada: {
                            purple: {
                                50: '#f5f3ff',
                                100: '#ede9fe',
                                200: '#ddd6fe',
                                300: '#c4b5fd',
                                400: '#a78bfa',
                                500: '#8b5cf6',
                                600: '#7c3aed',
                                700: '#6d28d9',
                                800: '#5b21b6',
                                900: '#4c1d95',
                            },
                            blue: {
                                50: '#eff6ff',
                                100: '#dbeafe',
                                200: '#bfdbfe',
                                300: '#93c5fd',
                                400: '#60a5fa',
                                500: '#3b82f6',
                                600: '#2563eb',
                                700: '#1d4ed8',
                                800: '#1e40af',
                                900: '#1e3a8a',
                            },
                            pink: {
                                50: '#fdf2f8',
                                100: '#fce7f3',
                                200: '#fbcfe8',
                                300: '#f9a8d4',
                                400: '#f472b6',
                                500: '#ec4899',
                                600: '#db2777',
                                700: '#be185d',
                                800: '#9d174d',
                                900: '#831843',
                            }
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Centralized AkuAda Design System Stylesheet -->
    <link rel="stylesheet" href="<?= base_url('css/akuada-theme.css') ?>">
    <script src="<?= base_url('js/akuada-swal.js') ?>"></script>
</head>
<body class="h-full flex flex-col font-sans antialiased text-slate-800 bg-slate-50 selection:bg-pink-500 selection:text-white">

    <!-- Role Switcher Quick Bar (Uniform across all pages) -->
    <div class="bg-gradient-to-r from-slate-900 via-purple-950 to-slate-900 text-white text-xs py-1.5 px-4 border-b border-purple-800/40 z-50">
        <div class="max-w-7xl mx-auto flex flex-wrap items-center justify-between gap-2">
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-pink-500/20 text-pink-300 font-semibold border border-pink-500/40">
                    <span class="material-symbols-outlined text-[13px]">tune</span> Role Simulator B2B
                </span>
                <span class="text-slate-300 hidden sm:inline">Pindah mode dashboard secara instan:</span>
            </div>
            <div class="flex items-center gap-1.5 flex-wrap">
                <a href="<?= base_url('/') ?>" class="px-2.5 py-1 rounded transition text-xs font-medium <?= current_url() == base_url('/') ? 'bg-purple-600 text-white shadow' : 'bg-slate-800/80 text-slate-300 hover:text-white hover:bg-slate-700' ?> flex items-center gap-1">
                    <span class="material-symbols-outlined text-[14px]">storefront</span> Marketplace
                </a>
                <a href="<?= base_url('auth/switch-role/admin') ?>" class="px-2.5 py-1 rounded transition text-xs font-medium <?= session('user_role') === 'admin' && str_contains(current_url(), 'admin') ? 'bg-purple-600 text-white shadow' : 'bg-slate-800/80 text-slate-300 hover:text-white hover:bg-slate-700' ?> flex items-center gap-1">
                    <span class="material-symbols-outlined text-[14px]">admin_panel_settings</span> Admin
                </a>
                <a href="<?= base_url('auth/switch-role/vendor') ?>" class="px-2.5 py-1 rounded transition text-xs font-medium <?= session('user_role') === 'vendor' && str_contains(current_url(), 'vendor') ? 'bg-blue-600 text-white shadow' : 'bg-slate-800/80 text-slate-300 hover:text-white hover:bg-slate-700' ?> flex items-center gap-1">
                    <span class="material-symbols-outlined text-[14px]">store</span> Vendor (Supplier)
                </a>
                <a href="<?= base_url('auth/switch-role/customer') ?>" class="px-2.5 py-1 rounded transition text-xs font-medium <?= session('user_role') === 'customer' && str_contains(current_url(), 'customer') ? 'bg-pink-600 text-white shadow' : 'bg-slate-800/80 text-slate-300 hover:text-white hover:bg-slate-700' ?> flex items-center gap-1">
                    <span class="material-symbols-outlined text-[14px]">corporate_fare</span> Buyer (Customer PT)
                </a>
                <div class="h-4 w-px bg-slate-700 mx-1 hidden sm:block"></div>
                <div class="text-[11px] text-slate-400 flex items-center gap-1">
                    <span class="material-symbols-outlined text-[14px] text-emerald-400">verified</span>
                    <span>Aktif: <strong class="text-white"><?= esc(session('user_name') ?? 'Guest / Buyer') ?></strong></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main View Content -->
    <?= $this->renderSection('content') ?>

    <!-- Flash message automatic SweetAlert handler -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            <?php if (session()->getFlashdata('success')): ?>
                AkuAdaAlert.success('Berhasil!', '<?= addslashes(session()->getFlashdata('success')) ?>');
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                AkuAdaAlert.error('Perhatian!', '<?= addslashes(session()->getFlashdata('error')) ?>');
            <?php endif; ?>
            <?php if (session()->getFlashdata('toast')): ?>
                AkuAdaAlert.toast('success', '<?= addslashes(session()->getFlashdata('toast')) ?>');
            <?php endif; ?>
        });
    </script>
</body>
</html>
