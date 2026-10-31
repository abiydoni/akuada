<?php

namespace App\Models;

use CodeIgniter\Model;

class JournalModel extends Model
{
    protected $table            = 'acc_journals';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'journal_number',
        'journal_date',
        'journal_type',
        'reference_type',
        'reference_id',
        'reference_number',
        'description',
        'total_debit',
        'total_credit',
        'status',
        'created_by',
        'created_at',
        'updated_at',
    ];
    protected $useTimestamps = true;

    public function getJournalsWithItems(?string $startDate = null, ?string $endDate = null, ?string $type = null): array
    {
        CoaModel::ensureAccountingInitialized();
        $db = \Config\Database::connect();

        $builder = $db->table('acc_journals j')
            ->select('j.*')
            ->where('j.status', 'POSTED');

        if ($startDate) {
            $builder->where('j.journal_date >=', $startDate);
        }
        if ($endDate) {
            $builder->where('j.journal_date <=', $endDate);
        }
        if ($type) {
            $builder->where('j.journal_type', $type);
        }

        $journals = $builder->orderBy('j.journal_date', 'DESC')
                            ->orderBy('j.id', 'DESC')
                            ->get()
                            ->getResultArray();

        if (empty($journals)) {
            return [];
        }

        $jIds = array_column($journals, 'id');
        $items = $db->table('acc_journal_items ji')
            ->select('ji.*, c.account_name, c.account_category, c.normal_balance')
            ->join('acc_coa c', 'c.id = ji.coa_id', 'left')
            ->whereIn('ji.journal_id', $jIds)
            ->orderBy('ji.id', 'ASC')
            ->get()
            ->getResultArray();

        $itemMap = [];
        foreach ($items as $it) {
            $itemMap[(int)$it['journal_id']][] = $it;
        }

        foreach ($journals as &$jrn) {
            $jrn['items'] = $itemMap[(int)$jrn['id']] ?? [];
        }
        unset($jrn);

        return $journals;
    }

    /**
     * Get General Ledger (Buku Besar) details per COA account with running balance.
     */
    public function getGeneralLedger(?int $coaId = null, ?string $startDate = null, ?string $endDate = null): array
    {
        CoaModel::ensureAccountingInitialized();
        $db       = \Config\Database::connect();
        $coaModel = new CoaModel();

        if ($coaId) {
            $acc = $coaModel->find($coaId);
            $accounts = $acc ? [$acc] : [];
        } else {
            $accounts = $coaModel->where('is_active', 1)->orderBy('account_code', 'ASC')->findAll();
        }

        $ledgers = [];

        foreach ($accounts as $acc) {
            $cid     = (int)$acc['id'];
            $isDebit = ($acc['normal_balance'] === 'DEBIT');
            $openBal = (float)($acc['opening_balance'] ?? 0);

            if ($startDate) {
                $prior = $db->table('acc_journal_items ji')
                    ->select('SUM(ji.debit) as sum_debit, SUM(ji.credit) as sum_credit')
                    ->join('acc_journals j', 'j.id = ji.journal_id')
                    ->where('j.status', 'POSTED')
                    ->where('ji.coa_id', $cid)
                    ->where('j.journal_date <', $startDate)
                    ->get()
                    ->getRowArray();

                $pDeb  = (float)($prior['sum_debit'] ?? 0);
                $pCred = (float)($prior['sum_credit'] ?? 0);
                $openBal = $isDebit ? ($openBal + $pDeb - $pCred) : ($openBal + $pCred - $pDeb);
            }

            $q = $db->table('acc_journal_items ji')
                ->select('ji.*, j.journal_number, j.journal_date, j.journal_type, j.reference_number, j.description as journal_description')
                ->join('acc_journals j', 'j.id = ji.journal_id')
                ->where('j.status', 'POSTED')
                ->where('ji.coa_id', $cid);

            if ($startDate) {
                $q->where('j.journal_date >=', $startDate);
            }
            if ($endDate) {
                $q->where('j.journal_date <=', $endDate);
            }

            $rows = $q->orderBy('j.journal_date', 'ASC')
                      ->orderBy('j.id', 'ASC')
                      ->orderBy('ji.id', 'ASC')
                      ->get()
                      ->getResultArray();

            $running = $openBal;
            $totDeb  = 0.0;
            $totCred = 0.0;

            foreach ($rows as &$r) {
                $d = (float)$r['debit'];
                $c = (float)$r['credit'];
                $totDeb  += $d;
                $totCred += $c;
                $running = $isDebit ? ($running + $d - $c) : ($running + $c - $d);
                $r['running_balance'] = round($running, 2);
            }
            unset($r);

            // Include account if specifically selected, or if it has opening balance / transactions
            if ($coaId || abs($openBal) > 0.001 || !empty($rows)) {
                $ledgers[] = [
                    'account'         => $acc,
                    'opening_balance' => round($openBal, 2),
                    'total_debit'     => round($totDeb, 2),
                    'total_credit'    => round($totCred, 2),
                    'ending_balance'  => round($running, 2),
                    'mutations'       => $rows,
                ];
            }
        }

        return $ledgers;
    }
}

