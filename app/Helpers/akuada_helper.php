<?php

if (!function_exists('generate_unique_number')) {
    /**
     * Generate nomor dokumen unik berbasis database sequence + microsecond.
     * Menghindari duplikat nomor order/invoice dibanding rand().
     *
     * @param string $prefix Prefix dokumen: ORD, INV, VINV, BAST
     * @param string $table  Tabel database untuk hitung sequence bulan ini
     * @return string Contoh: ORD-202611-00001842
     */
    function generate_unique_number(string $prefix, string $table = 'orders'): string
    {
        $db = \Config\Database::connect();
        $yearMonth = date('Ym');
        $count = (int)$db->table($table)
            ->where("DATE_FORMAT(created_at, '%Y%m')", $yearMonth)
            ->countAllResults() + 1;
        // 3 digit microsecond suffix untuk anti-collision di concurrent request
        $micro = str_pad((string)((int)(microtime(true) * 1000) % 1000), 3, '0', STR_PAD_LEFT);
        return $prefix . '-' . $yearMonth . '-' . sprintf('%04d', $count) . $micro;
    }
}

if (!function_exists('format_rupiah')) {
    /**
     * Format number to Indonesian Rupiah currency
     */
    function format_rupiah($amount, bool $withPrefix = true): string
    {
        $formatted = number_format((float)$amount, 0, ',', '.');
        return $withPrefix ? 'Rp ' . $formatted : $formatted;
    }
}

if (!function_exists('get_system_setting')) {
    /**
     * Get system setting value from database or config fallback
     */
    function get_system_setting(string $key, $default = null, bool $forceReload = false)
    {
        static $cachedSettings = null;
        if ($cachedSettings === null || $forceReload) {
            $db = \Config\Database::connect();
            $rows = $db->table('system_settings')->get()->getResultArray();
            $cachedSettings = [];
            foreach ($rows as $r) {
                $cachedSettings[$r['setting_key']] = $r['setting_value'];
            }
        }

        return $cachedSettings[$key] ?? $default;
    }
}

if (!function_exists('get_ppn_rate')) {
    /**
     * Get active PPN tax rate (%) from system settings
     */
    function get_ppn_rate(): float
    {
        $setting = get_system_setting('ppn_rate');
        if ($setting !== null && is_numeric($setting)) {
            return (float)$setting;
        }
        $config = config('AkuAda');
        return (float)($config->ppnRate ?? 11.0);
    }
}

if (!function_exists('get_pph_22_rate')) {
    /**
     * Get active PPh 22 tax rate (%) from system settings
     */
    function get_pph_22_rate(): float
    {
        $setting = get_system_setting('pph_22_rate');
        if ($setting !== null && is_numeric($setting)) {
            return (float)$setting;
        }
        $config = config('AkuAda');
        return (float)($config->pph22Rate ?? 1.5);
    }
}

if (!function_exists('format_tax_rate')) {
    /**
     * Format tax percentage cleanly (e.g. 11 -> "11%", 11.5 -> "11.5%")
     */
    function format_tax_rate($rate = null): string
    {
        $val = ($rate !== null && is_numeric($rate)) ? (float)$rate : get_ppn_rate();
        $formatted = rtrim(rtrim(number_format($val, 2, '.', ''), '0'), '.');
        return ($formatted === '' ? '0' : $formatted) . '%';
    }
}

if (!function_exists('badge_order_status')) {
    /**
     * Generate HTML badge for B2B order status
     */
    function badge_order_status(string $status): string
    {
        $badges = [
            'po_issued'           => ['bg' => 'bg-amber-100 text-amber-900 border-amber-300', 'label' => 'Penerbitan PO', 'icon' => 'receipt_long'],
            'pending_po_approval' => ['bg' => 'bg-amber-100 text-amber-900 border-amber-300', 'label' => 'Menunggu Approval PO', 'icon' => 'receipt_long'],
            'po_approved'         => ['bg' => 'bg-blue-100 text-blue-900 border-blue-300', 'label' => 'PO Dikonfirmasi', 'icon' => 'verified'],
            'in_production'       => ['bg' => 'bg-purple-100 text-purple-900 border-purple-300', 'label' => 'Dalam Produksi/Penyiapan', 'icon' => 'precision_manufacturing'],
            'processing'          => ['bg' => 'bg-purple-100 text-purple-900 border-purple-300', 'label' => 'Dalam Produksi', 'icon' => 'precision_manufacturing'],
            'quality_control'     => ['bg' => 'bg-indigo-100 text-indigo-900 border-indigo-300', 'label' => 'Pemeriksaan QC', 'icon' => 'fact_check'],
            'packaging'           => ['bg' => 'bg-pink-100 text-pink-900 border-pink-300', 'label' => 'Pengemasan & Paletisasi', 'icon' => 'inventory_2'],
            'shipped'             => ['bg' => 'bg-sky-100 text-sky-900 border-sky-300', 'label' => 'Dalam Pengiriman (Surat Jalan)', 'icon' => 'local_shipping'],
            'delivered'           => ['bg' => 'bg-violet-100 text-violet-900 border-violet-300', 'label' => 'Tiba di Lokasi Buyer', 'icon' => 'where_to_vote'],
            'bast_signed'         => ['bg' => 'bg-emerald-100 text-emerald-900 border-emerald-300', 'label' => 'BAST Disahkan', 'icon' => 'assignment_turned_in'],
            'completed'           => ['bg' => 'bg-emerald-100 text-emerald-900 border-emerald-300', 'label' => 'Selesai & Lunas', 'icon' => 'check_circle'],
            'cancelled'           => ['bg' => 'bg-rose-100 text-rose-900 border-rose-300', 'label' => 'Dibatalkan', 'icon' => 'cancel'],
        ];

        $b = $badges[$status] ?? ['bg' => 'bg-slate-100 text-slate-800 border-slate-200', 'label' => ucfirst($status), 'icon' => 'info'];
        return '<span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-full border ' . $b['bg'] . '"><span class="material-symbols-outlined text-[14px] leading-none">' . $b['icon'] . '</span>' . $b['label'] . '</span>';
    }
}

