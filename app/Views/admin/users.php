<?= $this->extend('layouts/admin') ?>

<?= $this->section('admin_content') ?>

<div class="space-y-6">
    <!-- Header Card -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full bg-purple-100 text-purple-800 text-[10px] font-bold uppercase tracking-wider">Otoritas Akses Sistem</span>
            </div>
            <h1 class="text-xl font-extrabold text-slate-900 mt-1">Manajemen Akun Pengguna (Users)</h1>
            <p class="text-xs text-slate-500 mt-0.5">Kelola seluruh hak akses login platform: Superadmin, Mitra Vendor, dan Tim Procurement Buyer.</p>
        </div>
        <button onclick="document.getElementById('modal-add-user').classList.remove('hidden')" 
                class="btn-akuada-primary px-4 py-2.5 text-xs shadow-md flex items-center gap-2 self-start sm:self-auto">
            <span class="material-symbols-outlined text-[18px]">person_add</span>
            <span>+ Buat Akun Pengguna Baru</span>
        </button>
    </div>

    <!-- Role Filter Pills -->
    <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs">
        <a href="<?= base_url('admin/users') ?>" 
           class="px-3.5 py-1.5 rounded-xl font-bold transition whitespace-nowrap <?= empty($roleFilter) ? 'bg-purple-600 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-50' ?>">
            Semua Pengguna (<?= count($users) ?>)
        </a>
        <a href="<?= base_url('admin/users?role=admin') ?>" 
           class="px-3.5 py-1.5 rounded-xl font-bold transition whitespace-nowrap <?= ($roleFilter === 'admin') ? 'bg-purple-600 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-50' ?>">
            Superadmin
        </a>
        <a href="<?= base_url('admin/users?role=vendor') ?>" 
           class="px-3.5 py-1.5 rounded-xl font-bold transition whitespace-nowrap <?= ($roleFilter === 'vendor') ? 'bg-purple-600 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-50' ?>">
            Mitra Vendor
        </a>
        <a href="<?= base_url('admin/users?role=customer') ?>" 
           class="px-3.5 py-1.5 rounded-xl font-bold transition whitespace-nowrap <?= ($roleFilter === 'customer') ? 'bg-purple-600 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-50' ?>">
            Corporate Buyer
        </a>
    </div>

    <!-- Users Table Card - Fits 100% width with NO horizontal scroll -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-purple-600"></span>
                <h3 class="font-bold text-slate-800 text-sm">Daftar Akun Pengguna</h3>
                <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 font-semibold text-[11px]"><?= count($users) ?> Akun</span>
            </div>
        </div>

        <div class="w-full">
            <table class="w-full text-xs text-left border-collapse">
                <thead class="bg-slate-50/90 border-b border-slate-200 text-slate-600 font-semibold uppercase text-[11px] tracking-wider">
                    <tr>
                        <th class="px-4 py-3.5">Nama Pengguna & Kontak</th>
                        <th class="px-4 py-3.5 text-center w-40">Peran (Role)</th>
                        <th class="px-4 py-3.5">Perusahaan Terkait</th>
                        <th class="px-4 py-3.5 text-center w-36">Status Akun</th>
                        <th class="px-4 py-3.5 text-center w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-slate-400">
                                    <span class="material-symbols-outlined text-4xl mb-2 text-slate-300">manage_accounts</span>
                                    <p class="font-semibold text-slate-600">Tidak ada data pengguna ditemukan</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">Gunakan filter lain atau buat akun pengguna baru.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($users as $u): ?>
                        <?php 
                            $roleBg = match($u['role']) {
                                'admin'    => 'bg-purple-50 text-purple-800 border-purple-200',
                                'vendor'   => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                                'customer' => 'bg-blue-50 text-blue-800 border-blue-200',
                                default    => 'bg-slate-100 text-slate-800 border-slate-200'
                            };
                            $roleLabel = match($u['role']) {
                                'admin'    => 'Superadmin Platform',
                                'vendor'   => 'Mitra Vendor (Supplier)',
                                'customer' => 'Corporate Buyer',
                                default    => ucfirst($u['role'])
                            };
                        ?>
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <!-- User, Email & Phone (No text cut-off) -->
                            <td class="px-4 py-3.5 align-top">
                                <div class="flex items-start gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-purple-600 to-blue-600 text-white font-black flex items-center justify-center text-sm shadow-sm flex-shrink-0 mt-0.5">
                                        <?= strtoupper(substr($u['name'], 0, 1)) ?>
                                    </div>
                                    <div class="space-y-0.5">
                                        <div class="font-bold text-slate-900 leading-snug break-words"><?= esc($u['name']) ?></div>
                                        <div class="text-[11px] text-slate-500 font-mono"><?= esc($u['email']) ?></div>
                                        <div class="text-[11px] text-slate-400 font-mono">
                                            Jabatan: <span class="text-slate-700 font-medium"><?= esc($u['position'] ?? 'Staff') ?></span> • Telp: <?= esc($u['phone'] ?? '-') ?>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Role -->
                            <td class="px-4 py-3.5 align-top text-center">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border <?= $roleBg ?> inline-block">
                                    <?= $roleLabel ?>
                                </span>
                            </td>

                            <!-- Company (No text cut-off) -->
                            <td class="px-4 py-3.5 align-top">
                                <?php if (!empty($u['company_name'])): ?>
                                    <div class="font-semibold text-slate-800 leading-snug break-words"><?= esc($u['company_name']) ?></div>
                                    <div class="text-[10px] text-purple-700 font-semibold uppercase mt-0.5"><?= esc($u['legal_type'] ?? '') ?></div>
                                <?php else: ?>
                                    <span class="text-slate-400 font-medium">Internal AkuAda Platform</span>
                                <?php endif; ?>
                            </td>

                            <!-- Status -->
                            <td class="px-4 py-3.5 align-top text-center">
                                <?php if ($u['status'] === 'active'): ?>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Ditangguhkan
                                    </span>
                                <?php endif; ?>
                            </td>

                            <!-- Actions -->
                            <td class="px-4 py-3.5 align-top text-center">
                                <div class="inline-flex items-center gap-1">
                                    <button onclick="editUser(<?= htmlspecialchars(json_encode($u), ENT_QUOTES, 'UTF-8') ?>)" 
                                            class="p-1.5 hover:bg-purple-50 text-purple-600 rounded-lg transition border border-purple-200" title="Edit Profil Pengguna">
                                        <span class="material-symbols-outlined text-[17px]">edit</span>
                                    </button>
                                    
                                    <button onclick="openResetPassword(<?= $u['id'] ?>, '<?= esc($u['name']) ?>')" 
                                            class="p-1.5 hover:bg-indigo-50 text-indigo-600 rounded-lg transition border border-indigo-200" title="Reset Kata Sandi">
                                        <span class="material-symbols-outlined text-[17px]">key</span>
                                    </button>

                                    <a href="<?= base_url('admin/users/toggle/' . $u['id']) ?>" 
                                       onclick="return confirm('Ubah status akun <?= esc($u['name']) ?>?');"
                                       class="p-1.5 hover:bg-slate-100 <?= $u['status'] === 'active' ? 'text-amber-600 hover:bg-amber-50 border border-amber-200' : 'text-emerald-600 hover:bg-emerald-50 border border-emerald-200' ?> rounded-lg transition" 
                                       title="<?= $u['status'] === 'active' ? 'Tangguhkan Akun' : 'Aktifkan Akun' ?>">
                                        <span class="material-symbols-outlined text-[17px]"><?= $u['status'] === 'active' ? 'block' : 'check_circle' ?></span>
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

