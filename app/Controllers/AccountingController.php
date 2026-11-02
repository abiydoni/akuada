<?php

namespace App\Controllers;

use App\Models\CoaModel;
use App\Models\JournalModel;

class AccountingController extends BaseController
{
    public function coa()
    {
        $coaModel = new CoaModel();
        $categoryFilter = $this->request->getGet('category');
        $accounts = $coaModel->getCoaWithBalances();

        if ($categoryFilter) {
            $accounts = array_values(array_filter($accounts, fn($a) => $a['account_category'] === $categoryFilter));
        }

        return view('admin/accounting/coa', [
            'title'          => 'Master Chart of Accounts (COA) - General Ledger AkuAda',
            'activeTab'      => 'coa',
            'accounts'       => $accounts,
            'categoryFilter' => $categoryFilter,
        ]);
    }

    public function storeCoa()
    {
        CoaModel::ensureAccountingInitialized();
        $code = strtoupper(trim((string)$this->request->getPost('account_code')));
        if (!preg_match('/^\d{3}-\d{3}$/', $code)) {
            session()->setFlashdata('error', 'Format Kode Akun COA wajib 3 digit - 3 digit (contoh: 111-004 atau 610-005).');
            return redirect()->back();
        }

        $coaModel = new CoaModel();
        if ($coaModel->where('account_code', $code)->first()) {
            session()->setFlashdata('error', 'Kode Akun COA ' . $code . ' sudah terdaftar. Gunakan kode unik lainnya.');
            return redirect()->back();
        }

        $category = $this->request->getPost('account_category') ?: 'ASSET';
        $reportPos = in_array($category, ['ASSET', 'LIABILITY', 'EQUITY']) ? 'NERACA' : 'LABA_RUGI';
        $normalBal = $this->request->getPost('normal_balance') ?: (in_array($category, ['ASSET', 'COGS', 'EXPENSE', 'OTHER_EXPENSE']) ? 'DEBIT' : 'CREDIT');

        $coaModel->insert([
            'account_code'        => $code,
            'account_name'        => trim((string)$this->request->getPost('account_name')),
            'account_category'    => $category,
            'account_group'       => trim((string)($this->request->getPost('account_group') ?: 'Umum')),
            'normal_balance'      => $normalBal,
            'report_position'     => $reportPos,
            'is_cash_bank'        => $this->request->getPost('is_cash_bank') ? 1 : 0,
            'bank_name'           => $this->request->getPost('bank_name') ?: null,
            'bank_account_number' => $this->request->getPost('bank_account_number') ?: null,
            'opening_balance'     => (float)($this->request->getPost('opening_balance') ?? 0),
            'is_system'           => 0,
            'is_active'           => 1,
            'description'         => trim((string)$this->request->getPost('description')),
        ]);

        session()->setFlashdata('success', 'Akun Master COA [' . $code . '] berhasil ditambahkan ke General Ledger!');
        return redirect()->to('/admin/accounting/coa');
    }

    public function updateCoa()
    {
        CoaModel::ensureAccountingInitialized();
        $id   = (int)$this->request->getPost('id');
        $code = strtoupper(trim((string)$this->request->getPost('account_code')));
        if (!preg_match('/^\d{3}-\d{3}$/', $code)) {
            session()->setFlashdata('error', 'Format Kode Akun COA wajib 3 digit - 3 digit (contoh: 111-001).');
            return redirect()->back();
        }

        $coaModel = new CoaModel();
        $existing = $coaModel->where('account_code', $code)->where('id !=', $id)->first();
        if ($existing) {
            session()->setFlashdata('error', 'Kode Akun COA ' . $code . ' sudah dipakai oleh akun lain.');
            return redirect()->back();
        }

        $category  = $this->request->getPost('account_category') ?: 'ASSET';
        $reportPos = in_array($category, ['ASSET', 'LIABILITY', 'EQUITY']) ? 'NERACA' : 'LABA_RUGI';

        $coaModel->update($id, [
            'account_code'        => $code,
            'account_name'        => trim((string)$this->request->getPost('account_name')),
            'account_category'    => $category,
            'account_group'       => trim((string)$this->request->getPost('account_group')),
            'normal_balance'      => $this->request->getPost('normal_balance') ?: 'DEBIT',
            'report_position'     => $reportPos,
            'is_cash_bank'        => $this->request->getPost('is_cash_bank') ? 1 : 0,
            'bank_name'           => $this->request->getPost('bank_name') ?: null,
            'bank_account_number' => $this->request->getPost('bank_account_number') ?: null,
            'opening_balance'     => (float)($this->request->getPost('opening_balance') ?? 0),
            'description'         => trim((string)$this->request->getPost('description')),
        ]);

        // Also update account_code in journal items if changed
        $db = \Config\Database::connect();
        $db->table('acc_journal_items')->where('coa_id', $id)->update(['account_code' => $code]);

        session()->setFlashdata('success', 'Data Akun Master COA [' . $code . '] berhasil diperbarui!');
        return redirect()->to('/admin/accounting/coa');
    }