if (!function_exists('render_order_progress_stepper')) {
    /**
     * Render responsive B2B Order Progress Stepper / Timeline
     */
    function render_order_progress_stepper(string $currentStatus): string
    {
        $steps = [
            'po_issued'       => ['title' => 'Penerbitan PO', 'sub' => 'PO Terbit', 'icon' => 'receipt_long', 'rank' => 1],
            'po_approved'     => ['title' => 'PO Dikonfirmasi', 'sub' => 'Disetujui Vendor', 'icon' => 'verified', 'rank' => 2],
            'in_production'   => ['title' => 'Produksi / Penyiapan', 'sub' => 'Assembling / Gudang', 'icon' => 'precision_manufacturing', 'rank' => 3],
            'quality_control' => ['title' => 'Pemeriksaan Mutu (QC)', 'sub' => 'Uji SNI / Standar', 'icon' => 'fact_check', 'rank' => 4],
            'packaging'       => ['title' => 'Pengemasan', 'sub' => 'Palet & Segel', 'icon' => 'inventory_2', 'rank' => 5],
            'shipped'         => ['title' => 'Pengiriman', 'sub' => 'Surat Jalan Aktif', 'icon' => 'local_shipping', 'rank' => 6],
            'delivered'       => ['title' => 'Tiba di Lokasi', 'sub' => 'Diterima Logistik', 'icon' => 'where_to_vote', 'rank' => 7],
            'bast_signed'     => ['title' => 'Pengesahan BAST', 'sub' => 'BAST Sah & Selesai', 'icon' => 'assignment_turned_in', 'rank' => 8],
        ];

        // Map legacy statuses
        $statusAliases = [
            'pending_po_approval' => 'po_issued',
            'processing'          => 'in_production',
            'completed'           => 'bast_signed',
        ];
        $normStatus = $statusAliases[$currentStatus] ?? $currentStatus;
        $currentRank = $steps[$normStatus]['rank'] ?? 1;

        if ($currentStatus === 'cancelled') {
            return '<div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl text-rose-800 text-xs font-bold flex items-center gap-2"><span class="material-symbols-outlined text-rose-600">cancel</span> Pesanan PO Dibatalkan.</div>';
        }

        $html = '<div class="w-full py-2">';
        $html .= '<div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-2 relative">';

        foreach ($steps as $key => $s) {
            $isPast = $s['rank'] < $currentRank;
            $isCurrent = $s['rank'] === $currentRank;

            if ($isPast) {
                $circleBg = 'bg-emerald-600 text-white shadow-sm ring-4 ring-emerald-50';
                $textColor = 'text-slate-800 font-bold';
                $subColor = 'text-emerald-700 font-medium';
                $icon = 'check_circle';
            } elseif ($isCurrent) {
                $circleBg = 'bg-gradient-to-tr from-purple-700 via-blue-600 to-pink-500 text-white shadow-lg shadow-purple-500/30 ring-4 ring-purple-100 animate-pulse';
                $textColor = 'text-purple-950 font-black';
                $subColor = 'text-purple-700 font-bold';
                $icon = $s['icon'];
            } else {
                $circleBg = 'bg-slate-100 text-slate-400 border border-slate-200';
                $textColor = 'text-slate-400 font-semibold';
                $subColor = 'text-slate-400';
                $icon = $s['icon'];
            }

            $html .= '<div class="flex flex-col items-center text-center p-2 rounded-xl transition ' . ($isCurrent ? 'bg-purple-50/60 border border-purple-200' : '') . '">';
            $html .= '  <div class="w-10 h-10 rounded-full flex items-center justify-center text-lg ' . $circleBg . ' mb-2">';
            $html .= '      <span class="material-symbols-outlined text-[20px]">' . $icon . '</span>';
            $html .= '  </div>';
            $html .= '  <span class="text-xs leading-tight ' . $textColor . '">' . $s['title'] . '</span>';
            $html .= '  <span class="text-[10px] mt-0.5 ' . $subColor . '">' . $s['sub'] . '</span>';
            if ($isCurrent) {
                $html .= '  <span class="mt-1 px-1.5 py-0.5 rounded bg-pink-500 text-white text-[9px] font-bold uppercase tracking-wider">Proses Ini</span>';
            }
            $html .= '</div>';
        }

        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }
}

