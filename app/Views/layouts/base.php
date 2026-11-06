<!DOCTYPE html>
<html lang="id" class="min-h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token-name" content="<?= csrf_token() ?>">
    <meta name="csrf-hash" content="<?= csrf_hash() ?>">
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
    <script src="<?= base_url('js/akuada-swal.js?v=' . filemtime(FCPATH . 'js/akuada-swal.js')) ?>"></script>
    <script src="<?= base_url('js/akuada-realtime.js?v=' . filemtime(FCPATH . 'js/akuada-realtime.js')) ?>"></script>
</head>
<body class="min-h-screen flex flex-col font-sans antialiased text-slate-800 bg-slate-50 selection:bg-pink-500 selection:text-white overflow-x-hidden">

    <!-- Main View Content -->
    <?= $this->renderSection('content') ?>

    <!-- Flash message automatic SweetAlert handler -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            <?php if (session()->getFlashdata('success')): ?>
                <?php 
                    $successMsg = (string)session()->getFlashdata('success');
                    $isLong = mb_strlen($successMsg) > 120;
                ?>
                <?php if ($isLong): ?>
                    AkuAdaAlert.success('Berhasil!', '<?= addslashes($successMsg) ?>');
                <?php else: ?>
                    AkuAdaAlert.toast('success', '<?= addslashes($successMsg) ?>');
                <?php endif; ?>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                AkuAdaAlert.error('Perhatian!', '<?= addslashes(session()->getFlashdata('error')) ?>');
            <?php endif; ?>
            <?php if (session()->getFlashdata('warning')): ?>
                AkuAdaAlert.warning('Peringatan', '<?= addslashes(session()->getFlashdata('warning')) ?>');
            <?php endif; ?>
            <?php if (session()->getFlashdata('info')): ?>
                AkuAdaAlert.toast('info', '<?= addslashes(session()->getFlashdata('info')) ?>');
            <?php endif; ?>
            <?php if (session()->getFlashdata('toast')): ?>
                AkuAdaAlert.toast('success', '<?= addslashes(session()->getFlashdata('toast')) ?>');
            <?php endif; ?>
        });
    </script>
    <!-- Global CSRF Auto-Injector for all POST forms -->
    <script>
        (function() {
            function autoInjectCsrf() {
                const tokenName = '<?= csrf_token() ?>';
                const tokenHash = '<?= csrf_hash() ?>';
                document.querySelectorAll('form').forEach(function(form) {
                    const method = (form.getAttribute('method') || '').toUpperCase();
                    if (method === 'POST') {
                        let input = form.querySelector('input[name="' + tokenName + '"]');
                        if (!input) {
                            input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = tokenName;
                            form.prepend(input);
                        }
                        input.value = tokenHash;
                    }
                });
            }
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', autoInjectCsrf);
            } else {
                autoInjectCsrf();
            }
            // Re-inject on any dynamic modal or form show
            document.addEventListener('click', function() { setTimeout(autoInjectCsrf, 50); });
        })();
    </script>
</body>
</html>
