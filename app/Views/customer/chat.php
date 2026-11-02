<?= $this->extend('layouts/customer') ?>

<?= $this->section('customer_content') ?>

<?= view('shared/chat_center', [
    'threads' => $threads,
    'vendors' => $vendors ?? [],
    'orders'  => $orders ?? [],
]) ?>

<?= $this->endSection() ?>