if (!function_exists('get_allowed_top_terms')) {
    /**
     * Return array of allowed TOP day integers from system settings (e.g. [14, 30, 60, 90])
     */
    function get_allowed_top_terms(): array
    {
        $raw = (string)get_system_setting('allowed_top_terms', '14,30,60,90');
        $parts = array_filter(array_map('intval', explode(',', $raw)), fn($v) => $v > 0);
        if (empty($parts)) {
            return [14, 30, 60, 90];
        }
        sort($parts);
        return array_values(array_unique($parts));
    }
}

if (!function_exists('get_max_top_days')) {
    function get_max_top_days(): int
    {
        $terms = get_allowed_top_terms();
        return !empty($terms) ? max($terms) : 90;
    }
}

if (!function_exists('get_min_top_days')) {
    function get_min_top_days(): int
    {
        $terms = get_allowed_top_terms();
        return !empty($terms) ? min($terms) : 14;
    }
}

if (!function_exists('get_navbar_categories')) {
    /**
     * Get categories dynamically from database for navbar popover & footer links
     */
    function get_navbar_categories(): array
    {
        static $cats = null;
        if ($cats === null) {
            try {
                $db = \Config\Database::connect();
                $cats = $db->table('categories')->orderBy('sort_order', 'ASC')->get()->getResultArray();
            } catch (\Throwable $e) {
                $cats = [];
            }
        }
        return $cats;
    }
}

if (!function_exists('get_active_buyer_credit_info')) {
    /**
     * Get live credit limit, used credit, and company name from DB for customer layout sidebar
     */
    function get_active_buyer_credit_info(): array
    {
        $defaultLimit = (float)get_system_setting('default_credit_limit', 100000000);
        try {
            $companyId = session('company_id');
            $companyModel = new \App\Models\CompanyModel();
            if ($companyId) {
                $comp = $companyModel->find($companyId);
                if ($comp && ($comp['type'] ?? '') === 'buyer') {
                    return [
                        'company_name'     => $comp['company_name'],
                        'credit_limit_top' => (float)$comp['credit_limit_top'],
                        'credit_used_top'  => (float)$comp['credit_used_top'],
                    ];
                }
            }
            $firstBuyer = $companyModel->where('type', 'buyer')->orderBy('id', 'ASC')->first();
            if ($firstBuyer) {
                return [
                    'company_name'     => $firstBuyer['company_name'],
                    'credit_limit_top' => (float)$firstBuyer['credit_limit_top'],
                    'credit_used_top'  => (float)$firstBuyer['credit_used_top'],
                ];
            }
        } catch (\Throwable $e) {
            // Fallback below
        }

        return [
            'company_name'     => session('company_name') ?? 'Perusahaan Pembeli',
            'credit_limit_top' => (float)(session('credit_limit_top') ?? $defaultLimit),
            'credit_used_top'  => (float)(session('credit_used_top') ?? 0),
        ];
    }
}

if (!function_exists('badge_payment_method')) {
    /**
     * Render payment method badge
     */
    function badge_payment_method(string $method): string
    {
        $methods = [
            'top_14'       => ['label' => 'TOP 14 Hari', 'color' => 'bg-purple-50 text-purple-700 border-purple-200'],
            'top_30'       => ['label' => 'TOP 30 Hari', 'color' => 'bg-indigo-50 text-indigo-700 border-indigo-200'],
            'top_60'       => ['label' => 'TOP 60 Hari', 'color' => 'bg-blue-50 text-blue-700 border-blue-200'],
            'top_90'       => ['label' => 'TOP 90 Hari', 'color' => 'bg-sky-50 text-sky-700 border-sky-200'],
            'corporate_po' => ['label' => 'PO On-Account', 'color' => 'bg-pink-50 text-pink-700 border-pink-200'],
            'bank_va'      => ['label' => 'Virtual Account', 'color' => 'bg-slate-50 text-slate-700 border-slate-200'],
            'escrow'       => ['label' => 'Escrow AkuAda', 'color' => 'bg-emerald-50 text-emerald-700 border-emerald-200'],
        ];

        $m = $methods[$method] ?? ['label' => strtoupper($method), 'color' => 'bg-gray-100 text-gray-800 border-gray-200'];
        return '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium border ' . $m['color'] . '">' . $m['label'] . '</span>';
    }
}

if (!function_exists('get_default_vendor_fee_rate')) {
    /**
     * Get active vendor fee rate from database settings or config
     */
    function get_default_vendor_fee_rate(): float
    {
        $setting = get_system_setting('vendor_fee_default_percent');
        if ($setting !== null && is_numeric($setting)) {
            return (float)$setting;
        }

        $config = config('AkuAda');
        return $config->defaultVendorFeeRate ?? 2.5;
    }
}

if (!function_exists('calculate_vendor_fee')) {
    /**
     * Calculate marketplace platform fee from vendor for a product item
     */
    function calculate_vendor_fee(float $subtotal, ?float $customPercent = null, float $ppnAmount = 0.0): array
    {
        $percent = ($customPercent !== null && $customPercent >= 0) ? $customPercent : get_default_vendor_fee_rate();
        $basisSetting = get_system_setting('vendor_fee_calc_basis', 'dpp');
        $basisAmount = ($basisSetting === 'gross') ? ($subtotal + $ppnAmount) : $subtotal;
        $feeAmount = round($basisAmount * ($percent / 100), 2);
        
        $minFee = (float)get_system_setting('vendor_fee_min_amount', 0);
        if ($minFee > 0 && $feeAmount < $minFee) {
            $feeAmount = $minFee;
        }

        $netAmount = max(0, $subtotal - $feeAmount);

        return [
            'percent'    => $percent,
            'fee_amount' => $feeAmount,
            'net_amount' => $netAmount,
        ];
    }
}