    public function journals()
    {
        $journalModel = new JournalModel();
        $coaModel     = new CoaModel();

        $startDate = $this->request->getGet('start_date') ?: '';
        $endDate   = $this->request->getGet('end_date') ?: '';
        $type      = $this->request->getGet('type') ?: '';

        $journals = $journalModel->getJournalsWithItems($startDate ?: null, $endDate ?: null, $type ?: null);
        $coaList  = $coaModel->where('is_active', 1)->orderBy('account_code', 'ASC')->findAll();

        return view('admin/accounting/journals', [
            'title'      => 'Jurnal Umum (General Journal) - General Ledger AkuAda',
            'activeTab'  => 'journals',
            'journals'   => $journals,
            'coaList'    => $coaList,
            'startDate'  => $startDate,
            'endDate'    => $endDate,
            'typeFilter' => $type,
        ]);
    }

    public function storeJournal()
    {
        CoaModel::ensureAccountingInitialized();
        $db = \Config\Database::connect();

        $journalDate = $this->request->getPost('journal_date') ?: date('Y-m-d');
        $journalType = $this->request->getPost('journal_type') ?: 'GENERAL';
        $refNumber   = trim((string)$this->request->getPost('reference_number')) ?: ('REF-' . date('YmdHis'));
        $description = trim((string)$this->request->getPost('description'));

        $coaIds     = $this->request->getPost('coa_id') ?? [];
        $lineDescs  = $this->request->getPost('line_description') ?? [];
        $debits     = $this->request->getPost('debit') ?? [];
        $credits    = $this->request->getPost('credit') ?? [];

        $coaModel = new CoaModel();
        $allCoa   = $coaModel->findAll();
        $coaById  = [];
        foreach ($allCoa as $c) {
            $coaById[(int)$c['id']] = $c;
        }

        $validLines  = [];
        $totalDebit  = 0.0;
        $totalCredit = 0.0;

        foreach ($coaIds as $idx => $cid) {
            $cid = (int)$cid;
            $d   = round(max(0, (float)($debits[$idx] ?? 0)), 2);
            $c   = round(max(0, (float)($credits[$idx] ?? 0)), 2);

            if ($cid > 0 && isset($coaById[$cid]) && ($d > 0 || $c > 0)) {
                $totalDebit  += $d;
                $totalCredit += $c;
                $validLines[] = [
                    'coa_id'           => $cid,
                    'account_code'     => $coaById[$cid]['account_code'],
                    'line_description' => trim((string)($lineDescs[$idx] ?? '')) ?: $description,
                    'debit'            => $d,
                    'credit'           => $c,
                ];
            }
        }

        if (count($validLines) < 2) {
            session()->setFlashdata('error', 'Jurnal Umum minimal harus memiliki 2 baris akun (Debit dan Kredit).');
            return redirect()->back();
        }

        if (abs($totalDebit - $totalCredit) > 0.01) {
            session()->setFlashdata('error', 'Jurnal Tidak Seimbang (Out of Balance)! Total Debit (' . format_rupiah($totalDebit) . ') harus sama persis dengan Total Kredit (' . format_rupiah($totalCredit) . ').');
            return redirect()->back();
        }

        $now       = date('Y-m-d H:i:s');
        $jrnNumber = 'JRN/MAN/' . date('Ym') . '/' . rand(1000, 9999);

        $db->table('acc_journals')->insert([
            'journal_number'   => $jrnNumber,
            'journal_date'     => $journalDate,
            'journal_type'     => $journalType,
            'reference_type'   => 'manual',
            'reference_id'     => null,
            'reference_number' => $refNumber,
            'description'      => $description,
            'total_debit'      => round($totalDebit, 2),
            'total_credit'     => round($totalCredit, 2),
            'status'           => 'POSTED',
            'created_by'       => session('user_name') ?? 'Superadmin Finance',
            'created_at'       => $now,
            'updated_at'       => $now,
        ]);
        $journalId = (int)$db->insertID();

        foreach ($validLines as $vl) {
            $db->table('acc_journal_items')->insert(array_merge($vl, [
                'journal_id' => $journalId,
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }

        session()->setFlashdata('success', 'Jurnal Umum #' . $jrnNumber . ' senilai ' . format_rupiah($totalDebit) . ' berhasil diposting ke Buku Besar!');
        return redirect()->to('/admin/accounting/journals');
    }

    public function deleteJournal($id)
    {
        CoaModel::ensureAccountingInitialized();
        $db  = \Config\Database::connect();
        $jrn = $db->table('acc_journals')->where('id', (int)$id)->get()->getRowArray();

        if ($jrn) {
            if ($jrn['reference_type'] !== 'manual' && $jrn['reference_type'] !== 'cash_bank_manual') {
                session()->setFlashdata('error', 'Jurnal otomatis dari transaksi PO hanya dapat diubah melalui pembaruan PO/Pembayaran.');
                return redirect()->back();
            }
            $db->table('acc_journal_items')->where('journal_id', (int)$id)->delete();
            $db->table('acc_journals')->where('id', (int)$id)->delete();
            session()->setFlashdata('success', 'Jurnal #' . $jrn['journal_number'] . ' berhasil dihapus.');
        }

        return redirect()->back();
    }

    public function syncJournals()
    {
        CoaModel::ensureAccountingInitialized();
        CoaModel::syncOrdersToJournals();
        session()->setFlashdata('success', 'Seluruh transaksi PO, Piutang Buyer (AR), Hutang Vendor (AP), PPN, dan Komisi berhasil disinkronkan ke Jurnal Umum & Buku Besar!');
        return redirect()->back();
    }

    public function ledger()
    {
        CoaModel::ensureAccountingInitialized();
        $journalModel = new JournalModel();
        $coaModel     = new CoaModel();

        $coaList   = $coaModel->where('is_active', 1)->orderBy('account_code', 'ASC')->findAll();
        $coaId     = $this->request->getGet('coa_id') ? (int)$this->request->getGet('coa_id') : (int)($coaList[0]['id'] ?? 0);
        $startDate = $this->request->getGet('start_date') ?: '';
        $endDate   = $this->request->getGet('end_date') ?: '';

        $ledgers = $coaId > 0 ? $journalModel->getGeneralLedger($coaId, $startDate ?: null, $endDate ?: null) : [];

        return view('admin/accounting/ledger', [
            'title'      => 'Buku Besar (General Ledger) - Akuntansi AkuAda',
            'activeTab'  => 'ledger',
            'ledgers'    => $ledgers,
            'coaList'    => $coaList,
            'selectedId' => $coaId,
            'startDate'  => $startDate,
            'endDate'    => $endDate,
        ]);
    }

    public function cashBank()
    {
        $coaModel     = new CoaModel();
        $journalModel = new JournalModel();

        $allBalances  = $coaModel->getCoaWithBalances();
        $bankAccounts = array_values(array_filter($allBalances, fn($a) => (int)$a['is_cash_bank'] === 1));
        $otherCoa     = array_values(array_filter($allBalances, fn($a) => (int)$a['is_cash_bank'] === 0));

        $selectedBankId = $this->request->getGet('bank_coa_id')
            ? (int)$this->request->getGet('bank_coa_id')
            : (int)($bankAccounts[0]['id'] ?? 0);
        $startDate      = $this->request->getGet('start_date') ?: '';
        $endDate        = $this->request->getGet('end_date') ?: '';

        $bankLedgers = [];
        foreach ($bankAccounts as $ba) {
            if ((int)$ba['id'] !== $selectedBankId) {
                continue;
            }
            $lg = $journalModel->getGeneralLedger((int)$ba['id'], $startDate ?: null, $endDate ?: null);
            if (!empty($lg)) {
                $bankLedgers[] = $lg[0];
            }
        }

        return view('admin/accounting/cash_bank', [
            'title'          => 'Buku Kas & Bank (Cash & Bank Book) - Akuntansi AkuAda',
            'activeTab'      => 'cash-bank',
            'bankAccounts'   => $bankAccounts,
            'otherCoa'       => $otherCoa,
            'bankLedgers'    => $bankLedgers,
            'selectedBankId' => $selectedBankId,
            'startDate'      => $startDate,
            'endDate'        => $endDate,
        ]);
    }

    public function storeCashBankTransaction()
    {
        CoaModel::ensureAccountingInitialized();
        $db = \Config\Database::connect();

        $txType      = $this->request->getPost('tx_type') === 'CASH_IN' ? 'CASH_IN' : 'CASH_OUT';
        $bankCoaId   = (int)$this->request->getPost('bank_coa_id');
        $contraCoaId = (int)$this->request->getPost('contra_coa_id');
        $amount      = round(max(0, (float)$this->request->getPost('amount')), 2);
        $txDate      = $this->request->getPost('tx_date') ?: date('Y-m-d');
        $refNo       = trim((string)$this->request->getPost('reference_number')) ?: ('BNK/' . date('Ymd') . '/' . rand(100, 999));
        $desc        = trim((string)$this->request->getPost('description'));

        if ($bankCoaId <= 0 || $contraCoaId <= 0 || $amount <= 0) {
            session()->setFlashdata('error', 'Harap pilih Rekening Kas/Bank, Akun Lawan, dan nominal lebih dari Rp 0.');
            return redirect()->back();
        }

        $coaModel  = new CoaModel();
        $bankAcc   = $coaModel->find($bankCoaId);
        $contraAcc = $coaModel->find($contraCoaId);

        if (!$bankAcc || !$contraAcc) {
            session()->setFlashdata('error', 'Akun COA tidak valid.');
            return redirect()->back();
        }

        $now       = date('Y-m-d H:i:s');
        $jrnNumber = 'JRN/' . ($txType === 'CASH_IN' ? 'BM' : 'BK') . '/' . date('Ym') . '/' . rand(1000, 9999);

        $db->table('acc_journals')->insert([
            'journal_number'   => $jrnNumber,
            'journal_date'     => $txDate,
            'journal_type'     => $txType,
            'reference_type'   => 'cash_bank_manual',
            'reference_id'     => null,
            'reference_number' => $refNo,
            'description'      => $desc,
            'total_debit'      => $amount,
            'total_credit'     => $amount,
            'status'           => 'POSTED',
            'created_by'       => session('user_name') ?? 'Treasury Officer',
            'created_at'       => $now,
            'updated_at'       => $now,
        ]);
        $jid = (int)$db->insertID();

        if ($txType === 'CASH_IN') {
            // Debit Bank, Credit Contra Account
            $lines = [
                [
                    'journal_id'       => $jid,
                    'coa_id'           => (int)$bankAcc['id'],
                    'account_code'     => $bankAcc['account_code'],
                    'line_description' => 'Kas/Bank Masuk: ' . $desc,
                    'debit'            => $amount,
                    'credit'           => 0.00,
                    'created_at'       => $now,
                    'updated_at'       => $now,
                ],
                [
                    'journal_id'       => $jid,
                    'coa_id'           => (int)$contraAcc['id'],
                    'account_code'     => $contraAcc['account_code'],
                    'line_description' => $desc,
                    'debit'            => 0.00,
                    'credit'           => $amount,
                    'created_at'       => $now,
                    'updated_at'       => $now,
                ],
            ];
        } else {
            // Debit Contra Account, Credit Bank
            $lines = [
                [
                    'journal_id'       => $jid,
                    'coa_id'           => (int)$contraAcc['id'],
                    'account_code'     => $contraAcc['account_code'],
                    'line_description' => $desc,
                    'debit'            => $amount,
                    'credit'           => 0.00,
                    'created_at'       => $now,
                    'updated_at'       => $now,
                ],
                [
                    'journal_id'       => $jid,
                    'coa_id'           => (int)$bankAcc['id'],
                    'account_code'     => $bankAcc['account_code'],
                    'line_description' => 'Kas/Bank Keluar: ' . $desc,
                    'debit'            => 0.00,
                    'credit'           => $amount,
                    'created_at'       => $now,
                    'updated_at'       => $now,
                ],
            ];
        }

        $db->table('acc_journal_items')->insertBatch($lines);
        session()->setFlashdata('success', 'Transaksi Buku Bank #' . $jrnNumber . ' sebesar ' . format_rupiah($amount) . ' berhasil dicatat!');
        return redirect()->to('/admin/accounting/cash-bank');
    }

    public function trialBalance()
    {
        $coaModel  = new CoaModel();
        $startDate = $this->request->getGet('start_date') ?: '';
        $endDate   = $this->request->getGet('end_date') ?: '';

        $accounts = $coaModel->getCoaWithBalances($startDate ?: null, $endDate ?: null);

        return view('admin/accounting/trial_balance', [
            'title'     => 'Neraca Saldo (Trial Balance) - Laporan Keuangan AkuAda',
            'activeTab' => 'trial-balance',
            'accounts'  => $accounts,
            'startDate' => $startDate,
            'endDate'   => $endDate,
        ]);
    }

    public function incomeStatement()
    {
        $coaModel  = new CoaModel();
        $startDate = $this->request->getGet('start_date') ?: '';
        $endDate   = $this->request->getGet('end_date') ?: '';

        $accounts = $coaModel->getCoaWithBalances($startDate ?: null, $endDate ?: null);

        $revenues      = [];
        $cogs          = [];
        $expenses      = [];
        $otherIncomes  = [];
        $otherExpenses = [];

        $totalRevenue      = 0.0;
        $totalCogs         = 0.0;
        $totalExpense      = 0.0;
        $totalOtherIncome  = 0.0;
        $totalOtherExpense = 0.0;

        foreach ($accounts as $acc) {
            // For Income Statement over a period, if startDate is set we use period net movement; otherwise ending_balance
            $isDebit = ($acc['normal_balance'] === 'DEBIT');
            $val = $startDate
                ? ($isDebit ? ($acc['mutation_debit'] - $acc['mutation_credit']) : ($acc['mutation_credit'] - $acc['mutation_debit']))
                : (float)$acc['ending_balance'];

            // Contra-revenue (e.g., 413-001 Potongan & Retur Penjualan with DEBIT normal balance) reduces revenue
            if ($acc['account_category'] === 'REVENUE') {
                $signedVal = ($acc['normal_balance'] === 'CREDIT') ? $val : -$val;
                $acc['report_amount'] = $signedVal;
                $revenues[] = $acc;
                $totalRevenue += $signedVal;
            } elseif ($acc['account_category'] === 'COGS') {
                $acc['report_amount'] = $val;
                $cogs[] = $acc;
                $totalCogs += $val;
            } elseif ($acc['account_category'] === 'EXPENSE') {
                $acc['report_amount'] = $val;
                $expenses[] = $acc;
                $totalExpense += $val;
            } elseif ($acc['account_category'] === 'OTHER_INCOME') {
                $acc['report_amount'] = $val;
                $otherIncomes[] = $acc;
                $totalOtherIncome += $val;
            } elseif ($acc['account_category'] === 'OTHER_EXPENSE') {
                $acc['report_amount'] = $val;
                $otherExpenses[] = $acc;
                $totalOtherExpense += $val;
            }
        }

        $grossProfit     = $totalRevenue - $totalCogs;
        $operatingProfit = $grossProfit - $totalExpense;
        $netProfit       = $operatingProfit + $totalOtherIncome - $totalOtherExpense;

        return view('admin/accounting/income_statement', [
            'title'             => 'Laporan Laba Rugi (Income Statement) - Laporan Keuangan AkuAda',
            'activeTab'         => 'income-statement',
            'revenues'          => $revenues,
            'cogs'              => $cogs,
            'expenses'          => $expenses,
            'otherIncomes'      => $otherIncomes,
            'otherExpenses'     => $otherExpenses,
            'totalRevenue'      => $totalRevenue,
            'totalCogs'         => $totalCogs,
            'grossProfit'       => $grossProfit,
            'totalExpense'      => $totalExpense,
            'operatingProfit'   => $operatingProfit,
            'totalOtherIncome'  => $totalOtherIncome,
            'totalOtherExpense' => $totalOtherExpense,
            'netProfit'         => $netProfit,
            'startDate'         => $startDate,
            'endDate'           => $endDate,
        ]);
    }

    public function balanceSheet()
    {
        $coaModel = new CoaModel();
        $asOfDate = $this->request->getGet('as_of_date') ?: date('Y-m-d');

        $accounts = $coaModel->getCoaWithBalances(null, $asOfDate);

        $currentAssets     = [];
        $fixedAssets       = [];
        $shortLiabilities  = [];
        $longLiabilities   = [];
        $equities          = [];

        $totalCurrentAssets    = 0.0;
        $totalFixedAssets      = 0.0;
        $totalShortLiabilities = 0.0;
        $totalLongLiabilities  = 0.0;
        $totalEquityAccounts   = 0.0;

        // Calculate Current Year Net Profit up to $asOfDate to include in Equity
        $netProfitCurrentYear = 0.0;

        foreach ($accounts as $acc) {
            $cat = $acc['account_category'];
            $bal = (float)$acc['ending_balance'];

            if ($cat === 'ASSET') {
                // Contra-asset (e.g., 122-001 Akumulasi Penyusutan with CREDIT normal balance) reduces asset total
                $signedBal = ($acc['normal_balance'] === 'DEBIT') ? $bal : -$bal;
                $acc['report_amount'] = $signedBal;
                if (stripos($acc['account_group'], 'Tetap') !== false || str_starts_with($acc['account_code'], '12')) {
                    $fixedAssets[] = $acc;
                    $totalFixedAssets += $signedBal;
                } else {
                    $currentAssets[] = $acc;
                    $totalCurrentAssets += $signedBal;
                }
            } elseif ($cat === 'LIABILITY') {
                $signedBal = ($acc['normal_balance'] === 'CREDIT') ? $bal : -$bal;
                $acc['report_amount'] = $signedBal;
                if (stripos($acc['account_group'], 'Panjang') !== false || str_starts_with($acc['account_code'], '22')) {
                    $longLiabilities[] = $acc;
                    $totalLongLiabilities += $signedBal;
                } else {
                    $shortLiabilities[] = $acc;
                    $totalShortLiabilities += $signedBal;
                }
            } elseif ($cat === 'EQUITY') {
                $signedBal = ($acc['normal_balance'] === 'CREDIT') ? $bal : -$bal;
                $acc['report_amount'] = $signedBal;
                $equities[] = $acc;
                $totalEquityAccounts += $signedBal;
            } elseif ($cat === 'REVENUE') {
                $netProfitCurrentYear += (($acc['normal_balance'] === 'CREDIT') ? $bal : -$bal);
            } elseif (in_array($cat, ['COGS', 'EXPENSE', 'OTHER_EXPENSE'])) {
                $netProfitCurrentYear -= $bal;
            } elseif ($cat === 'OTHER_INCOME') {
                $netProfitCurrentYear += $bal;
            }
        }

        $totalAssets      = round($totalCurrentAssets + $totalFixedAssets, 2);
        $totalLiabilities = round($totalShortLiabilities + $totalLongLiabilities, 2);
        $totalEquity      = round($totalEquityAccounts + $netProfitCurrentYear, 2);
        $totalPasiva      = round($totalLiabilities + $totalEquity, 2);

        return view('admin/accounting/balance_sheet', [
            'title'                 => 'Laporan Neraca (Balance Sheet) - Laporan Keuangan AkuAda',
            'activeTab'             => 'balance-sheet',
            'asOfDate'              => $asOfDate,
            'currentAssets'         => $currentAssets,
            'fixedAssets'           => $fixedAssets,
            'shortLiabilities'      => $shortLiabilities,
            'longLiabilities'       => $longLiabilities,
            'equities'              => $equities,
            'totalCurrentAssets'    => $totalCurrentAssets,
            'totalFixedAssets'      => $totalFixedAssets,
            'totalAssets'           => $totalAssets,
            'totalShortLiabilities' => $totalShortLiabilities,
            'totalLongLiabilities'  => $totalLongLiabilities,
            'totalLiabilities'      => $totalLiabilities,
            'totalEquityAccounts'   => $totalEquityAccounts,
            'netProfitCurrentYear'  => $netProfitCurrentYear,
            'totalEquity'           => $totalEquity,
            'totalPasiva'           => $totalPasiva,
        ]);
    }

    public function cashFlow()
    {
        CoaModel::ensureAccountingInitialized();
        $db       = \Config\Database::connect();
        $coaModel = new CoaModel();

        $startDate = $this->request->getGet('start_date') ?: '';
        $endDate   = $this->request->getGet('end_date') ?: '';

        $allBalances  = $coaModel->getCoaWithBalances($startDate ?: null, $endDate ?: null);
        $bankAccounts = array_values(array_filter($allBalances, fn($a) => (int)$a['is_cash_bank'] === 1));
        $bankIds      = array_map(fn($a) => (int)$a['id'], $bankAccounts);

        $openingCash = array_sum(array_column($bankAccounts, 'effective_opening'));
        $endingCash  = array_sum(array_column($bankAccounts, 'ending_balance'));

        // Inspect all journal entries touching a cash/bank account in the period
        $operatingIn  = 0.0;
        $operatingOut = 0.0;
        $investingIn  = 0.0;
        $investingOut = 0.0;
        $financingIn  = 0.0;
        $financingOut = 0.0;
        $details      = [];

        if (!empty($bankIds)) {
            $q = $db->table('acc_journal_items ji')
                ->select('ji.*, j.journal_number, j.journal_date, j.journal_type, j.description as journal_description, c.account_name as bank_account_name')
                ->join('acc_journals j', 'j.id = ji.journal_id')
                ->join('acc_coa c', 'c.id = ji.coa_id')
                ->where('j.status', 'POSTED')
                ->whereIn('ji.coa_id', $bankIds);

            if ($startDate) {
                $q->where('j.journal_date >=', $startDate);
            }
            if ($endDate) {
                $q->where('j.journal_date <=', $endDate);
            }

            $cashItems = $q->orderBy('j.journal_date', 'ASC')->orderBy('j.id', 'ASC')->get()->getResultArray();

            foreach ($cashItems as $ci) {
                $in  = (float)$ci['debit'];
                $out = (float)$ci['credit'];

                // Check contra accounts in the same journal to classify Operating / Investing / Financing
                $contraRows = $db->table('acc_journal_items ji2')
                    ->select('c2.account_code, c2.account_name, c2.account_category, c2.account_group')
                    ->join('acc_coa c2', 'c2.id = ji2.coa_id')
                    ->where('ji2.journal_id', $ci['journal_id'])
                    ->whereNotIn('ji2.coa_id', $bankIds)
                    ->get()
                    ->getResultArray();

                $section = 'OPERATING';
                foreach ($contraRows as $cr) {
                    if ($cr['account_category'] === 'EQUITY' || str_starts_with($cr['account_code'], '22')) {
                        $section = 'FINANCING';
                        break;
                    }
                    if (str_starts_with($cr['account_code'], '12')) {
                        $section = 'INVESTING';
                        break;
                    }
                }

                if ($section === 'FINANCING') {
                    $financingIn  += $in;
                    $financingOut += $out;
                } elseif ($section === 'INVESTING') {
                    $investingIn  += $in;
                    $investingOut += $out;
                } else {
                    $operatingIn  += $in;
                    $operatingOut += $out;
                }

                $ci['flow_section'] = $section;
                $details[] = $ci;
            }
        }

        return view('admin/accounting/cash_flow', [
            'title'        => 'Laporan Arus Kas (Cash Flow Statement) - Laporan Keuangan AkuAda',
            'activeTab'    => 'cash-flow',
            'startDate'    => $startDate,
            'endDate'      => $endDate,
            'openingCash'  => $openingCash,
            'endingCash'   => $endingCash,
            'operatingIn'  => $operatingIn,
            'operatingOut' => $operatingOut,
            'investingIn'  => $investingIn,
            'investingOut' => $investingOut,
            'financingIn'  => $financingIn,
            'financingOut' => $financingOut,
            'details'      => $details,
        ]);
    }
}

