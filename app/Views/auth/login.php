<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>

<div class="min-h-screen w-full py-10 lg:py-16 px-4 sm:px-6 lg:px-8 bg-gradient-to-br from-slate-950 via-[#130d24] to-slate-950 flex items-center justify-center relative overflow-x-hidden">
    <!-- Ambient ambient glow decorations in background -->
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-purple-600/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute top-1/2 -right-32 w-[30rem] h-[30rem] bg-pink-500/15 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-32 left-1/3 w-96 h-96 bg-blue-600/15 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-6xl w-full grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center relative z-10">

        <!-- LEFT COLUMN: Enterprise Value Showcase & Informative Highlights (Col 7) -->
        <div class="lg:col-span-7 space-y-6 text-white order-2 lg:order-1">
            
            <!-- Brand Badge & Logo -->
            <div class="space-y-3">
                <a href="<?= base_url('/') ?>" class="inline-flex items-center gap-3 group">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-purple-600 via-blue-600 to-pink-500 flex items-center justify-center text-white shadow-xl shadow-purple-500/30 group-hover:scale-105 transition transform">
                        <span class="material-symbols-outlined text-3xl">domain</span>
                    </div>
                    <div>
                        <span class="text-2xl font-black tracking-tight text-white block leading-none">
                            Aku<span class="text-transparent bg-clip-text bg-gradient-to-r from-pink-400 to-purple-400">Ada</span>
                        </span>
                        <span class="text-[10px] text-purple-300 font-bold uppercase tracking-wider block pt-0.5">B2B E-Procurement Hub</span>
                    </div>
                </a>

                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-500/20 border border-purple-400/30 text-purple-200 text-xs font-semibold backdrop-blur-md">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Platform Pengadaan Operasional Perusahaan Resmi & Terpercaya</span>
                </div>

                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black text-white tracking-tight leading-snug">
                    Solusi Pengadaan B2B Terpadu, Transparan, & Akuntabel
                </h1>
                <p class="text-xs sm:text-sm text-slate-300 leading-relaxed max-w-xl">
                    Satu portal untuk menghubungkan perusahaan pembeli (Corporate Buyer) dengan mitra supplier terverifikasi di seluruh Indonesia dengan fasilitas tempo resmi dan kepatuhan perpajakan penuh.
                </p>
            </div>

            <!-- 4 Key Informative Feature Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 pt-1">
                <!-- Card 1: TOP Payment -->
                <div class="p-4 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-md hover:bg-white/10 hover:border-purple-400/40 transition space-y-1.5">
                    <div class="w-9 h-9 rounded-xl bg-purple-500/20 text-purple-300 flex items-center justify-center">
                        <span class="material-symbols-outlined text-xl">credit_card</span>
                    </div>
                    <h3 class="font-bold text-white text-xs">Fasilitas Term of Payment (TOP)</h3>
                    <p class="text-[11px] text-slate-300 leading-relaxed">
                        Fasilitas belanja tempo korporat hingga 30 hari. Jatuh tempo dihitung resmi setelah Berita Acara Serah Terima (BAST) disahkan.
                    </p>
                </div>

                <!-- Card 2: Cashback Buyer -->
                <div class="p-4 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-md hover:bg-white/10 hover:border-emerald-400/40 transition space-y-1.5">
                    <div class="w-9 h-9 rounded-xl bg-emerald-500/20 text-emerald-300 flex items-center justify-center">
                        <span class="material-symbols-outlined text-xl">savings</span>
                    </div>
                    <h3 class="font-bold text-white text-xs">Cashback Saldo Buyer Otomatis</h3>
                    <p class="text-[11px] text-slate-300 leading-relaxed">
                        Setiap transaksi invoice yang lunas menghasilkan saldo cashback yang dapat dicairkan langsung ke rekening bank perusahaan Anda.
                    </p>
                </div>

                <!-- Card 3: e-Faktur Pajak PPN -->
                <div class="p-4 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-md hover:bg-white/10 hover:border-blue-400/40 transition space-y-1.5">
                    <div class="w-9 h-9 rounded-xl bg-blue-500/20 text-blue-300 flex items-center justify-center">
                        <span class="material-symbols-outlined text-xl">receipt_long</span>
                    </div>
                    <h3 class="font-bold text-white text-xs">Faktur Pajak PPN Terintegrasi</h3>
                    <p class="text-[11px] text-slate-300 leading-relaxed">
                        e-Faktur PPN resmi diterbitkan otomatis atas nama entitas PT/CV Anda, menjamin kepatuhan pajak DJP dan pelaporan SPT Masa.
                    </p>
                </div>

                <!-- Card 4: Tier Pricing & TKDN -->
                <div class="p-4 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-md hover:bg-white/10 hover:border-pink-400/40 transition space-y-1.5">
                    <div class="w-9 h-9 rounded-xl bg-pink-500/20 text-pink-300 flex items-center justify-center">
                        <span class="material-symbols-outlined text-xl">inventory_2</span>
                    </div>
                    <h3 class="font-bold text-white text-xs">Harga Grosir Bertingkat & TKDN</h3>
                    <p class="text-[11px] text-slate-300 leading-relaxed">
                        Akses ribuan SKU produk pengadaan langsung dari produsen resmi dengan harga tier volume dan sertifikasi TKDN terverifikasi.
                    </p>
                </div>
            </div>

            <!-- Live Enterprise Stats Strip -->
            <div class="p-4 rounded-2xl bg-gradient-to-r from-purple-900/40 via-blue-900/40 to-slate-900/60 border border-purple-500/30 backdrop-blur-md flex flex-wrap items-center justify-between gap-4">
                <div class="space-y-0.5">
                    <div class="text-lg font-black text-white font-mono">50.000+</div>
                    <div class="text-[10px] text-purple-200 uppercase font-semibold">SKU Pengadaan B2B</div>
                </div>
                <div class="h-8 w-px bg-white/10 hidden sm:block"></div>
                <div class="space-y-0.5">
                    <div class="text-lg font-black text-emerald-400 font-mono">100%</div>
                    <div class="text-[10px] text-purple-200 uppercase font-semibold">Faktur Pajak Sah</div>
                </div>
                <div class="h-8 w-px bg-white/10 hidden sm:block"></div>
                <div class="space-y-0.5">
                    <div class="text-lg font-black text-blue-400 font-mono">TOP s.d 30 Hari</div>
                    <div class="text-[10px] text-purple-200 uppercase font-semibold">Fasilitas Kredit Tempo</div>
                </div>
                <div class="h-8 w-px bg-white/10 hidden sm:block"></div>
                <div class="space-y-0.5">
                    <div class="text-lg font-black text-pink-400 font-mono">B2B Escrow</div>
                    <div class="text-[10px] text-purple-200 uppercase font-semibold">Jaminan Transaksi Aman</div>
                </div>
            </div>

            <!-- Security Assurance Note -->
            <div class="flex items-center gap-2 text-[11px] text-slate-400 pt-1">
                <span class="material-symbols-outlined text-[16px] text-emerald-400">verified</span>
                <span>Enkripsi SSL 256-Bit • Perlindungan Anti-Bot Aktif • Tata Kelola GCG Pengadaan Teruji</span>
            </div>

        </div>

        <!-- RIGHT COLUMN: Modern Interactive Login Card (Col 5) -->
        <div class="lg:col-span-5 order-1 lg:order-2">
            <div class="bg-white/95 backdrop-blur-xl p-7 sm:p-8 rounded-3xl border border-white/20 shadow-2xl relative space-y-6">

                <!-- Header Card -->
                <div class="space-y-1.5 pb-2 border-b border-slate-100">
                    <div class="flex items-center justify-between">
                        <span class="px-2.5 py-0.5 rounded-full bg-purple-100 text-purple-800 text-[10px] font-extrabold uppercase tracking-wider">
                            Autentikasi Akun
                        </span>
                        <span class="text-[10px] text-slate-400 font-mono flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Server Aktif
                        </span>
                    </div>
                    <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                        Masuk ke Portal
                    </h2>
                    <p class="text-xs text-slate-500">
                        Satu pintu masuk untuk Buyer Korporat, Mitra Vendor, dan Administrator.
                    </p>
                </div>

                <!-- Login Form -->
                <form action="<?= base_url('auth/login/process') ?>" method="POST" class="space-y-4 text-xs">
                    <?= csrf_field() ?>

                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block font-bold text-slate-700 mb-1">Email Perusahaan / Akun:</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <span class="material-symbols-outlined text-[18px]">alternate_email</span>
                            </span>
                            <input type="email" id="email" name="email" required placeholder="nama@perusahaan.co.id" value="<?= old('email') ?>"
                                   class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 focus:ring-4 focus:ring-purple-100 transition text-slate-800 font-medium">
                        </div>
                    </div>

                    <!-- Password Field with Show/Hide Toggle -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label for="password" class="block font-bold text-slate-700">Kata Sandi:</label>
                            <span onclick="AkuAdaAlert ? AkuAdaAlert.info('Bantuan Kata Sandi', 'Untuk mengatur ulang kata sandi akun korporat Anda, hubungi Admin atau email ke support@akuada.co.id.') : alert('Hubungi support@akuada.co.id')" class="text-[11px] text-purple-600 hover:underline cursor-pointer font-medium">
                                Lupa Sandi?
                            </span>
                        </div>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <span class="material-symbols-outlined text-[18px]">lock</span>
                            </span>
                            <input type="password" id="password" name="password" required placeholder="Masukkan kata sandi akun"
                                   class="w-full pl-10 pr-11 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 focus:ring-4 focus:ring-purple-100 transition text-slate-800 font-mono">
                            <button type="button" onclick="togglePasswordVisibility()" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none">
                                <span class="material-symbols-outlined text-[18px]" id="passwordToggleIcon">visibility</span>
                            </button>
                        </div>
                    </div>

                    <!-- 1-Click Smart Captcha (Saya Bukan Robot) -->
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 hover:border-purple-300 transition-all shadow-xs" id="captchaBox">
                        <!-- Honeypot anti-bot hidden field -->
                        <div style="display:none !important; visibility:hidden !important; opacity:0 !important; position:absolute !important; left:-9999px !important;">
                            <input type="text" name="auth_security_guard_hp" tabindex="-1" autocomplete="off">
                        </div>
                        
                        <input type="hidden" name="captcha_token" id="captchaToken" value="">

                        <div class="flex items-center justify-between gap-3 select-none">
                            <div class="flex items-center gap-3 cursor-pointer group" onclick="triggerSmartCaptcha()">
                                <!-- Custom 1-Click Checkbox Box -->
                                <div id="captchaCheckWrapper" class="w-7 h-7 rounded-lg border-2 border-slate-300 bg-white flex items-center justify-center transition-all shadow-2xs group-hover:border-purple-500">
                                    <!-- Idle icon / unchecked -->
                                    <span id="captchaIconEmpty" class="w-2.5 h-2.5 rounded-xs bg-transparent"></span>
                                    <!-- Spinning loader -->
                                    <span id="captchaSpinner" class="material-symbols-outlined text-purple-600 text-lg animate-spin hidden">progress_activity</span>
                                    <!-- Verified checkmark -->
                                    <span id="captchaIconChecked" class="material-symbols-outlined text-emerald-600 text-xl font-black hidden">check_circle</span>
                                </div>

                                <div class="flex flex-col">
                                    <span class="font-extrabold text-slate-800 text-xs transition" id="captchaLabel">Saya bukan robot</span>
                                    <span class="text-[10px] text-slate-400" id="captchaSub">Klik kotak untuk verifikasi keamanan</span>
                                </div>
                            </div>

                            <div class="flex flex-col items-end text-slate-400 flex-shrink-0 pl-2.5 border-l border-slate-200">
                                <span class="material-symbols-outlined text-purple-600 text-xl">verified_user</span>
                                <span class="text-[9px] font-bold text-slate-500 tracking-tight">AkuAda Guard</span>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full py-3.5 rounded-xl bg-gradient-to-r from-purple-700 via-indigo-600 to-blue-600 hover:opacity-95 text-white font-extrabold text-xs shadow-lg shadow-purple-600/30 flex items-center justify-center gap-2 transition transform active:scale-[0.99] mt-2">
                        <span class="material-symbols-outlined text-[18px]">login</span>
                        <span>Masuk ke Akun Sekarang</span>
                    </button>
                </form>

                <!-- Script 1-Click Captcha & Password Visibility -->
                <script>
                    let isCaptchaVerified = false;

                    function triggerSmartCaptcha() {
                        if (isCaptchaVerified) return;

                        const wrapper = document.getElementById('captchaCheckWrapper');
                        const emptyIcon = document.getElementById('captchaIconEmpty');
                        const spinner = document.getElementById('captchaSpinner');
                        const checked = document.getElementById('captchaIconChecked');
                        const label = document.getElementById('captchaLabel');
                        const sub = document.getElementById('captchaSub');
                        const box = document.getElementById('captchaBox');
                        const tokenInput = document.getElementById('captchaToken');

                        // Tampilkan animasi putar sesaat (simulasi verifikasi 1-klik)
                        emptyIcon.classList.add('hidden');
                        spinner.classList.remove('hidden');
                        wrapper.classList.remove('border-slate-300');
                        wrapper.classList.add('border-purple-500');
                        label.textContent = 'Memverifikasi manusia...';

                        setTimeout(() => {
                            spinner.classList.add('hidden');
                            checked.classList.remove('hidden');
                            wrapper.classList.remove('border-purple-500');
                            wrapper.classList.add('border-emerald-500', 'bg-emerald-50');
                            box.classList.remove('border-slate-200', 'border-rose-400');
                            box.classList.add('border-emerald-300', 'bg-emerald-50/40');

                            label.textContent = 'Terverifikasi (Saya Manusia)';
                            label.classList.add('text-emerald-800');
                            sub.textContent = 'Verifikasi keamanan 1-klik berhasil';
                            sub.classList.add('text-emerald-600');

                            tokenInput.value = '<?= esc($captchaHash ?? '') ?>';
                            isCaptchaVerified = true;
                        }, 400);
                    }

                    function togglePasswordVisibility() {
                        const pwd = document.getElementById('password');
                        const icon = document.getElementById('passwordToggleIcon');
                        if (pwd.type === 'password') {
                            pwd.type = 'text';
                            icon.textContent = 'visibility_off';
                        } else {
                            pwd.type = 'password';
                            icon.textContent = 'visibility';
                        }
                    }

                    document.addEventListener('DOMContentLoaded', function() {
                        const loginForm = document.querySelector('form[action*="login/process"]');
                        if (loginForm) {
                            loginForm.addEventListener('submit', function(e) {
                                const token = document.getElementById('captchaToken').value;
                                if (!token || !isCaptchaVerified) {
                                    e.preventDefault();
                                    const box = document.getElementById('captchaBox');
                                    box.classList.add('border-rose-400', 'bg-rose-50/40', 'animate-pulse');
                                    setTimeout(() => box.classList.remove('animate-pulse'), 800);
                                    
                                    if (typeof AkuAdaAlert !== 'undefined') {
                                        AkuAdaAlert.warning('Verifikasi Diperlukan', 'Silakan klik kotak "Saya bukan robot" untuk melanjutkan masuk.');
                                    } else {
                                        alert('Silakan klik kotak "Saya bukan robot" terlebih dahulu.');
                                    }
                                }
                            });
                        }
                    });
                </script>

                <!-- Registration Promotion Card for Corporate Buyers -->
                <div class="pt-4 border-t border-slate-100 space-y-2.5">
                    <div class="p-3.5 rounded-2xl bg-gradient-to-r from-purple-50 to-pink-50 border border-purple-200/80 space-y-2">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-purple-700 text-lg">add_business</span>
                            <strong class="font-extrabold text-slate-900 text-xs">Belum Punya Akun Perusahaan?</strong>
                        </div>
                        <p class="text-[11px] text-slate-600 leading-snug">
                            Daftarkan entitas PT/CV Anda dan unggah berkas legalitas untuk membuka fasilitas Term of Payment (TOP) & harga grosir.
                        </p>
                        <a href="<?= base_url('auth/register') ?>" class="w-full py-2.5 px-3 rounded-xl bg-purple-700 hover:bg-purple-800 text-white font-bold text-xs flex items-center justify-center gap-1.5 transition shadow-xs">
                            <span class="material-symbols-outlined text-[16px]">how_to_reg</span>
                            <span>Daftar Akun Buyer Baru & Unggah Legalitas &rarr;</span>
                        </a>
                    </div>

                    <!-- Vendor Partnership Notice -->
                    <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200 text-[10.5px] text-slate-500 flex items-start gap-2">
                        <span class="material-symbols-outlined text-blue-600 text-[16px] flex-shrink-0 mt-0.5">storefront</span>
                        <div>
                            <strong>Ingin Jadi Rekanan Mitra Supplier / Vendor?</strong> Hubungi kami di <a href="mailto:vendor@akuada.co.id" class="text-blue-700 font-bold underline">vendor@akuada.co.id</a> untuk kurasi katalog & PKS resmi.
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

<?= $this->endSection() ?>