if (!function_exists('recalculate_order_from_settings')) {
    /**
     * Dynamically recalculate an order (and its items if present) using active system settings (ppn_rate, vendor_fee, etc.)
     */
    function recalculate_order_from_settings(array $order): array
    {
        $ppnRate = get_ppn_rate();
        $defaultVendorFee = get_default_vendor_fee_rate();
        $order['ppn_rate'] = $ppnRate;

        if (!empty($order['items']) && is_array($order['items'])) {
            $calcSubtotal = 0.0;
            $calcPpnTotal = 0.0;
            $calcFeeTotal = 0.0;
            $calcNetTotal = 0.0;

            foreach ($order['items'] as &$it) {
                $itSubtotal = (float)($it['subtotal'] ?? ((float)$it['unit_price'] * (int)$it['qty']));
                $itPpn = round($itSubtotal * ($ppnRate / 100), 2);
                $itFeePercent = (isset($it['vendor_fee_percent']) && (float)$it['vendor_fee_percent'] > 0)
                    ? (float)$it['vendor_fee_percent']
                    : $defaultVendorFee;
                $feeCalc = calculate_vendor_fee($itSubtotal, $itFeePercent, $itPpn);

                $it['subtotal']           = $itSubtotal;
                $it['ppn_percent']        = $ppnRate;
                $it['ppn_amount']         = $itPpn;
                $it['vendor_fee_percent'] = $feeCalc['percent'];
                $it['vendor_fee_amount']  = $feeCalc['fee_amount'];
                $it['vendor_net_amount']  = $feeCalc['net_amount'];

                $calcSubtotal += $itSubtotal;
                $calcPpnTotal += $itPpn;
                $calcFeeTotal += $feeCalc['fee_amount'];
                $calcNetTotal += $feeCalc['net_amount'];
            }
            unset($it);

            $defaultShip = (float)get_system_setting('default_shipping_amount', 0);
            $shipAmt = ((float)($order['shipping_amount'] ?? 0) > 0) ? (float)$order['shipping_amount'] : $defaultShip;
            $order['shipping_amount']   = $shipAmt;
            $order['subtotal']          = $calcSubtotal;
            $order['ppn_amount']        = $calcPpnTotal;
            $order['total_vendor_fee']  = $calcFeeTotal;
            $order['vendor_net_payout'] = $calcNetTotal;
            $order['total_amount']      = $calcSubtotal + $calcPpnTotal + $shipAmt;
        } else {
            $subtotal = (float)($order['subtotal'] ?? 0);
            $ppnAmount = round($subtotal * ($ppnRate / 100), 2);
            $feeRate = (isset($order['vendor_commission_rate']) && (float)$order['vendor_commission_rate'] > 0)
                ? (float)$order['vendor_commission_rate']
                : $defaultVendorFee;
            $feeCalc = calculate_vendor_fee($subtotal, $feeRate, $ppnAmount);
            $defaultShip = (float)get_system_setting('default_shipping_amount', 0);
            $shipAmt = ((float)($order['shipping_amount'] ?? 0) > 0) ? (float)$order['shipping_amount'] : $defaultShip;

            $order['shipping_amount']   = $shipAmt;
            $order['ppn_amount']        = $ppnAmount;
            $order['total_vendor_fee']  = $feeCalc['fee_amount'];
            $order['vendor_net_payout'] = $feeCalc['net_amount'];
            $order['total_amount']      = $subtotal + $ppnAmount + $shipAmt;
        }

        return $order;
    }
}

