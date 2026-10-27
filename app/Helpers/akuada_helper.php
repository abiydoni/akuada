<?php

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
    function get_system_setting(string $key, $default = null)
    {
        static $cachedSettings = null;
        if ($cachedSettings === null) {
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
                // Completed step
                $circleBg = 'bg-emerald-600 text-white shadow-sm ring-4 ring-emerald-50';
                $borderLine = 'border-emerald-500';
                $textColor = 'text-slate-800 font-bold';
                $subColor = 'text-emerald-700 font-medium';
                $icon = 'check_circle';
            } elseif ($isCurrent) {
                // Active step with vibrant gradient Ungu/Biru/Pink pulse
                $circleBg = 'bg-gradient-to-tr from-purple-700 via-blue-600 to-pink-500 text-white shadow-lg shadow-purple-500/30 ring-4 ring-purple-100 animate-pulse';
                $borderLine = 'border-purple-600';
                $textColor = 'text-purple-950 font-black';
                $subColor = 'text-purple-700 font-bold';
                $icon = $s['icon'];
            } else {
                // Upcoming step
                $circleBg = 'bg-slate-100 text-slate-400 border border-slate-200';
                $borderLine = 'border-slate-200';
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
     * 
     * @param float $subtotal Item subtotal (DPP sebelum PPN)
     * @param float|null $customPercent Specific fee percent for product, or null to use system default
     * @return array [percent, fee_amount, net_amount]
     */
    function calculate_vendor_fee(float $subtotal, ?float $customPercent = null): array
    {
        $percent = ($customPercent !== null && $customPercent >= 0) ? $customPercent : get_default_vendor_fee_rate();
        $feeAmount = round($subtotal * ($percent / 100), 2);
        
        // Check minimum fee per transaction if configured
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

