<?= $this->extend('layouts/admin') ?>

<?= $this->section('admin_content') ?>

<?= view('shared/chat_center', [
    'threads'   => $threads,
    'companies' => $companies ?? [],
    'orders'    => $orders ?? [],
]) ?>

<?= $this->endSection() ?>