if (!function_exists('sync_orders_with_system_settings')) {
    /**
     * Synchronize products, order_items, and orders tables in database whenever system settings (PPN, vendor fee) change
     */
    function sync_orders_with_system_settings(): void
    {
        // Force reload settings cache
        get_system_setting('ppn_rate', 11, true);
        $ppnRate = get_ppn_rate();
        $defaultFee = get_default_vendor_fee_rate();

        $db = \Config\Database::connect();

        // 1. Update all taxable products' ppn_percent to active ppn_rate
        $db->table('products')->where('is_taxable', 1)->update(['ppn_percent' => $ppnRate]);

        // 2. Recalculate all order_items and orders
        $orders = $db->table('orders')->get()->getResultArray();
        foreach ($orders as $ord) {
            $items = $db->table('order_items')->where('order_id', $ord['id'])->get()->getResultArray();
            $ordSubtotal = 0.0;
            $ordPpn = 0.0;
            $ordFee = 0.0;
            $ordNet = 0.0;

            foreach ($items as $it) {
                $itSubtotal = (float)$it['subtotal'];
                $itPpn = round($itSubtotal * ($ppnRate / 100), 2);
                $itFeePct = ((float)$it['vendor_fee_percent'] > 0) ? (float)$it['vendor_fee_percent'] : $defaultFee;
                $feeCalc = calculate_vendor_fee($itSubtotal, $itFeePct, $itPpn);

                $db->table('order_items')->where('id', $it['id'])->update([
                    'ppn_amount'         => $itPpn,
                    'vendor_fee_percent' => $feeCalc['percent'],
                    'vendor_fee_amount'  => $feeCalc['fee_amount'],
                    'vendor_net_amount'  => $feeCalc['net_amount'],
                ]);

                $ordSubtotal += $itSubtotal;
                $ordPpn += $itPpn;
                $ordFee += $feeCalc['fee_amount'];
                $ordNet += $feeCalc['net_amount'];
            }

            if (empty($items)) {
                $ordSubtotal = (float)$ord['subtotal'];
                $ordPpn = round($ordSubtotal * ($ppnRate / 100), 2);
                $feeCalc = calculate_vendor_fee($ordSubtotal, $defaultFee, $ordPpn);
                $ordFee = $feeCalc['fee_amount'];
                $ordNet = $feeCalc['net_amount'];
            }

            $defaultShip = (float)get_system_setting('default_shipping_amount', 0);
            $shipAmt = ((float)($ord['shipping_amount'] ?? 0) > 0) ? (float)$ord['shipping_amount'] : $defaultShip;
            $ordTotal = $ordSubtotal + $ordPpn + $shipAmt;
            $db->table('orders')->where('id', $ord['id'])->update([
                'subtotal'          => $ordSubtotal,
                'ppn_amount'        => $ordPpn,
                'total_vendor_fee'  => $ordFee,
                'vendor_net_payout' => $ordNet,
                'shipping_amount'   => $shipAmt,
                'total_amount'      => $ordTotal,
            ]);
        }
    }
}

