<?= $this->extend('layouts/vendor') ?>

<?= $this->section('vendor_content') ?>

<?= view('shared/chat_center', [
    'threads' => $threads,
    'buyers'  => $buyers ?? [],
    'orders'  => $orders ?? [],
]) ?>

<?= $this->endSection() ?>