<!-- Modal Buat Akun Baru -->
<div id="modal-add-user" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 text-xs space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <span class="material-symbols-outlined text-purple-600">person_add</span>
                Buat Akun Pengguna Baru
            </h2>
            <button onclick="document.getElementById('modal-add-user').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <form action="<?= base_url('admin/users/store') ?>" method="POST" class="space-y-4">
            <div>
                <label class="block font-bold text-slate-700 mb-1">Nama Lengkap: <span class="text-rose-500">*</span></label>
                <input type="text" name="name" required placeholder="Contoh: Rian Anggara, S.Kom" 
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 font-bold">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Email Login: <span class="text-rose-500">*</span></label>
                    <input type="email" name="email" required placeholder="rian@perusahaan.co.id" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 font-semibold">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nomor Telepon / WA:</label>
                    <input type="text" name="phone" placeholder="081299887766" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Peran Akses (Role): <span class="text-rose-500">*</span></label>
                    <select name="role" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 bg-white font-bold">
                        <option value="customer">Corporate Buyer (Pembeli)</option>
                        <option value="vendor">Mitra Vendor (Supplier)</option>
                        <option value="admin">Superadmin AkuAda</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Jabatan di Perusahaan:</label>
                    <input type="text" name="position" placeholder="Procurement Staff" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600">
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Kaitkan ke Perusahaan (Opsional untuk Admin):</label>
                <select name="company_id" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 bg-white">
                    <option value="">-- Tanpa Perusahaan (Khusus Internal AkuAda) --</option>
                    <?php foreach ($companies as $comp): ?>
                        <option value="<?= $comp['id'] ?>"><?= esc($comp['company_name']) ?> (<?= strtoupper($comp['type']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Kata Sandi Awal: <span class="text-rose-500">*</span></label>
                <input type="text" name="password" value="<?= esc(get_system_setting('default_account_password', 'admin123')) ?>" required 
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 font-mono text-purple-700 font-bold">
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('modal-add-user').classList.add('hidden')" class="px-4 py-2 rounded-xl border border-slate-300 font-bold text-slate-600">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold transition">
                    Simpan Akun Pengguna
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Akun -->
<div id="modal-edit-user" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 text-xs space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <span class="material-symbols-outlined text-purple-600">edit</span>
                Edit Data Pengguna
            </h2>
            <button onclick="document.getElementById('modal-edit-user').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <form id="form-edit-user" action="" method="POST" class="space-y-4">
            <div>
                <label class="block font-bold text-slate-700 mb-1">Nama Lengkap: <span class="text-rose-500">*</span></label>
                <input type="text" name="name" id="edit_user_name" required 
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 font-bold">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Email: <span class="text-rose-500">*</span></label>
                    <input type="email" name="email" id="edit_user_email" required 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 font-semibold">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nomor Telepon:</label>
                    <input type="text" name="phone" id="edit_user_phone" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Role Akses:</label>
                    <select name="role" id="edit_user_role" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 bg-white font-bold">
                        <option value="customer">Corporate Buyer (Pembeli)</option>
                        <option value="vendor">Mitra Vendor (Supplier)</option>
                        <option value="admin">Superadmin AkuAda</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Jabatan:</label>
                    <input type="text" name="position" id="edit_user_position" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600">
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Perusahaan Terkait:</label>
                <select name="company_id" id="edit_user_company_id" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 bg-white">
                    <option value="">-- Tanpa Perusahaan --</option>
                    <?php foreach ($companies as $comp): ?>
                        <option value="<?= $comp['id'] ?>"><?= esc($comp['company_name']) ?> (<?= strtoupper($comp['type']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Status Akun:</label>
                <select name="status" id="edit_user_status" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 bg-white font-bold">
                    <option value="active">Active (Aktif)</option>
                    <option value="suspended">Suspended (Ditangguhkan)</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('modal-edit-user').classList.add('hidden')" class="px-4 py-2 rounded-xl border border-slate-300 font-bold text-slate-600">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Reset Password -->
<div id="modal-reset-password" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl border border-slate-200 text-xs space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <span class="material-symbols-outlined text-purple-600">key</span>
                Reset Kata Sandi
            </h2>
            <button onclick="document.getElementById('modal-reset-password').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <p class="text-slate-600" id="reset_user_text">Reset kata sandi untuk pengguna ini.</p>

        <form id="form-reset-password" action="" method="POST" class="space-y-4">
            <div>
                <label class="block font-bold text-slate-700 mb-1">Kata Sandi Baru: <span class="text-rose-500">*</span></label>
                <input type="text" name="new_password" value="<?= esc(get_system_setting('default_account_password', 'admin123')) ?>" required 
                       class="w-full px-3.5 py-2.5 rounded-xl border border-purple-300 focus:border-purple-600 font-mono text-purple-700 font-bold">
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('modal-reset-password').classList.add('hidden')" class="px-4 py-2 rounded-xl border border-slate-300 font-bold text-slate-600">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold transition">
                    Reset Password
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function editUser(user) {
    document.getElementById('edit_user_name').value = user.name || '';
    document.getElementById('edit_user_email').value = user.email || '';
    document.getElementById('edit_user_phone').value = user.phone || '';
    document.getElementById('edit_user_role').value = user.role || 'customer';
    document.getElementById('edit_user_position').value = user.position || '';
    document.getElementById('edit_user_company_id').value = user.company_id || '';
    document.getElementById('edit_user_status').value = user.status || 'active';
    
    document.getElementById('form-edit-user').action = '<?= base_url('admin/users/update/') ?>' + user.id;
    document.getElementById('modal-edit-user').classList.remove('hidden');
}

function openResetPassword(userId, userName) {
    document.getElementById('reset_user_text').textContent = 'Masukkan kata sandi baru untuk ' + userName + '.';
    document.getElementById('form-reset-password').action = '<?= base_url('admin/users/reset-password/') ?>' + userId;
    document.getElementById('modal-reset-password').classList.remove('hidden');
}
</script>

<?= $this->endSection() ?>