if (!function_exists('terbilang')) {
    /**
     * Convert number to Indonesian words
     */
    function terbilang($number): string
    {
        $words = ["", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas"];
        $number = abs((float)$number);
        
        if ($number < 12) {
            return " " . $words[(int)$number];
        } elseif ($number < 20) {
            return terbilang($number - 10) . " Belas";
        } elseif ($number < 100) {
            return terbilang(floor($number / 10)) . " Puluh" . terbilang(fmod($number, 10));
        } elseif ($number < 200) {
            return " Seratus" . terbilang($number - 100);
        } elseif ($number < 1000) {
            return terbilang(floor($number / 100)) . " Ratus" . terbilang(fmod($number, 100));
        } elseif ($number < 2000) {
            return " Seribu" . terbilang($number - 1000);
        } elseif ($number < 1000000) {
            return terbilang(floor($number / 1000)) . " Ribu" . terbilang(fmod($number, 1000));
        } elseif ($number < 1000000000) {
            return terbilang(floor($number / 1000000)) . " Juta" . terbilang(fmod($number, 1000000));
        } elseif ($number < 1000000000000) {
            return terbilang(floor($number / 1000000000)) . " Milyar" . terbilang(fmod($number, 1000000000));
        } elseif ($number < 1000000000000000) {
            return terbilang(floor($number / 1000000000000)) . " Triliun" . terbilang(fmod($number, 1000000000000));
        }
        return "";
    }
}

if (!function_exists('terbilang_rupiah')) {
    /**
     * Convert amount to Indonesian Rupiah words
     */
    function terbilang_rupiah($amount): string
    {
        $res = trim(preg_replace('/\s+/', ' ', terbilang($amount)));
        return $res ? $res . ' Rupiah' : 'Nol Rupiah';
    }
}

if (!function_exists('ensure_role_session')) {
    /**
     * Auto-bypass session into the active buyer/vendor account from database
     */
    function ensure_role_session(string $role): int
    {
        $companyType = ($role === 'vendor') ? 'vendor' : 'buyer';
        $companyModel = new \App\Models\CompanyModel();
        $userModel = new \App\Models\UserModel();

        // Jika sudah aktif di role yang sama dan company_id valid dengan tipe tersebut, langsung kembalikan
        if (session('user_role') === $role && session('company_id')) {
            $existing = $companyModel->find(session('company_id'));
            if ($existing && $existing['type'] === $companyType) {
                return (int)$existing['id'];
            }
        }

        // Ambil data company untuk tipe ini (jika masih 1 data / data pertama untuk bypass)
        $companies = $companyModel->where('type', $companyType)->orderBy('id', 'ASC')->findAll();
        $company = $companies[0] ?? null;

        $foundUser = null;
        if ($company) {
            $foundUser = $userModel->where('company_id', $company['id'])->where('role', $role)->first();
        }
        if (!$foundUser) {
            $foundUser = $userModel->where('role', $role)->orderBy('id', 'ASC')->first();
        }

        $user = $foundUser ? $userModel->getUserWithCompany($foundUser['id']) : null;

        if (!$user && $company) {
            $user = [
                'id'               => 0,
                'name'             => ($company['pic_name'] ?: $company['company_name']),
                'email'            => $company['email'],
                'role'             => $role,
                'avatar'           => null,
                'company_id'       => $company['id'],
                'company_name'     => $company['company_name'],
                'legal_type'       => $company['legal_type'],
                'npwp'             => $company['npwp'],
                'nib'              => $company['nib'],
                'commission_rate'  => $company['commission_rate'],
                'credit_limit_top' => $company['credit_limit_top'],
                'credit_used_top'  => $company['credit_used_top'],
            ];
        }

        if ($user && !empty($user['company_id'])) {
            session()->set([
                'user_id'          => $user['id'],
                'user_name'        => $user['name'],
                'user_email'       => $user['email'],
                'user_role'        => $user['role'],
                'user_avatar'      => $user['avatar'] ?? null,
                'company_id'       => $user['company_id'],
                'company_name'     => $user['company_name'],
                'company_legal'    => $user['legal_type'] ?? 'PT',
                'company_npwp'     => $user['npwp'] ?? null,
                'company_nib'      => $user['nib'] ?? null,
                'commission_rate'  => (float)($user['commission_rate'] ?? 2.50),
                'credit_limit_top' => (float)($user['credit_limit_top'] ?? 0),
                'credit_used_top'  => (float)($user['credit_used_top'] ?? 0),
            ]);
            return (int)$user['company_id'];
        }

        return (int)(session('company_id') ?? 1);
    }
}

if (!function_exists('classify_aging_bucket')) {
    /**
     * Classify an unpaid invoice/payable into Current, 30, 60, 90, or 120+ day aging buckets
     */
    function classify_aging_bucket(?string $dueDate, ?string $referenceDate = null): array
    {
        $ref = $referenceDate ?: date('Y-m-d');
        $due = $dueDate ?: $ref;
        $daysOverdue = (int)floor((strtotime($ref) - strtotime($due)) / 86400);

        if ($daysOverdue <= 0) {
            return [
                'key'          => 'current',
                'label'        => 'Current',
                'sublabel'     => 'Belum Jatuh Tempo',
                'days_overdue' => $daysOverdue,
                'badge_class'  => 'bg-emerald-50 text-emerald-800 border-emerald-200',
            ];
        } elseif ($daysOverdue <= 30) {
            return [
                'key'          => 'days_30',
                'label'        => '30 Hari',
                'sublabel'     => '1 â€“ 30 Hari',
                'days_overdue' => $daysOverdue,
                'badge_class'  => 'bg-amber-50 text-amber-800 border-amber-300',
            ];
        } elseif ($daysOverdue <= 60) {
            return [
                'key'          => 'days_60',
                'label'        => '60 Hari',
                'sublabel'     => '31 â€“ 60 Hari',
                'days_overdue' => $daysOverdue,
                'badge_class'  => 'bg-orange-50 text-orange-800 border-orange-300',
            ];
        } elseif ($daysOverdue <= 90) {
            return [
                'key'          => 'days_90',
                'label'        => '90 Hari',
                'sublabel'     => '61 â€“ 90 Hari',
                'days_overdue' => $daysOverdue,
                'badge_class'  => 'bg-rose-50 text-rose-800 border-rose-300',
            ];
        }

        return [
            'key'          => 'days_120',
            'label'        => '120 Hari',
            'sublabel'     => '91 â€“ 120+ Hari',
            'days_overdue' => $daysOverdue,
            'badge_class'  => 'bg-red-100 text-red-900 border-red-400',
        ];
    }
}

if (!function_exists('calculate_aging_summary')) {
    /**
     * Build complete AR/AP Aging Schedule (Current, 30, 60, 90, 120 days) and per-company breakdown
     *
     * @param array       $orders       List of orders
     * @param string      $mode         'buyer' (Piutang Buyer -> AkuAda) or 'vendor' (Hutang AkuAda -> Vendor)
     * @param array       $companies    Optional master list of companies to include even if Rp 0
     * @param string|null $groupByField Optional override field ('buyer_name' or 'vendor_name')
     */
    function calculate_aging_summary(array $orders, string $mode = 'buyer', array $companies = [], ?string $groupByField = null): array
    {
        $today = date('Y-m-d');
        $buckets = [
            'current'  => [
                'key'      => 'current',
                'title'    => 'Current',
                'range'    => 'Belum Jatuh Tempo',
                'amount'   => 0.0,
                'count'    => 0,
            ],
            'days_30'  => [
                'key'      => 'days_30',
                'title'    => '30 Hari',
                'range'    => '1 â€“ 30 Hari',
                'amount'   => 0.0,
                'count'    => 0,
            ],
            'days_60'  => [
                'key'      => 'days_60',
                'title'    => '60 Hari',
                'range'    => '31 â€“ 60 Hari',
                'amount'   => 0.0,
                'count'    => 0,
            ],
            'days_90'  => [
                'key'      => 'days_90',
                'title'    => '90 Hari',
                'range'    => '61 â€“ 90 Hari',
                'amount'   => 0.0,
                'count'    => 0,
            ],
            'days_120' => [
                'key'      => 'days_120',
                'title'    => '120 Hari',
                'range'    => '> 90 / 120 Hari',
                'amount'   => 0.0,
                'count'    => 0,
            ],
        ];

        $byEntity = [];
        foreach ($companies as $c) {
            $cName = $c['company_name'] ?? '';
            if ($cName !== '' && !isset($byEntity[$cName])) {
                $byEntity[$cName] = [
                    'name'     => $cName,
                    'current'  => 0.0,
                    'days_30'  => 0.0,
                    'days_60'  => 0.0,
                    'days_90'  => 0.0,
                    'days_120' => 0.0,
                    'total'    => 0.0,
                    'count'    => 0,
                ];
            }
        }

        $totalOutstanding = 0.0;
        $totalCount = 0;

        foreach ($orders as $o) {
            if ($mode === 'vendor') {
                $isPaid = (($o['vendor_payout_status'] ?? 'unpaid') === 'paid');
                $fee = (float)($o['total_vendor_fee'] ?? 0);
                $amount = (float)($o['vendor_net_payout'] ?? ($o['subtotal'] - $fee)) + (float)($o['ppn_amount'] ?? 0);
                $dueDate = $o['vendor_payout_due_date'] ?: date('Y-m-d', strtotime($o['created_at'] . ' +7 days'));
                $defaultField = 'vendor_name';
            } else {
                $isPaid = (($o['payment_status'] ?? 'unpaid') === 'paid');
                $amount = (float)($o['total_amount'] ?? 0);
                $dueDate = $o['due_date'] ?: date('Y-m-d', strtotime($o['created_at'] . ' +30 days'));
                $defaultField = 'buyer_name';
            }

            if ($isPaid) {
                continue;
            }

            $field = $groupByField ?: $defaultField;
            $entityName = $o[$field] ?? ($mode === 'vendor' ? 'Mitra Vendor' : 'Corporate Buyer');

            $bucketInfo = classify_aging_bucket($dueDate, $today);
            $bKey = $bucketInfo['key'];

            $buckets[$bKey]['amount'] += $amount;
            $buckets[$bKey]['count']++;
            $totalOutstanding += $amount;
            $totalCount++;

            if (!isset($byEntity[$entityName])) {
                $byEntity[$entityName] = [
                    'name'     => $entityName,
                    'current'  => 0.0,
                    'days_30'  => 0.0,
                    'days_60'  => 0.0,
                    'days_90'  => 0.0,
                    'days_120' => 0.0,
                    'total'    => 0.0,
                    'count'    => 0,
                ];
            }

            $byEntity[$entityName][$bKey] += $amount;
            $byEntity[$entityName]['total'] += $amount;
            $byEntity[$entityName]['count']++;
        }

        foreach ($buckets as &$b) {
            $b['percent'] = $totalOutstanding > 0 ? round(($b['amount'] / $totalOutstanding) * 100, 1) : 0;
        }
        unset($b);

        return [
            'buckets'           => $buckets,
            'by_entity'         => array_values($byEntity),
            'total_outstanding' => $totalOutstanding,
            'total_count'       => $totalCount,
        ];
    }
}

if (!function_exists('render_aging_table')) {
    /**
     * Render standardized Aging Schedule table with columns:
     * Nama Perusahaan | Current | 30 | 60 | 90 | 120 | Total
     */
    function render_aging_table(
        array $aging,
        string $title,
        string $subtitle,
        string $companyColLabel = 'Nama Perusahaan',
        string $accentColor = 'purple'
    ): string {
        $rows = $aging['by_entity'] ?? [];
        $buckets = $aging['buckets'] ?? [];
        $grandTotal = (float)($aging['total_outstanding'] ?? 0);

        $dotClass = match ($accentColor) {
            'blue'  => 'bg-blue-600',
            'pink'  => 'bg-pink-600',
            default => 'bg-purple-600',
        };

        $html = '<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">';
        $html .= '  <div class="px-6 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2">';
        $html .= '    <div class="flex items-center gap-2.5">';
        $html .= '      <span class="w-2.5 h-2.5 rounded-full ' . $dotClass . '"></span>';
        $html .= '      <div>';
        $html .= '        <h3 class="font-bold text-slate-900 text-sm">' . esc($title) . '</h3>';
        $html .= '        <p class="text-[11px] text-slate-500">' . esc($subtitle) . '</p>';
        $html .= '      </div>';
        $html .= '    </div>';
        $html .= '    <div class="flex items-center gap-2 text-[11px]">';
        $html .= '      <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 font-semibold">Total Outstanding: <strong class="font-mono text-slate-900">' . format_rupiah($grandTotal) . '</strong></span>';
        $html .= '    </div>';
        $html .= '  </div>';

        $html .= '  <div class="overflow-x-auto">';
        $html .= '    <table class="w-full text-xs text-left border-collapse">';
        $html .= '      <thead class="bg-slate-900 text-white uppercase text-[11px] tracking-wider font-bold">';
        $html .= '        <tr>';
        $html .= '          <th class="px-4 py-3.5">' . esc($companyColLabel) . '</th>';
        $html .= '          <th class="px-4 py-3.5 text-right bg-emerald-950/60">Current<span class="block text-[9px] font-normal text-emerald-300 normal-case">Belum Jatuh Tempo</span></th>';
        $html .= '          <th class="px-4 py-3.5 text-right bg-amber-950/60">30 Hari<span class="block text-[9px] font-normal text-amber-300 normal-case">1 â€“ 30 Hari</span></th>';
        $html .= '          <th class="px-4 py-3.5 text-right bg-orange-950/60">60 Hari<span class="block text-[9px] font-normal text-orange-300 normal-case">31 â€“ 60 Hari</span></th>';
        $html .= '          <th class="px-4 py-3.5 text-right bg-rose-950/60">90 Hari<span class="block text-[9px] font-normal text-rose-300 normal-case">61 â€“ 90 Hari</span></th>';
        $html .= '          <th class="px-4 py-3.5 text-right bg-red-950/80">120 Hari<span class="block text-[9px] font-normal text-red-300 normal-case">&gt; 90 / 120 Hari</span></th>';
        $html .= '          <th class="px-4 py-3.5 text-right bg-slate-800">Total</th>';
        $html .= '        </tr>';
        $html .= '      </thead>';
        $html .= '      <tbody class="divide-y divide-slate-100 bg-white">';

        if (empty($rows)) {
            $html .= '        <tr><td colspan="7" class="px-6 py-8 text-center text-slate-400">Tidak ada saldo aktif pada periode ini.</td></tr>';
        } else {
            foreach ($rows as $r) {
                $html .= '        <tr class="hover:bg-slate-50/80 transition-colors">';
                $html .= '          <td class="px-4 py-3.5 font-bold text-slate-900">';
                $html .= '            <div>' . esc($r['name']) . '</div>';
                $html .= '            <span class="text-[10px] font-normal text-slate-400">' . (int)$r['count'] . ' tagihan belum lunas</span>';
                $html .= '          </td>';
                $html .= '          <td class="px-4 py-3.5 text-right font-mono ' . ($r['current'] > 0 ? 'font-bold text-emerald-700 bg-emerald-50/30' : 'text-slate-400') . '">' . format_rupiah($r['current']) . '</td>';
                $html .= '          <td class="px-4 py-3.5 text-right font-mono ' . ($r['days_30'] > 0 ? 'font-bold text-amber-700 bg-amber-50/30' : 'text-slate-400') . '">' . format_rupiah($r['days_30']) . '</td>';
                $html .= '          <td class="px-4 py-3.5 text-right font-mono ' . ($r['days_60'] > 0 ? 'font-bold text-orange-700 bg-orange-50/30' : 'text-slate-400') . '">' . format_rupiah($r['days_60']) . '</td>';
                $html .= '          <td class="px-4 py-3.5 text-right font-mono ' . ($r['days_90'] > 0 ? 'font-bold text-rose-700 bg-rose-50/30' : 'text-slate-400') . '">' . format_rupiah($r['days_90']) . '</td>';
                $html .= '          <td class="px-4 py-3.5 text-right font-mono ' . ($r['days_120'] > 0 ? 'font-extrabold text-red-700 bg-red-50/40' : 'text-slate-400') . '">' . format_rupiah($r['days_120']) . '</td>';
                $html .= '          <td class="px-4 py-3.5 text-right font-mono font-black text-slate-900 bg-slate-50/60">' . format_rupiah($r['total']) . '</td>';
                $html .= '        </tr>';
            }
        }

        $cAmt   = (float)($buckets['current']['amount'] ?? 0);
        $d30Amt = (float)($buckets['days_30']['amount'] ?? 0);
        $d60Amt = (float)($buckets['days_60']['amount'] ?? 0);
        $d90Amt = (float)($buckets['days_90']['amount'] ?? 0);
        $d120Amt= (float)($buckets['days_120']['amount'] ?? 0);

        $html .= '      </tbody>';
        $html .= '      <tfoot class="border-t-2 border-slate-900 bg-slate-100/90 font-bold text-xs">';
        $html .= '        <tr>';
        $html .= '          <td class="px-4 py-3.5 font-extrabold text-slate-900 uppercase">Total Aging</td>';
        $html .= '          <td class="px-4 py-3.5 text-right font-mono text-emerald-800">' . format_rupiah($cAmt) . '<span class="block text-[10px] font-normal text-slate-500">' . ($buckets['current']['percent'] ?? 0) . '%</span></td>';
        $html .= '          <td class="px-4 py-3.5 text-right font-mono text-amber-800">' . format_rupiah($d30Amt) . '<span class="block text-[10px] font-normal text-slate-500">' . ($buckets['days_30']['percent'] ?? 0) . '%</span></td>';
        $html .= '          <td class="px-4 py-3.5 text-right font-mono text-orange-800">' . format_rupiah($d60Amt) . '<span class="block text-[10px] font-normal text-slate-500">' . ($buckets['days_60']['percent'] ?? 0) . '%</span></td>';
        $html .= '          <td class="px-4 py-3.5 text-right font-mono text-rose-800">' . format_rupiah($d90Amt) . '<span class="block text-[10px] font-normal text-slate-500">' . ($buckets['days_90']['percent'] ?? 0) . '%</span></td>';
        $html .= '          <td class="px-4 py-3.5 text-right font-mono text-red-900">' . format_rupiah($d120Amt) . '<span class="block text-[10px] font-normal text-slate-500">' . ($buckets['days_120']['percent'] ?? 0) . '%</span></td>';
        $html .= '          <td class="px-4 py-3.5 text-right font-mono font-black text-sm text-purple-950 bg-purple-100/60">' . format_rupiah($grandTotal) . '<span class="block text-[10px] font-normal text-purple-700">100%</span></td>';
        $html .= '        </tr>';
        $html .= '      </tfoot>';
        $html .= '    </table>';
        $html .= '  </div>';
        $html .= '</div>';

        return $html;
    }
}
